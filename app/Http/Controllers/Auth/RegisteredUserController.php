<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required'],
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($request->password);
        if (isset($input['birthdate'])) {
            $input['birthdate'] = Carbon::parse($input['birthdate'])->toDateString();
        }

        $user = User::create($input);

        event(new Registered($user));

        $user->assignRole($request->role);

        if($request->role != 'operator') {
            $token = $user->createToken('auth');
    
            Auth::login($user);
        }

        return response()->json([
            'status' => true,
            'user' => $user,
            'token' => $token->plainTextToken ?? ''
        ]);
    }
}
