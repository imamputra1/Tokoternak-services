<?php
use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [\App\Http\Controllers\AuthenticationController::class, 'register']); // Register a new user
Route::post('/check-otp-register', [\App\Http\Controllers\AuthenticationController::class, 'verifyOtp']); // Verify OTP, false or true
Route::post('/verify-register', [\App\Http\Controllers\AuthenticationController::class, 'verifyRegister']); // if user successfully registered
Route::post('/resend-otp-register', [\App\Http\Controllers\AuthenticationController::class, 'resendOtp']); // Resend OTP

Route::prefix('forgot-password')->group(function(){
    Route::post('/request', [\App\Http\Controllers\ForgotPasswordController::class, 'request']); // Send OTP to email
    Route::post('/resend-otp', [\App\Http\Controllers\ForgotPasswordController::class, 'resendOTP']); // Verify OTP
    Route::post('/check-otp', [\App\Http\Controllers\ForgotPasswordController::class, 'checkOTP']); // Reset Password
    Route::post('/reset-password', [\App\Http\Controllers\ForgotPasswordController::class, 'resetPassword']); // Resend OTP
});

Route::post('/login', [\App\Http\Controllers\AuthenticationController::class, 'login']); // Login user


