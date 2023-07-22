<?php

namespace App\Http\Controllers;

use App\Handlers\EncryptionHandler;
use App\Models\SblUserImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class MainController extends Controller
{

    public function index()
    {
        // Toast::info('This is a toast message.');
        return view('front.index');
    }

    public function home()
    {
        // dd(ApiController::fetchGetWalletDetails('01710455990'));
        // dd(Session::all());
        $name = "Guest User";
        if (Session::has('logInfo') && isset(Session::get('logInfo')['account_info'][0]['AccountName'])) {
            $name = Session::get('logInfo')['account_info'][0]['AccountName'];
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

    // Assuming you have a route pointing to this controller method

    public function uploadUserPhoto(Request $request)
    {
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');

            // Store the image directly in the desired public folder (public/uploads/photos)
            $publicPath = $file->storeAs('uploads/photos', $file->hashName(), 'public');

            // Save the image details in the database using the SblUserImage model

            $name = null;
            $userPhone = null;
            $userAccount = null;
            if (Session::has('logInfo') && isset(Session::get('logInfo')['account_info'][0]['AccountName'])) {
                $name = Session::get('logInfo')['account_info'][0]['AccountName'] ?? null;
                // $userPhone = Session::get('logInfo')['account_info'][0]['AccountNo'];
                $userPhone = Session::get('logInfo')['otp_info']['otp_phone'] ?? null;
            }

            $image = new SblUserImage();
            $image->user_id = 0; // Replace null with the user ID if applicable
            $image->name = $name;
            $image->user_phone = $userPhone;
            $image->user_account = $userAccount;
            $image->filename = $file->getClientOriginalName();
            $image->path = $publicPath;
            $image->save();

            return response()->json(['image_url' => asset($publicPath)], 200);
        }

        return response()->json(['message' => 'No photo provided.'], 400);
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
