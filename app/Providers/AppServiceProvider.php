<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Fix for `php artisan serve` failing on Windows due to missing environment variables
        if (class_exists(\Illuminate\Foundation\Console\ServeCommand::class)) {
            \Illuminate\Foundation\Console\ServeCommand::$passthroughVariables[] = 'SystemRoot';
            \Illuminate\Foundation\Console\ServeCommand::$passthroughVariables[] = 'TEMP';
            \Illuminate\Foundation\Console\ServeCommand::$passthroughVariables[] = 'TMP';
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
