<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;


class MainController extends Controller
{
    public function index()
    {
        return view('front.index');
    }

    public function home()
    {
        $name = "";
        if (Session::has('logInfo')) {
            if (Session::has('logInfo')) {
                $name = Session::get('logInfo')['account_info'][1]['AccountName'];
            }
        }

        $data = [
            'title' => 'Home Page',
            'prompt' => asset('uploads/prompts/audio.mp3'),
            'name' => $name
        ];

        return view('front.home')->with($data);
    }

    public function sendOtp()
    {
        return view('front.send-otp');
    }

    public function verifyOtp()
    {
        return view('front.verify-otp');
    }
}
