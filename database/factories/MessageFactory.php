<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Message;
use App\Models\ChatRoom;
use App\Models\User;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            'content' => $this->faker->text,
            'created_at' => now(),
            'updated_at' => now(),
            'chat_room_id' => ChatRoom::factory(),
            'user_id' => User::factory(),
        ];
    }
}
