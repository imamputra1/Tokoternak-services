<?php
use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [\App\Http\Controllers\AuthenticationController::class, 'register']); // Register a new user
Route::post('/check-otp-register', [\App\Http\Controllers\AuthenticationController::class, 'verifyOtp']); // Verify OTP, false or true
Route::post('/verify-register', [\App\Http\Controllers\AuthenticationController::class, 'verifyRegister']); // if user successfully registered
Route::post('/resend-otp-register', [\App\Http\Controllers\AuthenticationController::class, 'resendOtp']); // Resend OTP
Route::post('/login', [\App\Http\Controllers\AuthenticationController::class, 'login']); // Login user
Route::post('/logout', [\App\Http\Controllers\AuthenticationController::class, 'logout']); // Logout user
Route::post('/forgot-password', [\App\Http\Controllers\AuthenticationController::class, 'forgotPassword']); // Forgot password
Route::post('/reset-password', [\App\Http\Controllers\AuthenticationController::class, 'resetPassword']); // Reset password
Route::post('/check-otp-reset-password', [\App\Http\Controllers\AuthenticationController::class, 'checkOtpResetPassword']); // Check OTP for reset password
Route::post('/verify-reset-password', [\App\Http\Controllers\AuthenticationController::class, 'verifyResetPassword']); // Verify reset password
Route::post('/resend-otp-reset-password', [\App\Http\Controllers\AuthenticationController::class, 'resendOtpResetPassword']); // Resend OTP for reset password
