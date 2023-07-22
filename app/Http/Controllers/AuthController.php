<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
