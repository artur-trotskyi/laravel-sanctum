<?php

namespace Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_successful(): void
    {
        $requestAttributes = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson(route('auth.register'), $requestAttributes);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
        ]);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'createdAt',
                    'updatedAt',
                ],
                'accessToken',
            ],
        ]);
    }

    public function test_register_with_invalid_data(): void
    {
        $requestAttributes = [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'pass',
            'password_confirmation' => 'different-pass',
        ];

        $response = $this->postJson(route('auth.register'), $requestAttributes);

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'errors' => [
                [
                    'message' => 'The name field is required.',
                    'source' => 'name',
                    'status' => 422,
                ],
                [
                    'message' => 'The email field must be a valid email address.',
                    'source' => 'email',
                    'status' => 422,
                ],
                [
                    'message' => 'The password field must be at least 6 characters.',
                    'source' => 'password',
                    'status' => 422,
                ],
                [
                    'message' => 'The password field confirmation does not match.',
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

    public function test_register_with_existing_email(): void
    {
        User::factory()->create([
            'email' => 'johndoe@example.com',
        ]);

        $requestAttributes = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson(route('auth.register'), $requestAttributes);

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'errors' => [
                [
                    'message' => 'The email has already been taken.',
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
}
