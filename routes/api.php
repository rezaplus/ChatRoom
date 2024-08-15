<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatRoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');


Route::post('refresh', function () {
    return response()->json(['token' => auth()->refresh()]);
});


Route::group(['middleware' => ['auth:api', 'permission:view chat rooms']], function () {
    
});


Route::middleware(['auth:api'])->group(function () {
    Route::post('/chat-rooms', [ChatRoomController::class, 'createChatRoom'])->middleware('permission:create chat room');
    Route::delete('/chat-rooms/{id}', [ChatRoomController::class, 'deleteChatRoom'])->middleware('permission:delete chat room');
    Route::get('/chat-rooms', [ChatRoomController::class, 'viewChatRooms'])->middleware('permission:view chat rooms');
    Route::post('/chat-rooms/request-join', [ChatRoomController::class, 'joinChatRoom'])->middleware('permission:request join chat room');
    Route::post('/chat-rooms/approve-reject-join-request', [ChatRoomController::class, 'approveOrRejectJoinRequest'])->middleware('permission:approve or reject join requests');
});