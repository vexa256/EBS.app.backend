<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class MobileAuthController extends Controller
{

    public function AuthenticateUser(Request $request)
    {
        $credentials = $request->only('PhoneNumber', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed
            $user = Auth::user();
            unset($user->password); // Remove the password from the user object

            return response()->json([
                'message' => 'Authentication successful',
                'user' => $user
            ], 200);
        } else {
            // Authentication failed
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    }
}
