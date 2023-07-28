<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function checkLoginStatus(Request $request)
    {
        // Your logic to check the login status goes here
        // For example, you can use the 'logInfo' session data
        $isLogged = $request->session()->has('logInfo');

        // Return the login status as JSON response
        return response()->json(['is_logged' => $isLogged]);
    }

    public function logout()
    {
        // Implement any necessary cleanup or logout procedures here
        // For example, you can clear the user session data or perform any other actions

        // Set 'is_logged_in' to false in the session
        Session::put('is_logged_in', false);

        // Return a JSON response indicating successful logout
        return response()->json(['message' => 'Logout successful']);
    }
}
