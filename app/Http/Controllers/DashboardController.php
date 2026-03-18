<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $familyId = $user->family_id;

        // 1. Ambil Nama Keluarga & Kode Undangan (Eager Loading via User)
        $family = $user->family;
        $familyData = [
            'name' => $family->name ?? 'Keluarga',
            'inviteCode' => $family->invite_code ?? '-',
        ];

        // 2. Metrics Finansial (Bulan Berjalan)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Total Saldo (Semua Wallet)
        $saldoTersedia = (int) Wallet::where('family_id', $familyId)->sum('balance');

        // Cashflow Masuk & Keluar Bulan Ini
        $cashflow = Transaction::where('family_id', $familyId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw("SUM(CASE WHEN type = 'Income' THEN amount ELSE 0 END) as masuk")
            ->selectRaw("SUM(CASE WHEN type = 'Expense' THEN amount ELSE 0 END) as keluar")
            ->first();

        // 3. Data Doughnut Chart (Pengeluaran per Kategori)
        $expensesByCategory = Transaction::where('family_id', $familyId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('type', 'Expense')
            ->with('category:id,name')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();

        $palette = ['#3b82f6', '#f59e0b', '#ef4444', '#10b981', '#8b5cf6', '#ec4899', '#14b8a6'];
        
        $doughnutData = null;
        if ($expensesByCategory->isNotEmpty()) {
            $doughnutData = [
                'labels' => $expensesByCategory->map(fn($item) => $item->category->name ?? 'Lainnya'),
                'datasets' => [[
                    'backgroundColor' => array_slice($palette, 0, $expensesByCategory->count()),
                    'data' => $expensesByCategory->pluck('total')->map(fn($val) => (int) $val),
                ]]
            ];
        }

        // 4. Trend Arus Kas 7 Hari Terakhir (Optimized: Single Query)
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();
        $dailyTrend = Transaction::where('family_id', $familyId)
            ->where('date', '>=', $sevenDaysAgo)
            ->select(
                DB::raw('DATE(date) as day'),
                DB::raw("SUM(CASE WHEN type = 'Income' THEN amount ELSE -amount END) as net")
            )
            ->groupBy('day')
            ->get()
            ->pluck('net', 'day'); // Format: ['2026-03-19' => 50000]

        $lineLabels = [];
        $lineValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $dateObj = Carbon::now()->subDays($i);
            $dateString = $dateObj->format('Y-m-d');
            
            $lineLabels[] = $dateObj->translatedFormat('D');
            $lineValues[] = (int) ($dailyTrend[$dateString] ?? 0);
        }

        $lineData = [
            'labels' => $lineLabels,
            'datasets' => [[
                'label' => 'Net Cashflow',
                'borderColor' => '#3b82f6',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'data' => $lineValues,
                'fill' => true,
                'tension' => 0.4
            ]]
        ];

        // 5. Aktivitas Terkini (5 Transaksi Terakhir)
        $recentActivities = Transaction::where('family_id', $familyId)
            ->with(['wallet:id,name', 'category:id,name'])
            ->latest('date')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn($trx) => [
                'id' => $trx->id,
                'text' => $trx->notes ?? ($trx->category->name ?? 'Transaksi'),
                'category' => $trx->category->name ?? '-',
                'wallet' => $trx->wallet->name ?? '-',
                'amount' => $trx->type === 'Expense' ? -(int)$trx->amount : (int)$trx->amount,
                'date' => $trx->date->translatedFormat('d M, H:i'),
                'type' => $trx->type
            ]);

        return Inertia::render('Dashboard', [
            'metrics' => [
                'saldoTersedia' => $saldoTersedia,
                'posiseBersih' => $saldoTersedia, // Placeholder jika belum ada modul Debt
                'cashflowMasuk' => (int) ($cashflow->masuk ?? 0),
                'cashflowKeluar' => (int) ($cashflow->keluar ?? 0),
            ],
            'familyData' => $familyData,
            'doughnutData' => $doughnutData,
            'lineData' => $lineData,
            'recentActivities' => $recentActivities,
        ]);
    }
}