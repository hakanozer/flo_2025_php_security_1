<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\JWTHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create JWT token
        $payload = [
            'iss' => "laravel-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued
            'exp' => time() + 60*60 // Expiration time (1 hour)
        ];

        $jwt = $this->generateJWT($payload);

        // Store token in database for logout functionality
        $user->api_token = $jwt;
        $user->save();

        return response()->json([
            'access_token' => $jwt,
            'token_type' => 'bearer',
            'expires_in' => 3600,
            'user' => $user
        ]);
    }

    /**
     * Register a new user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:note,product',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Create JWT token
        $payload = [
            'iss' => "laravel-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued
            'exp' => time() + 60*60 // Expiration time (1 hour)
        ];

        $jwt = $this->generateJWT($payload);

        // Store token in database for logout functionality
        $user->api_token = $jwt;
        $user->save();

        return response()->json([
            'access_token' => $jwt,
            'token_type' => 'bearer',
            'expires_in' => 3600,
            'user' => $user
        ], 201);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->api_token = null;
        $user->save();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Generate JWT token
     *
     * @param  array  $payload
     * @return string
     */
    private function generateJWT($payload)
    {
        return JWTHelper::encode($payload);
    }
}
