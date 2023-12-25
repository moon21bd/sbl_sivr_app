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
        Session::put('is_logged_in', false);
        return response()->json(['message' => 'Good bye !']);
    }

    public function logoutOnClose()
    {
        // Session::put('is_logged_in', false);
        Session::flush();
        return response()->json(['message' => 'Good bye']);
    }
}
