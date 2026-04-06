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
        
        // Month and Year filtering (defaults to current)
        $selectedMonth = (int) $request->query('month', Carbon::now()->month);
        $selectedYear = (int) $request->query('year', Carbon::now()->year);
        
        $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        // Generate a list of months from January of the current year up to the current month
        $now = Carbon::now();
        $startOfYear = $now->copy()->startOfYear();
        $monthCursor = $startOfYear->copy();
        
        $monthList = [];
        while ($monthCursor->lessThanOrEqualTo($now)) {
            $monthList[$monthCursor->format('Y-m')] = [
                'name' => $monthCursor->translatedFormat('F'),
                'short' => $monthCursor->translatedFormat('M'),
                'year' => $monthCursor->year,
                'month' => $monthCursor->month,
                'label' => $monthCursor->translatedFormat('M y')
            ];
            $monthCursor->addMonth();
        }

        // Also fetch any historical months (outside current year range) that have transaction data
        $historicalMonths = Transaction::where('family_id', $familyId)
            ->where('date', '<', $startOfYear)
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as ym")
            ->distinct()
            ->orderBy('ym', 'asc')
            ->pluck('ym');

        foreach ($historicalMonths as $ym) {
            if (!isset($monthList[$ym])) {
                $date = Carbon::createFromFormat('Y-m', $ym);
                $monthList[$ym] = [
                    'name' => $date->translatedFormat('F'),
                    'short' => $date->translatedFormat('M'),
                    'year' => $date->year,
                    'month' => $date->month,
                    'label' => $date->translatedFormat('M y')
                ];
            }
        }

        // Sort chronologically and convert to indexed array
        ksort($monthList);
        $monthList = array_values($monthList);

        // Fallback (if no data and current month somehow isn't there)
        if (empty($monthList)) {
            $monthList[] = [
                'name' => $now->translatedFormat('F'),
                'short' => $now->translatedFormat('M'),
                'year' => $now->year,
                'month' => $now->month,
                'label' => $now->translatedFormat('M y')
            ];
        }

        // Metrics for the selected month
        $summary = Transaction::where('family_id', $familyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw("SUM(CASE WHEN type = 'Income' THEN amount ELSE 0 END) as total_income")
            ->selectRaw("SUM(CASE WHEN type = 'Expense' THEN amount ELSE 0 END) as total_expense")
            ->first();

        $totalIncome = (float) ($summary->total_income ?? 0);
        $totalExpense = (float) ($summary->total_expense ?? 0);
        $savingsAmount = $totalIncome - $totalExpense;
        $savingsRate = $totalIncome > 0 ? ($savingsAmount / $totalIncome) * 100 : 0;

        $largestTransaction = Transaction::where('family_id', $familyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('type', 'Expense')
            ->with(['category', 'wallet'])
            ->orderBy('amount', 'desc')
            ->first();

        // 6-Month Trend Data (Contextual: Leads up to selectedMonth)
        $trendStart = $startDate->copy()->subMonths(5)->startOfMonth();
        $trendMonths = Transaction::where('family_id', $familyId)
            ->whereBetween('date', [$trendStart, $endDate])
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month")
            ->selectRaw("SUM(CASE WHEN type = 'Income' THEN amount ELSE 0 END) as income")
            ->selectRaw("SUM(CASE WHEN type = 'Expense' THEN amount ELSE 0 END) as expense")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $trendLabels = [];
        $incomeTrend = [];
        $expenseTrend = [];

        for ($i = 5; $i >= 0; $i--) {
            $m = $startDate->copy()->subMonths($i);
            $key = $m->format('Y-m');
            $trendLabels[] = $m->translatedFormat('M y');
            $incomeTrend[] = (float) ($trendMonths->get($key)->income ?? 0);
            $expenseTrend[] = (float) ($trendMonths->get($key)->expense ?? 0);
        }

        // Category Summary (Actual vs Budget)
        $expensesByCategory = Transaction::where('family_id', $familyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('type', 'Expense')
            ->selectRaw('category_id, sum(amount) as actual')
            ->groupBy('category_id')
            ->pluck('actual', 'category_id');

        $budgets = \App\Models\Budget::where('family_id', $familyId)
            ->pluck('limit_amount', 'category_id');

        $categories = \App\Models\Category::all();
        $categorySummary = [];

        foreach ($categories as $cat) {
            $actual = (float) ($expensesByCategory[$cat->id] ?? 0);
            $limit = (float) ($budgets[$cat->id] ?? 0);
            
            if ($actual > 0 || $limit > 0) {
                $categorySummary[] = [
                    'name' => $cat->name,
                    'icon' => $cat->icon,
                    'color' => $cat->color,
                    'actual' => $actual,
                    'limit' => $limit,
                    'percentage' => $limit > 0 ? min(round(($actual / $limit) * 100, 1), 100) : ($actual > 0 ? 100 : 0),
                    'is_over' => $limit > 0 && $actual > $limit,
                    'remaining' => $limit > 0 ? max($limit - $actual, 0) : 0
                ];
            }
        }

        // Sort by highest spending
        usort($categorySummary, fn($a, $b) => $b['actual'] <=> $a['actual']);

        $catLabels = collect($categorySummary)->take(7)->map(fn($c) => $c['name']);
        $catData = collect($categorySummary)->take(7)->map(fn($c) => $c['actual']);
        $catColors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#64748b'];

        return Inertia::render('Reports', [
            'metrics' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'savingsAmount' => $savingsAmount,
                'savingsRate' => round($savingsRate, 1),
                'largestTransaction' => $largestTransaction ? [
                    'name' => $largestTransaction->text ?: ($largestTransaction->category->name ?? 'Pengeluaran'),
                    'amount' => (float) $largestTransaction->amount,
                    'date' => $largestTransaction->date->translatedFormat('d M Y'),
                    'wallet' => $largestTransaction->wallet->name ?? '-'
                ] : null
            ],
            'monthList' => $monthList,
            'categorySummary' => $categorySummary,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'activeMonthLabel' => $startDate->translatedFormat('F Y'),
            'trendChartData' => [
                'labels' => $trendLabels,
                'datasets' => [
                    [
                        'label' => 'Pemasukan',
                        'data' => $incomeTrend,
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true,
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'Pengeluaran',
                        'data' => $expenseTrend,
                        'borderColor' => '#f43f5e',
                        'backgroundColor' => 'rgba(244, 63, 94, 0.1)',
                        'fill' => true,
                        'tension' => 0.4
                    ]
                ]
            ],
            'categoryChartData' => [
                'labels' => $catLabels->isEmpty() ? [] : $catLabels,
                'datasets' => [[
                    'label' => 'Total Pengeluaran',
                    'data' => $catData->isEmpty() ? [] : $catData,
                    'backgroundColor' => array_slice($catColors, 0, $catData->count() ?: 1),
                    'borderWidth' => 0,
                    'hoverOffset' => 10
                ]]
            ]
        ]);
    }

    public function exportPdf(Request $request)
    {
        $familyId = $request->user()->family_id;
        $selectedMonth = $request->query('month', Carbon::now()->month);
        $selectedYear = $request->query('year', Carbon::now()->year);
        
        $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $transactions = Transaction::where('family_id', $familyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['category', 'wallet'])
            ->orderBy('date', 'asc')
            ->get();

        $summary = [
            'total_income' => $transactions->where('type', 'Income')->sum('amount'),
            'total_expense' => $transactions->where('type', 'Expense')->sum('amount'),
            'period' => $startDate->translatedFormat('F Y'),
            'date_range' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y')
        ];

        $pdf = Pdf::loadView('pdf.report', compact('transactions', 'summary'));
        
        return $pdf->download("Laporan_{$summary['period']}.pdf");
    }

}
