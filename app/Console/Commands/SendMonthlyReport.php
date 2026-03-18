<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendMonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-monthly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        // Loop semua keluarga yang ada
        $families = \App\Models\Family::all();

        foreach ($families as $family) {
            $expense = \App\Models\Transaction::where('family_id', $family->id)
                ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
                ->where('type', 'Expense')->sum('amount');
                
            $income = \App\Models\Transaction::where('family_id', $family->id)
                ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
                ->where('type', 'Income')->sum('amount');

            // Kirim ke semua anggota keluarga tersebut
            $users = $family->users;
            \Illuminate\Support\Facades\Notification::send($users, new MonthlyReportNotification([
                'expense' => $expense,
                'diff' => $income - $expense
            ]));
        }
    }
}
