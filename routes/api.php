<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('refresh', function () {
    return response()->json(['token' => auth()->refresh()]);
});