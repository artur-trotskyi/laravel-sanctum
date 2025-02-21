<?php

namespace Tests\Feature\Auth;

use App\Enums\Auth\TokenAbilityEnum;
use App\Models\Ticket;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken;

readonly class AuthTestHelper
{
    public static function mockUser(?int $count = null): Model|Collection
    {
        return User::factory($count)->create();
    }

    public static function mockTicket(?int $count = null): Model|Collection
    {
        return Ticket::factory($count)->create();
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
