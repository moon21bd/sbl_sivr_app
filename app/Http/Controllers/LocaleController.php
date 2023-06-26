<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class LocaleController extends Controller
{

    /*public function changeLocale(Request $request)
    {
        $locale = $request->input('locale');

        // Set the application locale
        App::setLocale($locale);

        // Store the locale in the session
        $request->session()->put('locale', $locale);

        // Redirect back to the previous page or any desired page
        return redirect()->back();
    }*/

    public function changeLocale(Request $request)
    {
        // Get the selected locale from the request data
        $locale = $request->input('locale');

        // Set the application locale
        App::setLocale($locale);

        // Store the locale in the session
        $request->session()->put('locale', $locale);

        // Return a response with the redirect URL
        return response()->json(['redirect' => url()->previous()]);
    }
}
