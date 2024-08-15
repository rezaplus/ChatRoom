<?php

namespace Tests\Feature;

use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ChatRoomTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user and a regular user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
        $this->user = User::factory()->create();
        $this->user->assignRole('User');
    }

    #[Test]
    public function admin_can_create_chat_room()
    {
        $response = $this->actingAs($this->admin, 'api')->postJson('/api/chat-rooms', [
            'name' => 'Test Chat Room',
            'description' => 'A room for testing',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Chat room created successfully',
                'chat_room' => [
                    'name' => 'Test Chat Room',
                    'description' => 'A room for testing',
                ]
            ]);

        $this->assertDatabaseHas('chat_rooms', [
            'name' => 'Test Chat Room',
            'description' => 'A room for testing',
        ]);
    }

    #[Test]
    public function admin_can_delete_chat_room()
    {
        $chatRoom = ChatRoom::factory()->create();

        $response = $this->actingAs($this->admin, 'api')->deleteJson("/api/chat-rooms/{$chatRoom->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Chat room deleted successfully']);

        $this->assertDatabaseMissing('chat_rooms', [
            'id' => $chatRoom->id,
        ]);
    }

    #[Test]
    public function user_can_view_chat_rooms()
    {
        $chatRoom = ChatRoom::factory()->create();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/chat-rooms');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $chatRoom->name,
                'description' => $chatRoom->description,
            ]);
    }

    #[Test]
    public function user_can_join_chat_room()
    {
        $chatRoom = ChatRoom::factory()->create();

        $response = $this->actingAs($this->user, 'api')->postJson("/api/chat-rooms/request-join", [
            'chat_room_id' => $chatRoom->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Request to join chat room sent']);

        $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $chatRoom->id,
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function admin_can_approve_or_reject_join_request()
    {
        // Create a chat room and add the user to it
        $chatRoom = ChatRoom::factory()->create();
        $chatRoom->users()->attach($this->user->id, ['status' => 'pending']);

        $response = $this->actingAs($this->admin, 'api')->postJson("/api/chat-rooms/approve-reject-join-request", [
            'chat_room_id' => $chatRoom->id,
            'user_id' => $this->user->id,
            'status' => 'approved',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'User request updated successfully']);

        $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $chatRoom->id,
            'user_id' => $this->user->id,
            'status' => 'approved',
        ]);
    }
}
