<?php

use App\Http\Controllers\AuthController;
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