<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    /**
     * Create user function.
     */
    public function register(UserRequest $request)
    {
        $validate = $request->validated();

        $user = User::create([
            'name' => $validate['name'],
            'email' => $validate['email'],
            'password' => Hash::make($validate['password'])
        ]);


        $response = [
            'user' => $user,
        ];

        return response($response, 201);
    }

    /**
     * User login function.
     */
    public function login(Request $request)
    {
        $filds = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $filds['email'])->first();

        if (!$user || !Hash::check($filds['password'], $user->password)) {
            return response(['message' => 'Bad Credentials'], 401);
        } else {
            $token = $user->createToken('myapptoken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];
        }

        return response($response, 201);
    }

    /**
     * User logout function.
     */
    public function logout(Request $request)
    {
        Auth::user()->tokens()->delete();
        return response(['message' => 'logget out'], 200);
    }

    public function getAuthUser()
    {
        $authuser = auth('sanctum')->user();
        if ($authuser) {
            return response()->json([
                'status' => 200,
                'message' => 'user who are authenticate',
                'data' => $authuser,
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'no authenticate users',
                'data' => $authuser,
            ]);
        }
    }
}
