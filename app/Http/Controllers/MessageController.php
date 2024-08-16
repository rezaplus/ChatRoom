<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\ChatRoom;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'chat_room_id' => 'required|exists:chat_rooms,id',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'user_id' => $request->user()->id,
            'chat_room_id' => $validated['chat_room_id'],
            'content' => $validated['content'],
        ]);

        // Broadcast the message
        broadcast(new MessageSent($message));

        return response()->json($message, 201);
    }

    public function delete(Message $message)
    {

        // check message exists
        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        $message->delete();
        return response()->json(['message' => 'Message deleted successfully'], 200);
    }

    // get chat room messages
    public function viewMessages($id)
    {

        $chatRoom = ChatRoom::find($id);

        if (!$chatRoom) {
            return response()->json(['message' => 'Chat room not found'], 404);
        }

        $messages = $chatRoom->messages;

        return response()->json(['messages' => $messages], 200);
    }
}
