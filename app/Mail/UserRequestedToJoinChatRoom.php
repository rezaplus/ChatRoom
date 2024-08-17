<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatRoom;

class UserRequestedToJoinChatRoom extends Mailable
{
    use Queueable, SerializesModels;

    public $chatRoom;

    public function __construct(ChatRoom $chatRoom)
    {
        $this->chatRoom = $chatRoom;
    }

    public function build()
    {
        return $this->subject('User Requested to Join Chat Room')
            ->view('emails.user-requested-to-join-chat-room');
    }
}
