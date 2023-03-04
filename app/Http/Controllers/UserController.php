<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updateProfileImage(Request $request)
    {
        try {
            if (auth()->id()) {
                if (!$user = User::find(auth()->id()))
                    return response()->json([
                        'status' => false,
                        'message' => 'User not found!',
                    ]);
                $fileName = auth()->id() . '_' . time() . '.' . $request->image->extension();
                if (!file_exists(public_path('profiles'))) {
                    mkdir(public_path('profiles'));
                }
                $request->image->move(public_path('profiles'), $fileName);

                $response = User::where(['id' => auth()->id()])->update([
                    'profile_img' => 'profiles/' . $fileName
                ]);

                $user->profile_img = 'profiles/' . $fileName;

                if (!$response)
                    return response()->json([
                        'status' => false,
                        'message' => 'Something went wrong!',
                    ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Profile Image Updated',
                    'data' => ['user' => $user]
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Internal Server Error! Please log in!'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
                'message' => 'Something went wrong!'
            ]);
        }
    }
}
