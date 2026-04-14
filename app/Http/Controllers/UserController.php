<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClientDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userType = $request->get('user_type', 'customer'); // 'customer' or 'user'

            $users = User::where('user_type', $userType)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $users
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
                'message' => 'Something went wrong!'
            ]);
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
                'message' => 'User not found!'
            ]);
        }
    }

    public function manage(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validatedData = $request->validate([
                'firstname' => 'sometimes|string|max:255',
                'lastname' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'phone' => 'sometimes|string|max:255',
                'user_type' => 'sometimes|in:customer,user',
                'status' => 'sometimes|integer|in:0,1',
                'password' => 'sometimes|string|min:8'
            ]);

            if (isset($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
                'message' => 'Something went wrong!'
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deletion of current user
            if ($user->id === Auth::id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete your own account!'
                ]);
            }

            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
                'message' => 'Something went wrong!'
            ]);
        }
    }

    public function getDocuments($id)
    {
        try {
            $user = User::findOrFail($id);

            $documents = ClientDocument::where('users_id', $id)->get();

            return response()->json([
                'status' => true,
                'data' => $documents
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
                'message' => 'Something went wrong!'
            ]);
        }
    }

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
