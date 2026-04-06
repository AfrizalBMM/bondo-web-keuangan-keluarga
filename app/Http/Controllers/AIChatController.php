<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Budget;
use App\Models\Goal;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class AIChatController extends Controller
{
    /**
     * Tampilkan halaman chat AI Advisor.
     */
    public function index()
    {
        return Inertia::render('AIChat');
    }

    /**
     * Proses chat dengan Groq AI (Llama 3).
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array',
        ]);

        $user = $request->user();
        $apiKey = config('services.groq.key');
        $model = config('services.groq.model', 'llama-3.3-70b-versatile');

        if (!$apiKey) {
            Log::error('AIChat Error: GROQ_API_KEY is missing in config.');
            return response()->json([
                'error' => 'GROQ_API_KEY belum dikonfigurasi. Silakan dapatkan di console.groq.com dan tambahkan ke file .env Anda.'
            ], 500);
        }

        // 1. Generate Context Finansial yang Sangat Detail
        $context = $this->generateFinancialContext($user);

        // 2. Build Prompt (System Message - Financial Planner Mode)
        $systemMessage = "Anda adalah Bondo AI, konsultan keuangan pribadi paling cerdas dan teliti. "
            . "Tugas Anda adalah menganalisa data keuangan keluarga berikut secara tajam dan memberikan saran strategis dalam Bahasa Indonesia.\n\n"
            . "DATA KEUANGAN ANDA:\n"
            . $context . "\n\n"
            . "KEUNGGULAN & INSTRUKSI BARU ANDA:\n"
            . "1. ANALISA 'WHAT-IF' (Karakter Perencanaan):\n"
            . "   - Jika user bertanya rencana belanja besar ('Boleh saya beli HP 5jt?'), hitung 'Sisa Arus Kas Bebas' = (Saldo Total + Rata-rata Pendapatan - Rata-rata Pengeluaran - Target Menabung).\n"
            . "   - Berikan peringatan jika pembelian tersebut akan menguras saldo di bawah batas aman (misal: di bawah rata-rata pengeluaran bulanan).\n"
            . "2. PENCARIAN CERDAS (Natural Language Search):\n"
            . "   - Anda memiliki akses ke 'Daftar 50 Transaksi Terakhir'. Cari pola atau transaksi tertentu jika user bertanya ('Cari belanja di minimarket minggu lalu').\n"
            . "3. GAYA BAHASA:\n"
            . "   - Ramah tapi tetap profesional dan realistis. Jangan takut melarang jika data menunjukkan kondisi kurang aman.\n"
            . "   - Gunakan format list jika memberikan kategori atau langkah penghematan.";

        // 3. Prepare Groq API Data
        $messages = [
            ['role' => 'system', 'content' => $systemMessage]
        ];
        
        if ($request->history) {
            foreach ($request->history as $chat) {
                $messages[] = [
                    'role' => $chat['role'] === 'ai' ? 'assistant' : 'user',
                    'content' => $chat['content']
                ];
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $request->message
        ];

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post("https://api.groq.com/openai/v1/chat/completions", [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.6,
                    'max_tokens' => 1200,
                ]);

            if ($response->failed()) {
                Log::error('Groq API Error Response:', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['error' => 'Gagal menghubungi AI. Coba beberapa saat lagi.'], 500);
            }

            $result = $response->json();
            $aiResponse = $result['choices'][0]['message']['content'] ?? 'Maaf, saya sedang kesulitan memproses data Anda.';

            return response()->json(['response' => $aiResponse]);

        } catch (\Exception $e) {
            Log::error('AIChat Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Meringkas Data Keuangan Sangat Detail (Historis & Pencarian).
     */
    private function generateFinancialContext($user)
    {
        $familyId = $user->family_id;
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $startOfMonth = $now->copy()->startOfMonth();
        $startOf3Months = $now->copy()->subMonths(3)->startOfMonth();

        // 0. Info Waktu & Progress Bulan
        $totalDaysInMonth = $now->daysInMonth;
        $dayOfMonth = $now->day;
        $daysLeft = $totalDaysInMonth - $dayOfMonth;
        $timeInfo = "Tanggal: " . $now->translatedFormat('l, d F Y') . " (Sisa $daysLeft hari lagi di bulan ini)";

        // 1. Saldo & Rata-rata Historis (3 Bulan)
        $wallets = Wallet::where('family_id', $familyId)->get();
        $totalCurrentBalance = $wallets->sum('balance');
        $walletSummary = $wallets->map(fn($w) => "- " . $w->name . ": Rp " . number_format($w->balance, 0, ',', '.'))->implode("\n");

        $historicalIncome = Transaction::where('family_id', $familyId)->where('type', 'Income')->where('date', '>=', $startOf3Months)->sum('amount');
        $historicalExpense = Transaction::where('family_id', $familyId)->where('type', 'Expense')->where('date', '>=', $startOf3Months)->sum('amount');
        
        $avgMonthlyIncome = floatval($historicalIncome) / 3;
        $avgMonthlyExpense = floatval($historicalExpense) / 3;
        
        $avgIncomeStr = number_format($avgMonthlyIncome, 0, ',', '.');
        $avgExpenseStr = number_format($avgMonthlyExpense, 0, ',', '.');
        $totalBalanceStr = number_format($totalCurrentBalance, 0, ',', '.');

        // 2. Ringkasan Hari Ini
        $todayTxs = Transaction::where('family_id', $familyId)->whereDate('date', $today)->get();
        $todayIncome = number_format(floatval($todayTxs->where('type', 'Income')->sum('amount')), 0, ',', '.');
        $todayExpense = number_format(floatval($todayTxs->where('type', 'Expense')->sum('amount')), 0, ',', '.');

        // 3. Ringkasan Bulan Berjalan
        $monthExpenses = Transaction::where('family_id', $familyId)->where('type', 'Expense')->where('date', '>=', $startOfMonth)->with('category')->get();
        $totalMonthExpense = number_format(floatval($monthExpenses->sum('amount')), 0, ',', '.');

        // 4. JENDELA PENCARIAN (Extended Log - 50 Items)
        $latestTxs = Transaction::where('family_id', $familyId)
            ->with(['category', 'wallet'])
            ->orderBy('date', 'desc')->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
        
        $txLogs = $latestTxs->map(function ($t) {
            $typeSign = $t->type === 'Income' ? '(+)' : '(-)';
            $txDate = $t->date->format('d M');
            return "  [$txDate] $typeSign " . ($t->notes ?: $t->category->name) . ": Rp " . number_format($t->amount, 0, ',', '.') . " [" . $t->wallet->name . "]";
        })->implode("\n");

        // 5. Budget, Goals, & Debt
        $budgetSummary = Budget::where('family_id', $familyId)->with('category')->get()->map(function ($b) {
            $spent = floatval($b->spent()); 
            $limit = floatval($b->limit_amount);
            return "  * " . ($b->category->name ?? 'Kategori') . ": Rp " . number_format($spent, 0, ',', '.') . " / Rp " . number_format($limit, 0, ',', '.');
        })->implode("\n");

        $goalSummary = Goal::where('family_id', $familyId)->whereRaw('current_amount < target_amount')->get()
            ->map(fn($g) => "  * " . $g->name . ": Progres Rp " . number_format(floatval($g->current_amount), 0, ',', '.') . " dari Rp " . number_format(floatval($g->target_amount), 0, ',', '.'))->implode("\n");

        $debts = Debt::where('family_id', $familyId)->where('status', '!=', 'Lunas')->get();
        $totalDebtStr = number_format(floatval($debts->sum('total_amount') - $debts->sum('paid_amount')), 0, ',', '.');

        return <<<TEXT
[KONTEKS WAKTU]
$timeInfo

[STATISTIK HISTORIS (Rata-rata 3 Bulan Terakhir)]
- Rata-rata Pemasukan: Rp $avgIncomeStr / bln
- Rata-rata Pengeluaran: Rp $avgExpenseStr / bln
- Saldo Total Semua Dompet: Rp $totalBalanceStr

[RINGKASAN HARI INI]
- Masuk: Rp $todayIncome
- Keluar: Rp $todayExpense

[RINGKASAN BULAN INI]
Total Keluar: Rp $totalMonthExpense

[TARGET TABUNGAN & HUTANG]
$goalSummary
Total Hutang Belum Lunas: Rp $totalDebtStr

[STATUS BUDGET]
$budgetSummary

[DAFTAR 50 TRANSAKSI TERAKHIR (Untuk Fitur Cari)]
$txLogs
TEXT;
    }
}
