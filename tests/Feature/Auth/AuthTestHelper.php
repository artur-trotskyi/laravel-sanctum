<?php

namespace Tests\Feature\Auth;

use App\Enums\Auth\TokenAbilityEnum;
use App\Models\User;
use App\Services\AuthService;
use Laravel\Sanctum\PersonalAccessToken;

readonly class AuthTestHelper
{
    public static function mockUser(): User
    {
        return User::factory()->create();
    }

    public static function clearUser(User $userModel): void
    {
        $userModel->tokens()->delete();
        cookie()->forget('refreshToken');
        $userModel->delete();
    }

    public static function verifyAccessToken(string $accessToken): bool
    {
        $tokenInDb = PersonalAccessToken::findToken($accessToken);

        return $tokenInDb && $tokenInDb->expires_at->isFuture() && $tokenInDb->can(TokenAbilityEnum::ACCESS_API->value);
    }

    public static function generateTokens(User $user): array
    {
        return app(AuthService::class)->generateTokens($user);
    }
}
