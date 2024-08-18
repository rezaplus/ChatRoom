<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\ChatRoom;
use PHPUnit\Framework\Attributes\Test;

class RateLimitingTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $admin;
    protected $chatRoom;

    protected function setUp(): void
    {
        parent::setUp();

        // clear the rate limiter cache
        $this->artisan('cache:clear');

        // Create a regular user
        $this->user = User::factory()->create();
        $this->user->assignRole('User');

        // Create an admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        // Create a chat room for tests
        $this->chatRoom = ChatRoom::factory()->create();
    }

    #[Test]
    public function testMessageSendingRateLimit()
    {
        $this->actingAs($this->user, 'api');

        for ($i = 0; $i < 11; $i++) {
            $response = $this->postJson(route('messages.send'), ['content' => 'Hello', 'chat_room_id' => $this->chatRoom->id]);

            if ($i < 10) {
                $response->assertStatus(201); // Assuming success status for within limit
            } else {
                $response->assertStatus(429)
                    ->assertJson(['message' => 'You have exceeded the rate limit. Please wait and try again later.']);
            }
        }
    }

    #[Test]
    public function testChatRoomCreationRateLimit()
    {
        $this->actingAs($this->admin, 'api');

        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson(route('chatrooms.create'), ['name' => 'New Chat Room']);

            if ($i < 5) {
                $response->assertStatus(201); // Assuming success status for within limit
            } else {
                $response->assertStatus(429)
                    ->assertJson(['message' => 'You have exceeded the rate limit. Please wait and try again later.']);
            }
        }
    }

    #[Test]
    public function testChatRoomDeletionRateLimit()
    {
        $this->actingAs($this->admin, 'api');

        for ($i = 0; $i < 6; $i++) {
            $chatRoom = ChatRoom::factory()->create();
            $response = $this->deleteJson(route('chatrooms.delete', ['id' => $chatRoom->id]));

            if ($i < 5) {
                $response->assertStatus(200); // Assuming success status for within limit
            } else {
                $response->assertStatus(429)
                    ->assertJson(['message' => 'You have exceeded the rate limit. Please wait and try again later.']);
            }
        }
    }

    #[Test]
    public function testJoinRequestRateLimit()
    {
        $this->actingAs($this->user, 'api');

        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson(route('chatrooms.requestJoin'), ['chat_room_id' => $this->chatRoom->id]);

            if ($i < 5) {
                // if response is 400 retrun true or status is 200 return true else return false
                if ($response->status() == 400) {
                    $response->assertStatus(400);
                } else {
                    $response->assertStatus(200);
                }
            } else {
                $response->assertStatus(429);
            }
        }
    }
}
