<?php
use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [\App\Http\Controllers\AuthenticationController::class, 'register']); // Register a new user
Route::post('/verify-otp', [\App\Http\Controllers\AuthenticationController::class, 'verifyOtp']); // Verify OTP, false or true
Route::post('/verify-register', [\App\Http\Controllers\AuthenticationController::class, 'verifyRegister']); // if user successfully registered
