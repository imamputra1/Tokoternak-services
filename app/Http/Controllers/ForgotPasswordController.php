<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ResponseFormatter;
use App\Mail\SendForgotPasswordOTP;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;


class ForgotPasswordController extends Controller
{

    public function request()
    {
        // Validasi input
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            \Log::error('Forgot password validation failed', ['errors' => $validator->errors()]);
            return ResponseFormatter::error(400, $validator->errors());
        }

        $email = request()->email;

        // Cek existing request
        $existingRequest = \DB::table('password_reset_tokens')->where('email', $email)->first();
        if ($existingRequest) {
            \Log::warning('Duplicate password reset request', ['email' => $email]);
            return ResponseFormatter::error(400, null, [
                'message' => 'Anda sudah melakukan ini, silahkan resend OTP!'
            ]);
        }

        // Generate OTP unik
        do {
            $otp = rand(100000, 999999);
            $otpExists = \DB::table('password_reset_tokens')->where('token', $otp)->exists();
        } while ($otpExists);

        // Simpan OTP ke database
        \DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $otp,
            'created_at' => now(),  // Tambahkan timestamp
              // Tambahkan expiry time
        ]);

        // Dapatkan user
        $user = User::where('email', $email)->firstOrFail();

        // Kirim email dengan error handling
        try {
            \Mail::to($user->email)->send(new \App\Mail\SendForgotPasswordOTP($user, $otp));
            \Log::info('Password reset OTP sent successfully', ['email' => $email, 'otp' => $otp]);

            return ResponseFormatter::success([
                'is_sent' => true,
                'message' => 'OTP telah dikirim ke email Anda'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Hapus token yang sudah dibuat jika email gagal dikirim
            \DB::table('password_reset_tokens')->where('email', $email)->delete();

            return ResponseFormatter::error(500, [
                'message' => 'Gagal mengirim email OTP',
                'error' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }

    public function resendOTP()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $otpRecord = \DB::table('password_reset_tokens')->where('email', request()->email)->first();
        if (is_null($otpRecord)) {
            return ResponseFormatter::error(400, null, [
                'Request tidak ditemukan!'
            ]);
        }

        $user = User::whereEmail(request()->email)->firstOrFail();

        do {
            $otp = rand(100000, 999999);

            $otpCount = \DB::table('password_reset_tokens')->where('token', $otp)->count();
        } while ($otpCount > 0);

        \DB::table('password_reset_tokens')->where('email', request()->email)->update([
            'token' => $otp
        ]);

        \Mail::to($user->email)->send(new \App\Mail\SendForgotPasswordOTP($user, $otp));

        return ResponseFormatter::success([
            'is_sent' => true
        ]);
    }


    public function checkOTP()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|exists:password_reset_tokens,token',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $check = \DB::table('password_reset_tokens')->where('token', request()->otp)->where('email', request()->email)->count();
        if ($check > 0) {
            return ResponseFormatter::success([
                'is_correct' => true
            ]);
        }

        return ResponseFormatter::error(400, 'Invalid OTP');
    }

    public function resetPassword()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|exists:password_reset_tokens,token',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $token = \DB::table('password_reset_tokens')->where('token', request()->otp)->where('email', request()->email)->first();
        if (!is_null($token)) {
            $user = User::whereEmail(request()->email)->first();
            $user->update([
                'password' => bcrypt(request()->password)
            ]);
            \DB::table('password_reset_tokens')->where('token', request()->otp)->where('email', request()->email)->delete();

            $token = $user->createToken(config('app.name'))->plainTextToken;

            return ResponseFormatter::success([
                'token' => $token
            ]);
        }

        return ResponseFormatter::error(400, 'Invalid OTP');
    }
}
