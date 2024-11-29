<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('userdetails', [AuthController::class, 'userdetails']);
Route::post('update_profile', [AuthController::class, 'update_profile']);
Route::post('coins_list', [AuthController::class, 'coins_list']);
Route::post('avatar_list', [AuthController::class, 'avatar_list']);
Route::post('transaction_list', [AuthController::class, 'transaction_list']);
Route::post('send_otp', [AuthController::class, 'send_otp']);
Route::post('settings_list', [AuthController::class, 'settings_list']);
Route::post('delete_users', [AuthController::class, 'delete_users']);
Route::post('user_validations', [AuthController::class, 'user_validations']);



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
