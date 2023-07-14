<?php

namespace App\Http\Controllers;

use App\Handlers\EncryptionHandler;
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

        // dd(Session::all());
        $name = "";
        if (Session::has('logInfo') && isset(Session::get('logInfo')['account_info'][1]['AccountName'])) {
            $name = Session::get('logInfo')['account_info'][1]['AccountName'];
        }

        $data = [
            'title' => 'Home',
            'prompt' => getPromptPath("get-started"),
            'name' => $name
        ];

        return view('front.home')->with($data);
    }

    public function sendOtp()
    {
        $data = [
            'title' => 'Send OTP',
            'prompt' => getPromptPath("default-send-otp"),
        ];
        return view('front.send-otp')->with($data);
    }

    public function verifyOtp()
    {
        $data = [
            'title' => 'Verify OTP',
            'prompt' => getPromptPath("default-verify-otp"),
        ];
        return view('front.verify-otp')->with($data);
    }

    public function encryptWeb()
    {
        $secretKey = '@@#$UEOE#$#$"{}][|!@#@@';
        $originalString = json_encode(['name' => 'Moon', 'email' => 'rhmoon21@gmail.com']);

        $Encryption = new EncryptionHandler();
        $keyFromPw = $Encryption->getKeyHashed($secretKey);
        $iv = $Encryption->getIV();
        $encryptedVal = $Encryption->encrypt($originalString, $keyFromPw, $iv);
        $decryptedVal = $Encryption->decrypt($encryptedVal, $keyFromPw);

        return view('example')->with([
            'keyFromPw' => $keyFromPw,
            'iv' => $iv,
            'keyFromPw' => bin2hex($keyFromPw),
            'iv' => bin2hex($iv),
            'encryptedVal' => $encryptedVal,
            'decryptedVal' => $decryptedVal
        ]);
    }

    public function decryptWeb()
    {
        $Encryption = new EncryptionHandler();
        $keyFromPw = $Encryption->getKeyHashed($secretKey);

    }


}
