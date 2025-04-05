<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Guest;
use App\Models\TimeLog;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => 'App\Policies\UserPolicy',
        Vehicle::class => 'App\Policies\VehiclePolicy',
        Guest::class => 'App\Policies\GuestPolicy',
        TimeLog::class => 'App\Policies\TimeLogPolicy',
    ];
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
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });

    }
}
