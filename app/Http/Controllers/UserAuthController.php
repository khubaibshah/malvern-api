<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

public function store(LoginRequest $request){
    $request->authenticate();
    $request->session()->regenerate();
    return response->noContent();

}
public function login(Request $request)
{
    // Validate request data
    $credentials = $request->validate([
        'email' => 'required|email|max:255',
        'password' => 'required|string',
    ]);

    // Attempt to authenticate the user
    if (Auth::attempt($credentials)) {
        // Authentication successful
        $user = Auth::user();
        $token = $user->createToken('AuthToken')->plainTextToken;

        // Return user data and token
        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    // Authentication failed
    return response()->json(['error' => 'Unauthorized'], 401);
}

public function register(Request $request){
    $registerUserData = $request->validate([
        'name'=>'required|string',
        'email'=>'required|string|email|unique:users',
        'password'=>'required|min:8'
    ]);
    $user = User::create([
        'name' => $registerUserData['name'],
        'email' => $registerUserData['email'],
        'password' => Hash::make($registerUserData['password']),
    ]);
    return response()->json([
        'message' => 'User Created ',
    ]);
}

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'user logged out'
        ];
    }
}
