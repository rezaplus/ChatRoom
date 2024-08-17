<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRequestedToJoinChatRoom;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotifyAdminsOfJoinRequest implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $chatRoom;

    public function __construct(ChatRoom $chatRoom)
    {
        Log::info('NotifyAdminsOfJoinRequest job created');
        $this->chatRoom = $chatRoom;
    }

    public function handle()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'Admin');
        })->get();


        // if there are no admins, return
        if ($admins->isEmpty()) {
            return;
        }

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new UserRequestedToJoinChatRoom($this->chatRoom));
        }
    }
}
