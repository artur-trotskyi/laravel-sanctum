<?php

namespace App\Services;

use App\Enums\Auth\TokenAbilityEnum;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Cookie\CookieJar;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\Cookie;

class AuthService
{
    /**
     * Generate access and refresh tokens for the authenticated user.
     *
     * @param  User|Authenticatable  $user  The authenticated user instance.
     * @return array{
     *     accessToken: string,
     *     refreshToken: string,
     * }
     */
    public function generateTokens(User|Authenticatable $user): array
    {
        $atExpireTime = now()->addMinutes(config('sanctum.expiration'));
        $rtExpireTime = now()->addMinutes(config('sanctum.rt_expiration'));

        $accessToken = $user->createToken('access_token', [TokenAbilityEnum::ACCESS_API], $atExpireTime);
        $refreshToken = $user->createToken('refresh_token', [TokenAbilityEnum::ISSUE_ACCESS_TOKEN], $rtExpireTime);

        return [
            'accessToken' => $accessToken->plainTextToken,
            'refreshToken' => $refreshToken->plainTextToken,
        ];
    }

    /**
     * Generates a secure refresh token cookie.
     */
    public function generateRefreshTokenCookie(string $refreshToken, ?int $rtExpireTime = null): Application|CookieJar|Cookie
    {
        if (! $rtExpireTime) {
            $rtExpireTime = config('sanctum.rt_expiration');
        }

        return cookie('refreshToken', $refreshToken, $rtExpireTime, secure: config('app.is_production'));
    }
}
