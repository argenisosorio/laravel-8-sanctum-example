<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register, method that allows receiving data from a request, validating it
    | and storing it in the users and tokens table, it returns a json response
    | with the assigned token.
    |--------------------------------------------------------------------------
    */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password'])
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Login, method that receives data from a request, compares it with the data
    | stored in the database and returns a json response.
    |--------------------------------------------------------------------------
    */
    public function login(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if (!Auth::attempt($attr)) {
            return response()->json([
                'message' => 'The data is invalid',
            ], 401);
        }
        else {
            $user = User::where('email', $request['email'])->firstOrFail();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);
        }
    }
}
