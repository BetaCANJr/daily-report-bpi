<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\DailyReport;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define gates untuk authorization daily reports
        Gate::define('update-report', function (User $user, DailyReport $report) {
            return $user->isAdmin() || $user->id === $report->user_id;
        });

        Gate::define('delete-report', function (User $user, DailyReport $report) {
            return $user->isAdmin() || $user->id === $report->user_id;
        });

        Gate::define('view-report', function (User $user, DailyReport $report) {
            // Semua user bisa view semua laporan
            return true;
        });
    }
}