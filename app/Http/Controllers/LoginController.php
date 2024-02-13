<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


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
    $data = $request->validate([
        'name' => 'required|string',
        'email' => 'required|string|unique:users,email',
        'password' => 'required|string|confirmed'
    ]);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password'])
    ]);

    $token = $user->createToken('apiToken')->plainTextToken;

    $res = [
        'user' => $user,
        'token' => $token
    ];
    return response($res, 201);
}


    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'user logged out'
        ];
    }
}
