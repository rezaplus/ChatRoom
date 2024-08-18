<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::post('register', [AuthController::class, 'register'])->name('auth.register');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('auth.logout');

// Token refresh route
Route::post('refresh', function () {
    return response()->json(['token' => auth()->refresh()]);
})->name('auth.refresh');

// Chat Room routes
Route::middleware(['auth:api', 'permission:view chat rooms'])->group(function () {
    Route::post('/chat-rooms', [ChatRoomController::class, 'createChatRoom'])->middleware(['permission:create chat room', 'throttle:5,1'])->name('chatrooms.create');
    Route::delete('/chat-rooms/{id}', [ChatRoomController::class, 'deleteChatRoom'])->middleware(['permission:delete chat room', 'throttle:5,1'])->name('chatrooms.delete');
    Route::get('/chat-rooms', [ChatRoomController::class, 'viewChatRooms'])->middleware('permission:view chat rooms')->name('chatrooms.index');
    Route::post('/chat-rooms/request-join', [ChatRoomController::class, 'joinChatRoom'])->middleware(['permission:request join chat room', 'throttle:5,1'])->name('chatrooms.requestJoin');
    Route::post('/chat-rooms/approve-reject-join-request', [ChatRoomController::class, 'approveOrRejectJoinRequest'])->middleware('permission:approve or reject join requests')->name('chatrooms.approveRejectRequest');
    Route::get('/chat-rooms/{id}', [ChatRoomController::class, 'viewChatRoom'])->middleware('permission:view chat rooms')->name('chatrooms.view');
});

// Message routes
Route::middleware(['auth:api'])->group(function () {
    Route::get('/messages/{id}', [MessageController::class, 'viewMessages'])->middleware('permission:view chat rooms')->name('messages.index');
    Route::post('/messages', [MessageController::class, 'send'])->middleware(['permission:send messages', 'throttle:10,1'])->name('messages.send');
    Route::delete('/messages/{id}', [MessageController::class, 'delete'])->middleware(['can:delete,messages'])->name('messages.delete');
});
