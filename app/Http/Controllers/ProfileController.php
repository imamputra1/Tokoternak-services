<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\ResponseFormatter;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getProfile()
    {
        $user = auth()->user();
        return ResponseFormatter::success(
            $user->api_response );
    }

    public function updateProfile()
    {
        $validator = \Validator::make(request()->all(), [
            'name'=>'required|min:3|max:30',
            'email'=>'required|email',
            'photo' => 'nullable|image|max:1024',
            'username'=>'required|min:3|max:20',
            'phone'=>'required|numeric',
            'farm_name'=>'nullable|min:2|max:20',
            'gender'=>'required|in:Laki-Laki,Perempuan,Lainnya',
            'birth_date'=>'required|date_format:Y-m-d',
            'address'=>'required|min:3|max:100',
            'city'=>'required|min:3|max:50',
            'social_media_provider'=>'nullable|in:google,facebook',
            'social_media_id'=>'nullable',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $payload = $validator->validated();
        if (!is_null(request()->photo)) {
            $payload['photo'] = request()->file('photo')->store(
                'user-photo', 'public'
            );
        }

        auth()->user()->update($payload);

        return $this->getProfile();
    }

    //     return $this->getProfile();
//     // }
//     public function updateProfile(Request $request)
// {
//     // Validasi input
//     $validator = \Validator::make(request()->all(), [
//         'name' => 'required|min:2|max:100',
//         'email' => 'required|email',
//         'photo' => 'nullable|image|max:1024',
//         'username' => 'nullable|min:2|max:20',
//         'phone' => 'nullable|numeric',
//         'farm_name' => 'nullable|min:2|max:100',
//         'gender' => 'required|in:Laki-Laki,Perempuan,Lainnya',
//         'birth_date' => 'nullable|date_format:Y-m-d',
//         'address' => 'nullable|min:3|max:100',
//         'city' => 'nullable|min:3|max:50',
//         'social_media_provider' => 'nullable|in:google,facebook',
//         'social_media_id' => 'nullable',
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'meta' => [
//                 'code' => 400,
//                 'status' => 'error',
//                 'messages' => $validator->errors()->all()
//             ]
//         ], 400);
//     }

//     try {
//         $user = auth()->user();
//         $data = $request->except(['email', 'photo']); // Email tidak boleh diupdate

//         // Handle file upload
//         if ($request->hasFile('photo')) {
//             $path = $request->file('photo')->store('profile-photos', 'public');
//             $data['photo_url'] = Storage::url($path);
//         }

//         $user->update($data);

//         return response()->json([
//             'meta' => [
//                 'code' => 200,
//                 'status' => 'success',
//                 'messages' => ['Profile updated successfully']
//             ],
//             'data' => $user->fresh()
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'meta' => [
//                 'code' => 500,
//                 'status' => 'error',
//                 'messages' => ['Failed to update profile']
//             ]
//         ], 500);
//     }
    // $payload = $validator->validated();
    // if (!is_null(request()->photo)) {
    //     $payload['photo'] = request()->file('photo')->store(
    //         'user-photo', 'public'
    //     );
    // }
    // auth()->user()->update($payload);

//     // return $this->getProfile();
// }
}
