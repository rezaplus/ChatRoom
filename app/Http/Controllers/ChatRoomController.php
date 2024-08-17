<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyAdminsOfJoinRequest;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ChatRoomController extends Controller
{
    // Create a new chat room
    public function createChatRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $chatRoom = new ChatRoom();
        $chatRoom->name = $request->name;
        $chatRoom->description = $request->description;
        $chatRoom->save();

        // Clear chat rooms cache
        Cache::forget('chat_rooms');

        return response()->json(['message' => 'Chat room created successfully', 'chat_room' => $chatRoom], 201);
    }

    // Delete a chat room
    public function deleteChatRoom($id)
    {
        $chatRoom = ChatRoom::find($id);

        if (!$chatRoom) {
            return response()->json(['message' => 'Chat room not found'], 404);
        }

        $chatRoom->delete();

        // Clear chat rooms cache
        Cache::forget('chat_rooms');

        return response()->json(['message' => 'Chat room deleted successfully'], 200);
    }

    // View all chat rooms
    public function viewChatRooms()
    {
        $chatRooms = cache()->remember('chat_rooms', 60, function () {
            return ChatRoom::all();
        });

        return response()->json(['chat_rooms' => $chatRooms], 200);
    }

    // request to join a chat room - pending status
    public function joinChatRoom(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'chat_room_id' => 'required|exists:chat_rooms,id',
            'user_id' => 'required|exists:users,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        
        $user = User::find($request->user_id);
        
        $chatRoom = ChatRoom::find($request->chat_room_id);
    
        dispatch(new NotifyAdminsOfJoinRequest($chatRoom));

        if ($chatRoom->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'User request already sent or user is already in chat room'], 400);
        }

        $chatRoom->users()->attach($user->id, ['status' => 'pending']);


        return response()->json(['message' => 'Request to join chat room sent'], 200);
    }

    // approve or reject a user's request to join a chat room
    public function approveOrRejectJoinRequest(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'user_id' => 'required|exists:users,id',
            'chat_room_id' => 'required|exists:chat_rooms,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $status = $request->status;
        $userId = $request->user_id;
        $id = $request->chat_room_id;

        $chatRoom = ChatRoom::find($id);

        $user = $chatRoom->users()->where('user_id', $userId)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found in chat room'], 404);
        }

        $user->pivot->status = $status;
        $user->pivot->save();

        // Clear chat rooms cache
        Cache::forget('chat_rooms');

        return response()->json(['message' => 'User request updated successfully'], 200);
    }

    // get chat room details
    public function viewChatRoom($id)
    {
        $chatRoom = ChatRoom::find($id);

        if (!$chatRoom) {
            return response()->json(['message' => 'Chat room not found'], 404);
        }

        // TODO: Paginate messages
        $messages = $chatRoom->messages;

        return response()->json(['chat_room' => $chatRoom, 'messages' => $messages], 200);
    }

}
