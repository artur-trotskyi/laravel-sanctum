<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

        AuthTestHelper::clearUser($user);
    }
}
