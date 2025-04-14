<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\ResponseFormatter;
use App\Mail\SendRegisterOTP;
use Laravel\Sanctum\HasApiTokens;


class AuthenticationController extends Controller
{
    public function register()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        do {
            $otp = rand(100000, 999999);

            $otpCount = User::where('otp_register', $otp)->count();
        } while ($otpCount > 0);

        $user = User::create([
            'email' => request()->email,
            'name' => request()->email,
            'otp_register' => $otp
        ]);

        \Mail::to($user->email)->send(new \App\Mail\SendRegisterOTP($user));

        return ResponseFormatter::success([
            'is_sent' => true
        ]);
    }

    public function resendOTP()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $user = User::where('email', request()->email)->whereNotNull('otp_register')->first();
        if (is_null($user)) {

            return ResponseFormatter::error(400, null, ['User tidak ditemukan']);
        }
            do {
                $otp = rand(100000, 999999);

                $otpCount = User::where('otp_register', $otp)->count();
            } while ($otpCount > 0);

            $user->update([
                'otp_register' => $otp
            ]);

            \Mail::to($user->email)->send(new SendRegisterOTP($user));

            return ResponseFormatter::success([
                'is_sent' => true
            ]);

        return ResponseFormatter::error(400, 'Invalid email');
    }
    public function verifyOtp()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|exists:users,otp_register',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $user = User::where('email', request()->email)->where('otp_register', request()->otp)->count();
        if ($user > 0) {
            return ResponseFormatter::success([
                'is_correct' => true
            ]);
        }

        return ResponseFormatter::error(400, 'Invalid OTP');
    }

    public function verifyRegister()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|exists:users,otp_register',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $user = User::where('email', request()->email)->where('otp_register', request()->otp)->first();
        if (!is_null($user)) {
            $user->update([
                'otp_register' => null,
                'email_verified_at' => now(),
                'password' => bcrypt(request()->password)
            ]);

            $token = $user->createToken(config('app.name'))->plainTextToken;

            return ResponseFormatter::success([
                'token' => $token
            ]);
        }

        return ResponseFormatter::error(400, 'Invalid OTP');
    }

    public function login()
    {
        $validator = \Validator::make(request()->all(), [
            'phone_email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $user = User::where('email', request()->phone_email)->orWhere('phone', request()->phone_email)->first();
        if (is_null($user)) {
            return ResponseFormatter::error(400, null, ['User tidak ditemukan']);
        }

        $userPassowrd = $user->password;
        if (\Hash::check(request()->password, $userPassowrd)) {
            $token = $user->createToken(config('app.name'))->plainTextToken;

            return ResponseFormatter::success([
                'token' => $token
            ]);
        }
        return ResponseFormatter::error(400, null, ['Password salah']);
    }

}
