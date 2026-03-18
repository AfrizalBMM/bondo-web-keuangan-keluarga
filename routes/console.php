<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan laporan setiap tanggal 1 jam 06:00 pagi
Schedule::command('reports:send-monthly')->monthlyOn(1, '06:00');
