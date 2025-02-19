<?php

namespace Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Auth\AuthTestHelper;
use Tests\TestCase;
use Throwable;

class AccessTokenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Throwable
     */
    public function test_access_token_expiration(): void
    {
        $user = AuthTestHelper::mockUser();
        $tokens = AuthTestHelper::generateTokens($user);

        // Manually make access token expired
        $this->travel(config('sanctum.expiration') + 10)->minutes();

        $response = $this->withHeader('Authorization', 'Bearer '.$tokens['accessToken'])
            ->postJson(route('auth.me'));

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
