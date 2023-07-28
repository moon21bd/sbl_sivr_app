<?php

namespace App\Http\Controllers;

use App\Handlers\EncryptionHandler;
use App\Models\SblUserImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class MainController extends Controller
{

    public function index()
    {
        return view('front.index');
    }

    public function home()
    {
        // Uncomment these lines for debugging
        // session()->forget(['logInfo', 'api_calling']);
        // dd(Session::all());

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Home',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto,
        ];

        return view('front.home', $data);
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
        try {
            // Get the base64 image data from the request
            $base64Image = $request->input('photo');

            // Save the image to the public/uploads/photos directory and get the image path
            $userPhone = Session::get('logInfo')['otp_info']['otp_phone'] ?? null;
            $accountName = Session::get('logInfo')['account_info']['accountName'] ?? null;
            $accountNo = Session::get('logInfo')['account_info']['accountNo'] ?? null;

            $imagePath = $this->uploadBase64Image($base64Image, $userPhone);

            // Save the image details in the database using the SblUserImage model
            $image = new SblUserImage();
            $image->user_id = 0;
            $image->name = $accountName;
            $image->user_phone = $userPhone;
            $image->user_account = $accountNo;
            $image->filename = $imagePath['filename'];
            $image->path = $imagePath['path']; // Save the image path in the database
            $image->save();

            // Return the image URL as a response
            return response()->json(['image_url' => asset($imagePath['path'])], 200);
        } catch (\Exception $error) {
            // Handle the error appropriately
            return response()->json(['message' => 'An error occurred while uploading the file. Please try again later.'], 500);
        }
    }

    protected function uploadBase64Image($inputFile, $namePrefix)
    {
        $imagePathPrefix = "uploads/photos/"; // Update the image path prefix
        $imageParts = explode(";base64,", $inputFile);
        $imageTypeAux = explode("image/", $imageParts[0]);
        $extension = $imageTypeAux[1];
        $imageBase64 = base64_decode($imageParts[1]);
        $fileNameToStore = $namePrefix . '-' . uniqid() . '.' . $extension;
        $filePath = public_path($imagePathPrefix) . $fileNameToStore;
        file_put_contents($filePath, $imageBase64);

        return [
            'filename' => $fileNameToStore,
            'path' => $imagePathPrefix . $fileNameToStore
        ];
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
