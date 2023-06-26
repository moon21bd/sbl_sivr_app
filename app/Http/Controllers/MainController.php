<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;


class MainController extends Controller
{
    public function index()
    {
        return view('front.index');
    }

    public function home()
    {
        $data = [
            'title' => 'Home Page',
            'prompt' => asset('uploads/prompts/audio.mp3'),
        ];

        return view('front.home')->with($data);
    }

}
