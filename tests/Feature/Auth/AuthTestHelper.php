<?php

namespace Tests\Feature\Auth;

use App\Models\User;

class AuthTestHelper
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
}
