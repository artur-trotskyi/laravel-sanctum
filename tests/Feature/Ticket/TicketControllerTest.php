<?php

namespace Feature\Ticket;

use App\Enums\Limit\RateLimiterEnum;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\Feature\Auth\AuthTestHelper;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $this->user = AuthTestHelper::mockUser();
        $this->tokens = AuthTestHelper::generateTokens($this->user);

        AuthTestHelper::mockUser(3);
        AuthTestHelper::mockTicket(20);
    }

    public function test_can_retrieve_tickets(): void
    {
        Ticket::factory()->count(10)->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->getJson(route('tickets.index', ['page' => 1, 'per_page' => 5]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'items' => [
                    '*' => [
                        'id',
                        'userId',
                        'title',
                        'description',
                        'status',
                        'createdAt',
                        'updatedAt',
                    ],
                ],
                'meta' => [
                    'currentPage',
                    'lastPage',
                    'perPage',
                    'total',
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
            ],
            'message',
        ]);

        $response->assertJsonFragment([
            'success' => true,
            'message' => 'Tickets retrieved successfully.',
        ]);
    }

    public function test_can_filter_tickets(): void
    {
        Ticket::factory()->create(['title' => 'Test Title', 'status' => 'closed', 'deleted_at' => null]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->getJson(route('tickets.index', ['title' => 'Test', 'status' => 'closed']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.items');
    }

    public function test_can_sort_tickets(): void
    {
        Ticket::factory()->create(['title' => 'A      AA', 'deleted_at' => null]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->getJson(route('tickets.index', ['sort_by' => 'title', 'sort_order' => 'asc']));

        $response->assertStatus(200)
            ->assertJsonPath('data.items.0.title', 'A      AA');
    }

    public function test_cannot_access_tickets_without_authentication(): void
    {
        $response = $this->getJson(route('tickets.index'));

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
    }

    public function test_too_many_requests(): void
    {
        $rateLimit = RateLimiterEnum::API_USER_PER_MINUTE->value * 2;
        for ($i = 0; $i < $rateLimit; $i++) {
            $this->withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$this->tokens['accessToken'],
            ])->getJson(route('tickets.index'));
        }

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->getJson(route('tickets.index'));

        $response->assertStatus(429);

        $response->assertJsonFragment([
            'errors' => [
                [
                    'message' => 'Too Many Attempts.',
                    'source' => '',
                    'status' => 429,
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

    public function test_can_create_ticket(): void
    {
        $ticketData = [
            'title' => 'Test Title',
            'description' => 'Test Description',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->postJson(route('tickets.store'), $ticketData);

        $response->assertStatus(201);

        $response->assertJsonFragment([
            'message' => 'Ticket created successfully.',
            'success' => true,
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'userId',
                'title',
                'description',
                'status',
                'createdAt',
                'updatedAt',
            ],
            'success',
            'message',
        ]);

        $this->assertDatabaseHas('tickets', $ticketData);
    }

    public function test_cannot_create_ticket_if_not_authenticated(): void
    {
        $ticketData = [
            'title' => 'Test Title',
            'description' => 'Test Description',
        ];

        $response = $this->postJson(route('tickets.store'), $ticketData);

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
    }

    public function test_can_view_own_ticket(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->getJson(route('tickets.show', $ticket->id));

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'message' => 'Ticket retrieved successfully.',
            'success' => true,
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'userId',
                'title',
                'description',
                'status',
                'createdAt',
                'updatedAt',
            ],
            'success',
            'message',
        ]);
    }

    public function test_cannot_view_other_users_ticket(): void
    {
        $anotherUser = AuthTestHelper::mockUser();
        $ticket = Ticket::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->getJson(route('tickets.show', $ticket->id));

        $response->assertStatus(403);

        $response->assertJsonFragment([
            'errors' => [
                [
                    'message' => 'This action is unauthorized.',
                    'source' => '',
                    'status' => 403,
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

    public function test_cannot_view_ticket_if_not_authenticated(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->getJson(route('tickets.show', $ticket->id));

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
    }

    public function test_can_update_own_ticket(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->user->id]);

        $updatedData = [
            'title' => 'TestTitle',
            'description' => 'TestDescription',
            'status' => 'closed',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->putJson(route('tickets.update', $ticket->id), $updatedData);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'message' => 'Ticket updated successfully.',
            'success' => true,
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'userId',
                'title',
                'description',
                'status',
                'createdAt',
                'updatedAt',
            ],
            'success',
            'message',
        ]);

        $this->assertDatabaseHas('tickets', array_merge(['id' => $ticket->id], $updatedData));
    }

    public function test_cannot_update_other_users_ticket(): void
    {
        $anotherUser = AuthTestHelper::mockUser();
        $ticket = Ticket::factory()->create(['user_id' => $anotherUser->id]);

        $updatedData = [
            'title' => 'TestTitle',
            'description' => 'TestDescription',
            'status' => 'closed',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->putJson(route('tickets.update', $ticket->id), $updatedData);

        $response->assertStatus(403);

        $response->assertJsonFragment([
            'errors' => [
                [
                    'message' => 'This action is unauthorized.',
                    'source' => '',
                    'status' => 403,
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

    public function test_cannot_update_ticket_if_not_authenticated(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->user->id]);

        $updatedData = [
            'title' => 'TestTitle',
            'description' => 'TestDescription',
            'status' => 'closed',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->putJson(route('tickets.update', $ticket->id), $updatedData);

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
    }

    public function test_can_delete_own_ticket(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->deleteJson(route('tickets.destroy', $ticket->id));

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'message' => 'Ticket deleted successfully.',
            'success' => true,
            'data' => [],
        ]);

        $response->assertJsonStructure([
            'message',
            'success',
            'data',
        ]);

        $ticket = Ticket::withTrashed()->find($ticket->id);
        $this->assertNotNull($ticket->deleted_at);
    }

    public function test_cannot_delete_other_users_ticket(): void
    {
        $anotherUser = AuthTestHelper::mockUser();
        $ticket = Ticket::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->deleteJson(route('tickets.destroy', $ticket->id));

        $response->assertStatus(403);

        $response->assertJsonFragment([
            'errors' => [
                [
                    'message' => 'This action is unauthorized.',
                    'source' => '',
                    'status' => 403,
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

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }

    public function test_cannot_delete_ticket_if_not_authenticated(): void
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->deleteJson(route('tickets.destroy', $ticket->id));

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
    }

    public function test_can_retrieve_cached_tickets_and_then_refresh(): void
    {
        // Arrange: Create tickets and simulate a cache hit.
        Ticket::factory()->count(10)->create(['title' => 'First Title']);

        // First request to get the tickets (this will be cached).
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->getJson(route('tickets.index', ['page' => 1, 'per_page' => 5, 'sort_order' => 'asc', 'sort_by' => 'created_at']));

        // Assert that the request was successful and we received the expected number of items.
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.items');

        // Assert that the response contains the old title from the cached tickets.
        $response->assertJsonFragment([
            'title' => 'First Title',
        ]);

        // Now, make a change to the database (update all tickets).
        Ticket::query()->update(['title' => 'Second Title']);

        // Second request — the cache should be used, and the data should not reflect the update yet.
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->getJson(route('tickets.index', ['page' => 1, 'per_page' => 5, 'sort_order' => 'asc', 'sort_by' => 'created_at']));

        // Assert that the cached data is still being used and the data hasn't changed.
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.items');

        // Ensure that the response still contains the old (cached) title.
        $response->assertJsonFragment([
            'title' => 'First Title',
        ]);

        // Simulate cache expiration or refresh.
        // You can either manually adjust the TTL or simulate cache clearing.
        Cache::tags(['tickets'])->flush(); // Clear the cache.

        // Make the same request again — now the cache should be missed, and the updated data should be returned.
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->tokens['accessToken'],
        ])->getJson(route('tickets.index', ['page' => 1, 'per_page' => 5, 'sort_order' => 'asc', 'sort_by' => 'created_at']));

        // Assert that the updated data is now returned.
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.items');

        // Ensure the response contains the new title.
        $response->assertJsonFragment([
            'title' => 'Second Title',
        ]);
    }
}
