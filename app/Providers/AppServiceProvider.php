<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Define Gates for role-based access
        Gate::define('owner-access', function (User $user) {
            return $user->isOwner();
        });

        Gate::define('finance-access', function (User $user) {
            return $user->isOwner() || $user->isFinance();
        });

        Gate::define('manage-settings', function (User $user) {
            return $user->isOwner();
        });

        Gate::define('manage-clients', function (User $user) {
            return $user->isOwner() || $user->isFinance();
        });

        Gate::define('manage-invoices', function (User $user) {
            return $user->isOwner() || $user->isFinance();
        });

        Gate::define('manage-finance', function (User $user) {
            return $user->isOwner() || $user->isFinance();
        });

        Gate::define('manage-employees', function (User $user) {
            return $user->isOwner() || $user->isFinance() || $user->isHr();
        });

        Gate::define('manage-payslips', function (User $user) {
            return $user->isOwner() || $user->isFinance() || $user->isHr();
        });
    }
}
