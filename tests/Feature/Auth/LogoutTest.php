<?php

namespace Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\Feature\Auth\AuthTestHelper;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_logout(): void
    {
        $user = AuthTestHelper::mockUser();
        $tokens = AuthTestHelper::generateTokens($user);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->withUnencryptedCookie('refreshToken', $tokens['refreshToken'])
            ->withCredentials()
            ->withHeader('Authorization', 'Bearer '.$tokens['accessToken'])
            ->postJson(route('auth.logout'));
        $response->assertStatus(200);

        $response->assertCookie('refreshToken', '', false);
        $this->assertEquals(0, PersonalAccessToken::where('tokenable_id', $user->id)->count());

        $response->assertJson([
            'success' => true,
            'message' => 'You are logged out.',
            'data' => [],
        ]);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

        AuthTestHelper::clearUser($user);
    }

    public function test_can_logout_with_expired_session(): void
    {
        $user = AuthTestHelper::mockUser();
        $tokens = AuthTestHelper::generateTokens($user);

        // Manually make access token expired
        $moveTime = config('sanctum.expiration') + 5;
        $this->travel($moveTime)->minutes();
        $this->assertFalse(AuthTestHelper::verifyAccessToken($tokens['accessToken']));

        $response = $this
            ->withUnencryptedCookie('refreshToken', $tokens['refreshToken'])
            ->withCredentials()
            ->withHeader('Authorization', 'Bearer '.$tokens['accessToken'])
            ->postJson(route('auth.logout'));

        $response->assertStatus(401);

        $response->assertJsonFragment([
            'errors' => [
                [
                    'message' => 'Unauthenticated.',
                    'source' => '',
                    'status' => 401,
                ],
            ],
        ]);

        $response->assertJsonStructure([
            'errors' => [
                '*' => [
                    'status',
                    'message',
                    'source',
                ],
            ],
        ]);

        AuthTestHelper::clearUser($user);
    }
}
