<?php

namespace App\Providers;

use App\Models\User;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Overload authorization method from \Laravel\Horizon\HorizonApplicationServiceProvider
     * to allow access to Horizon without having a logged in user.
     */
    protected function authorization(): void
    {
        Horizon::auth(function ($request) {
            return true;
        });
    }
}
