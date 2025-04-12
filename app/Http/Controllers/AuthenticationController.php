<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\ResponseFormatter;


class AuthenticationController extends Controller
{
        public function register(){
        $validator = \Validator::make(request()->all(),[
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return \App\ResponseFormatter::error(400, $validator->errors(), 'Validation Error');
        }

        do {
            $otp = rand(100000, 999999);

            $otpCount = User::where('otp_register', $otp)->count();
        } while ($otpCount > 0);

        $user = User::create([
            'email' => request()->email,
            'name' => request()->email,
            'otp_register' => $otp,
        ]);

        return ResponseFormatter::success($user, 'User created successfully');


    }
    public function verifyOtp(){


    }
    public function verifyRegister(){

    }

}
