<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Bus;
use App\Jobs\NotifyAdminsOfJoinRequest;
use App\Models\Message;
use App\Models\User;
use App\Models\ChatRoom;
use App\Mail\UserRequestedToJoinChatRoom;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;

class NotifyAdminsOfJoinRequestTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_sends_an_email_to_all_admins()
    {
        Mail::fake();

        // Create a chat room
        $chatRoom = ChatRoom::factory()->create();

        // Create admin users
        $admin1 = User::factory()->create();
        $admin1->assignRole('Admin');

        $admin2 = User::factory()->create();
        $admin2->assignRole('Admin');

        // Dispatch the job
        dispatch(new NotifyAdminsOfJoinRequest($chatRoom));

        // Assert emails were sent to admins
        Mail::assertSent(UserRequestedToJoinChatRoom::class, function ($mail) use ($admin1) {
            return $mail->hasTo($admin1->email);
        });

        Mail::assertSent(UserRequestedToJoinChatRoom::class, function ($mail) use ($admin2) {
            return $mail->hasTo($admin2->email);
        });
    }


    #[Test]
    public function it_dispatches_the_notify_admins_of_join_request_job()
    {
        // Fake the job
        Bus::fake();

        // Create a chat room
        $chatRoom = ChatRoom::factory()->create();

        // Dispatch the job
        dispatch(new NotifyAdminsOfJoinRequest($chatRoom));

        // Assert the job was dispatched
        Bus::assertDispatched(NotifyAdminsOfJoinRequest::class);
    }
}
