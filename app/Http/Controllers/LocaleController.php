<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocaleController extends Controller
{
    public function changeLocale(Request $request)
    {
        // Get the selected locale from the request data
        $locale = $request->input('locale');

        Log::info('locale: ' . $locale);

        // Set the application locale
        App::setLocale($locale);

        // Store the locale in the session
        $request->session()->put('locale', $locale);

        // Return a response with the redirect URL
        $url = url()->previous();
        $url = url('/');

        return response()->json(['redirect' => $url]);
    }
}
