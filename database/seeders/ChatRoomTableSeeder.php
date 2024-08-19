<?php

namespace Database\Seeders;

use App\Models\ChatRoom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatRoomTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chatroom = ChatRoom::factory()->create();

        $chatroom->users()->attach([1, 2]);
    }
}
