<?php

namespace App\Providers;

use App\Enums\Limit\RateLimiterEnum;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(RateLimiterEnum::API_USER_PER_MINUTE->value)->by($request->user()->id)
                : Limit::perMinute(RateLimiterEnum::API_IP_PER_MINUTE->value)->by($request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(RateLimiterEnum::AUTH_IP_PER_MINUTE->value)->by($request->ip());
        });
    }
}
