<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
     public function login(Request $request)
    {
        // Validate the request
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt authentication
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;
          return  apiResponse(true, 'Login successful', ['token' => $token, 'user' => $user], 200);

            
        }

        // If authentication fails
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

     public function logout(Request $request)
    {
        // auth()->user()->currentAccessToken()->delete(); // Revoke all tokens for the user
        // $request->user()->currentAccessToken()->delete();
        $request->user()->currentAccessToken()->delete();
        return apiResponse(true, 'Logged out successfully', [], 200);
 

    }

    public function getMyPermissions()
    { 
        return getPermissionsApi();
    }
}
