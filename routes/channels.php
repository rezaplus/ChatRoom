<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('chat-room.{roomId}', function (User $user, $roomId) {
    return $user->hasAccessToChatRoom($roomId);
});
