<?php

namespace App\Providers;

use App\Enums\Auth\TokenAbilityEnum;
use App\Models\PersonalAccessToken;
use App\Models\Ticket;
use App\Policies\TicketPolicy;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
        $this->overrideSanctumConfigurationToSupportRefreshToken();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        RedirectIfAuthenticated::redirectUsing(fn () => throw new AccessDeniedHttpException('You are already authenticated.'));

        Gate::policy(Ticket::class, TicketPolicy::class);
    }

    private function overrideSanctumConfigurationToSupportRefreshToken(): void
    {
        Sanctum::$accessTokenAuthenticationCallback = function ($accessToken, $isValid) {
            $abilities = collect($accessToken->abilities);
            if (! empty($abilities) && $abilities[0] === TokenAbilityEnum::ISSUE_ACCESS_TOKEN->value) {
                return $accessToken->expires_at && $accessToken->expires_at->isFuture();
            }

            return $isValid;
        };

        Sanctum::$accessTokenRetrievalCallback = function ($request) {
            if (! $request->routeIs('auth.refresh')) {
                return str_replace('Bearer ', '', $request->headers->get('Authorization'));
            }

            return $request->cookie('refreshToken') ?? '';
        };
    }
}
