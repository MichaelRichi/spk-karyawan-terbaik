<?php

namespace App\Providers;

use App\Services\PeriodeService;
use App\Services\SawService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * Daftarkan Services sebagai singleton agar tidak dibuat ulang di setiap inject.
     */
    public function register(): void
    {
        $this->app->singleton(SawService::class, fn() => new SawService());
        $this->app->singleton(PeriodeService::class, fn() => new PeriodeService());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gunakan Bootstrap 5 untuk pagination
        Paginator::useBootstrapFive();
    }
}