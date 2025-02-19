<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Throwable;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_login_without_required_fields(): void
    {
        $response = $this->postJson(route('auth.login'));
        $response->assertStatus(422);

        $response->assertJsonFragment([
            'errors' => [
                [
                    'message' => 'The email field is required.',
                    'source' => 'email',
                    'status' => 422,
                ],
                [
                    'message' => 'The password field is required.',
                    'source' => 'password',
                    'status' => 422,
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
    }

    public function test_cannot_login_with_wrong_password(): void
    {
        $user = AuthTestHelper::mockUser();

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'incorrect',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'errors' => [
                [
                    'message' => 'The password is incorrect.',
                    'source' => 'password',
                    'status' => 422,
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
    }

    public function test_cannot_login_with_wrong_email(): void
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => 'unexists@mail.example',
            'password' => 'incorrect',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'errors' => [
                [
                    'message' => 'The selected email is invalid.',
                    'source' => 'email',
                    'status' => 422,
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
    }

    /**
     * @throws Throwable
     */
    public function test_can_login(): void
    {
        $user = AuthTestHelper::mockUser();
        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertCookieNotExpired(
            'refreshToken'
        );

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'accessToken',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'emailVerifiedAt',
                    'createdAt',
                    'updatedAt',
                ],
            ],
        ]);

        $accessToken = $response->decodeResponseJson()['data']['accessToken'];
        $this->assertTrue(AuthTestHelper::verifyAccessToken($accessToken));

        AuthTestHelper::clearUser($user);
    }
}
