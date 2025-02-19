<?php

namespace Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Auth\AuthTestHelper;
use Tests\TestCase;
use Throwable;

class RefreshTokenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Throwable
     */
    public function test_can_refresh_token(): void
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
            ->postJson(route('auth.refresh'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'accessToken',
            ],
        ]);

        $accessToken = $response->decodeResponseJson()['data']['accessToken'];
        $this->assertTrue(AuthTestHelper::verifyAccessToken($accessToken));

        AuthTestHelper::clearUser($user);
    }

    /**
     * @throws Throwable
     */
    public function test_can_refresh_token_after_login(): void
    {
        $user = AuthTestHelper::mockUser();
        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertCookieNotExpired('refreshToken');
        $refreshToken = $response->getCookie('refreshToken', false)->getValue();
        $accessToken = $response->decodeResponseJson()['data']['accessToken'];

        // Manually make access token expired
        $moveTime = config('sanctum.expiration') + 5;
        $this->travel($moveTime)->minutes();
        $this->assertFalse(AuthTestHelper::verifyAccessToken($accessToken));

        $response = $this
            ->withUnencryptedCookie('refreshToken', $refreshToken)
            ->withCredentials()
            ->postJson(route('auth.refresh'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'accessToken',
            ],
        ]);

        $accessToken = $response->decodeResponseJson()['data']['accessToken'];
        $this->assertTrue(AuthTestHelper::verifyAccessToken($accessToken));

        AuthTestHelper::clearUser($user);
    }

    public function test_refresh_token_expiration(): void
    {
        $user = AuthTestHelper::mockUser();
        $tokens = AuthTestHelper::generateTokens($user);

        // Manually make access token expired
        $this->travel(config('sanctum.rt_expiration') + 5)->minutes();

        $response = $this
            ->withCredentials()
            ->withUnencryptedCookie('refreshToken', $tokens['refreshToken'])
            ->withHeader('Authorization', 'Bearer '.$tokens['accessToken'])
            ->postJson(route('auth.refresh'));

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
