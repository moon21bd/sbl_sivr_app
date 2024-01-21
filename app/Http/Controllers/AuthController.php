<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    public function logoutOnClose(Request $request)
    {
        Session::flush();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        $referer = $_SERVER['HTTP_REFERER'];

        session()->flash('status', 'error');
        session()->flash('message', __('messages.you_are_being_logged_out'));

        Log::info("LOGOUT-ON-CLOSE|REQUEST|" . json_encode($request->all()) . "|REFERER|" . $referer);

        return response()->json(['success' => true, 'message' => 'Logged out successfully']);
    }

    public function exitIntentDetection(Request $request)
    {
        $referer = $request->header('referer');
        $requests = $request->all();
        $referer2 = $_SERVER['HTTP_REFERER'];

        Log::info("exitIntentDetection-Request-Received|Referer|" . $referer . "|" . $referer2 . "|Request|" . json_encode($requests));

    }
}
