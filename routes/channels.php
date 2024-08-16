<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat-room.{roomId}', function (User $user, $roomId) {
    return $user->chatRooms->contains($roomId);
});
