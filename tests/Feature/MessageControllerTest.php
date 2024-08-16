<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\User;
use App\Models\ChatRoom;
use App\Events\MessageSent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;

class MessageControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $chatRoom;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and chat room for the tests
        $this->user = User::factory()->create();
        $this->user->assignRole('User');
        $this->chatRoom = ChatRoom::factory()->create();
    }

    #[Test]
    public function it_sends_a_message_and_dispatches_an_event()
    {
        // Arrange
        $messageContent = 'Hello, World!';
        $this->actingAs($this->user, 'api');

        // Mock the event dispatching
        Event::fake();

        // Act
        $response = $this->postJson('/api/messages', [
            'chat_room_id' => $this->chatRoom->id,
            'content' => $messageContent
        ]);

        // Assert
        $response->assertStatus(201);
        $response->assertJson([
            'user_id' => $this->user->id,
            'chat_room_id' => $this->chatRoom->id,
            'content' => $messageContent
        ]);

        // Assert that the message was created in the database
        $this->assertDatabaseHas('messages', [
            'user_id' => $this->user->id,
            'chat_room_id' => $this->chatRoom->id,
            'content' => $messageContent
        ]);

        // Assert that the MessageSent event was dispatched
        Event::assertDispatched(MessageSent::class, function ($event) use ($messageContent) {
            return $event->message->content === $messageContent;
        });
    }
}
