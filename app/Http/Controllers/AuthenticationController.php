<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            return response()->json([
                'message' => 'user logged in successfully',
                'statusCode' => 200,
                'user'=>auth()->user(),
                'token' => auth()->user()->createToken('accessToken')->accessToken,
            ]);
        } else {
            return response()->json([
                'errors' => 'username or password is not correct',
            ]);
        }
    }
    public function register(RegisterRequest $request)
    {
        $user=User::create($request->validated());
        $user->assignRole(['user']);
        $user->save();
        auth()->login($user);
        return response()->json([
            'message' => 'User created successfully',
            'user'=>auth()->user(),
            'token' => $user->createToken('accessToken')->accessToken,
            'created' => true,
        ]);

    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'logged out',
            'statusCode' => 200,
        ]);
    }
}