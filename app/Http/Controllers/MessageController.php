<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_room_id' => 'required|exists:chat_rooms,id',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();


        $message = Message::create([
            'user_id' => $request->user()->id,
            'chat_room_id' => $validated['chat_room_id'],
            'content' => $validated['content'],
        ]);

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }

    public function delete($id)
    {

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:messages,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $message = Message::find($id);

        // check if user is authorized to delete the message
        if (Gate::denies('delete', $message)) {
            throw new UnauthorizedHttpException('', 'You are not authorized to delete this message');
        }


        $message->delete();

        return response()->json(['message' => 'Message deleted successfully'], 200);
    }

    // get chat room messages
    public function viewMessages($id)
    {

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:chat_rooms,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $chatRoom = ChatRoom::find($id);

        $messages = $chatRoom->messages;

        return response()->json(['messages' => $messages], 200);
    }
}
