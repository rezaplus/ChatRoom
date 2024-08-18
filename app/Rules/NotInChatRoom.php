<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\ChatRoom;

class NotInChatRoom implements ValidationRule
{
    protected $chatRoomId;

    // Pass the chat room ID to the rule constructor
    public function __construct($chatRoomId)
    {
        $this->chatRoomId = $chatRoomId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the user is already in the chat room
        $isInChatRoom = ChatRoom::where('id', $this->chatRoomId)
            ->whereHas('users', function ($query) use ($value) {
                $query->where('user_id', $value);
            })
            ->exists();

        // If the user is already in the chat room, fail the validation
        if ($isInChatRoom) {
            $fail('The user is already in the chat room.');
        }
    }
}
