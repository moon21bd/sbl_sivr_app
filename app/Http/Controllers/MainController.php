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

        $prompt = (app()->getLocale() === 'en') ? "home/get-started-en" : "home/get-started-bn";

        Log::info('Prompt: ' . $prompt);

        $data = [
            'title' => 'Home',
            'prompt' => null,
            'name' => $name,
            'photo' => $userPhoto,
        ];

        return view('front.home', $data);
    }

    public function cards()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Cards',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.cards.index', $data);
    }

    public function accountAndLoan()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Account & Loan',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.account-and-loan.index', $data);
    }

    public function casasnd()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'CASASND',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.account-and-loan.casasnd', $data);
    }

    public function fixedDeposit()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Fixed Deposit',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.account-and-loan.fixed-deposit', $data);
    }

    public function accountDPS()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'DPS',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.account-and-loan.dps', $data);
    }

    public function loansAdvances()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Loans & Advances',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.account-and-loan.loans-and-advances', $data);
    }

    public function agentBanking()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Agent Banking',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.agent-banking', $data);
    }

    public function creditCard()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Credit Card',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.cards.credit-card', $data);
    }

    public function debitCard()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Debit Card',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.cards.debit-card', $data);
    }

    public function prePaidCard()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Pre-Paid Card',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.cards.prepaid-card', $data);
    }

    public function eSheba()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'eSheba',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.esheba.index', $data);
    }

    public function eWallet()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'eWallet',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.eWallet.index', $data);
    }


    public function islamiBanking()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Islami Banking',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.islami-banking.index', $data);
    }

    public function ibAccountRelated()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Account Related',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.islami-banking.account-related.index', $data);
    }


    public function ibLoansAdvances()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Loans & Advances',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.islami-banking.loans-advances.index', $data);
    }

    public function sonaliBankProducts()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Sonali Bank Products',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.sonali-products.index', $data);
    }

    public function sonaliPaymentGateway()
    {

        $logInfo = Session::get('logInfo');
        $name = data_get($logInfo, 'account_info.accountName', "Guest User");
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userImage = SblUserImage::where('user_phone', $userPhone)->orderBy('created_at', 'desc')->value('path');
        $userPhoto = $userImage ? asset($userImage) : asset('img/icon/user.svg');

        $data = [
            'title' => 'Sonali Payment Gateway',
            'prompt' => getPromptPath("get-started"),
            'name' => $name,
            'photo' => $userPhoto
        ];

        return view('front.spg.index', $data);
    }

    public function sendOtp()
    {
        $prompt = (app()->getLocale() === 'en') ? "otp/input-phone-number-en" : "otp/input-phone-number-bn";

        $data = [
            'title' => 'Send OTP',
            'prompt' => getPromptPath($prompt),
        ];
        return view('front.send-otp')->with($data);
    }

    public function verifyOtp()
    {
        $prompt = (app()->getLocale() === 'en') ? "otp/input-otp-code-en" : "otp/input-otp-code-bn";

        $data = [
            'title' => 'Verify OTP',
            'prompt' => getPromptPath($prompt),
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
