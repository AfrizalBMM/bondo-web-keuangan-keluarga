<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $familyId = $request->user()->family_id;
        $filter = $request->query('filter', 'Bulan Ini');
        $now = Carbon::now();

        // 1. Logika Filter Tanggal (Tetap sama)
        switch($filter) {
            case '7 Hari Terakhir':
                $startDate = $now->copy()->subDays(7);
                $endDate = $now;
                break;
            case '30 Hari Terakhir':
                $startDate = $now->copy()->subDays(30);
                $endDate = $now;
                break;
            case 'Tahun Ini':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
        }

        // 2. Metrics (Optimasi: Gabungkan query Expense & Income jika perlu, tapi ini sudah cukup oke)
        $totalDays = max($startDate->diffInDays($endDate), 1);
        
        $summary = Transaction::where('family_id', $familyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw("SUM(CASE WHEN type = 'Expense' THEN amount ELSE 0 END) as total_expense")
            ->selectRaw("SUM(CASE WHEN type = 'Income' THEN amount ELSE 0 END) as total_income")
            ->first();

        $totalExpense = $summary->total_expense ?? 0;
        $totalIncome = $summary->total_income ?? 0;
        $avgDailyExpense = $totalExpense / $totalDays;
        $cashflowDifference = $totalIncome - $totalExpense;

        $largestTransaction = Transaction::where('family_id', $familyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('type', 'Expense')
            ->with('category')
            ->orderBy('amount', 'desc')
            ->first();

        // 3. OPTIMASI TREND CHART (Tanpa Loop Query)
        $sixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();
        
        // Ambil semua data 6 bulan dalam SATU query
        $monthlyData = Transaction::where('family_id', $familyId)
            ->where('date', '>=', $sixMonthsAgo)
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month")
            ->selectRaw("SUM(CASE WHEN type = 'Income' THEN amount ELSE 0 END) as income")
            ->selectRaw("SUM(CASE WHEN type = 'Expense' THEN amount ELSE 0 END) as expense")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month'); // Mudahkan akses via key 'YYYY-MM'

        $trendLabels = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthKey = $now->copy()->subMonths($i)->format('Y-m');
            $monthLabel = $now->copy()->subMonths($i)->translatedFormat('M y');
            
            $trendLabels[] = $monthLabel;
            // Ambil dari collection $monthlyData, jika tidak ada isi 0
            $incomeData[] = $monthlyData->get($monthKey)->income ?? 0;
            $expenseData[] = $monthlyData->get($monthKey)->expense ?? 0;
        }

        // 4. Bar Chart: Expense by Category (Tetap sama, sudah efisien)
        $expensesByCategory = Transaction::where('family_id', $familyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('type', 'Expense')
            ->with('category')
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->get();

        // Mapping chart data... (Logika warna dan label Anda sudah bagus)
        $catLabels = $expensesByCategory->map(fn($exp) => $exp->category->name ?? 'Lainnya');
        $catData = $expensesByCategory->pluck('total');
        $catColors = ['#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#10b981', '#ec4899', '#14b8a6'];

        return Inertia::render('Reports', [
            'metrics' => [
                'avgDailyExpense' => $avgDailyExpense,
                'cashflowDifference' => $cashflowDifference,
                'largestTransaction' => [
                    'name' => $largestTransaction ? ($largestTransaction->notes ?: ($largestTransaction->category->name ?? 'Pengeluaran')) : '-',
                    'amount' => $largestTransaction ? $largestTransaction->amount : 0,
                    'date' => $largestTransaction ? $largestTransaction->date->format('d M Y') : '-'
                ]
            ],
            'trendChartData' => [
                'labels' => $trendLabels,
                'datasets' => [
                    [
                        'label' => 'Pemasukan',
                        'data' => $incomeData,
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true, 'tension' => 0.4
                    ],
                    [
                        'label' => 'Pengeluaran',
                        'data' => $expenseData,
                        'borderColor' => '#f43f5e',
                        'backgroundColor' => 'rgba(244, 63, 94, 0.1)',
                        'fill' => true, 'tension' => 0.4
                    ]
                ]
            ],
            'categoryChartData' => [
                'labels' => $catLabels->isEmpty() ? ['Belum Ada Data'] : $catLabels,
                'datasets' => [[
                    'label' => 'Total Pengeluaran',
                    'data' => $catData->isEmpty() ? [0] : $catData,
                    'backgroundColor' => array_slice($catColors, 0, $catData->count() ?: 1),
                    'borderRadius' => 4
                ]]
            ],
            'currentFilter' => $filter
        ]);
    }

    public function exportPdf(Request $request)
    {
        $familyId = $request->user()->family_id;
        $filter = $request->query('filter', 'Bulan Ini'); // Tangkap filter dari URL
        $now = Carbon::now();

        // Gunakan logika switch-case yang sama dengan method index() Anda
        switch($filter) {
            case '7 Hari Terakhir':
                $startDate = $now->copy()->subDays(7);
                $endDate = $now;
                break;
            case '30 Hari Terakhir':
                $startDate = $now->copy()->subDays(30);
                $endDate = $now;
                break;
            case 'Tahun Ini':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
        }

        $transactions = Transaction::where('family_id', $familyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['category', 'wallet'])
            ->orderBy('date', 'asc')
            ->get();

        $summary = [
            'total_income' => $transactions->where('type', 'Income')->sum('amount'),
            'total_expense' => $transactions->where('type', 'Expense')->sum('amount'),
            'period' => $filter,
            'date_range' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y')
        ];

        $pdf = Pdf::loadView('pdf.report', compact('transactions', 'summary'));
        
        return $pdf->download("Laporan_{$filter}_{$now->format('Ymd')}.pdf");
    }

}
