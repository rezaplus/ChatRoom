<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    /**
     * Determine if the given message can be deleted by the user.
     */
    public function delete(User $user, Message $message)
    {
        // if user has delete any message permission
        if ($user->hasPermission('delete any message')) {
            return true;
        }

        // if user has delete own message permission
        return $user->id === $message->user_id && $user->hasPermission('delete own messages');
    }
}
