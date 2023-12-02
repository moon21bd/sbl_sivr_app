<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocaleController extends Controller
{
    public function changeLocale(Request $request)
    {
        $locale = $request->input('locale');
        Log::info('currentLocale: ' . $locale);

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        // $url = url()->previous();
        // $url = url('/');

        return response()->json(['redirect' => url()->previous()]);
    }
}
