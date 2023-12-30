<?php

namespace App\Http\Controllers;

use App\Handlers\EncryptionHandler;
use App\Models\SblUserImage;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        return view('front.index');
    }

    public function home()
    {
        // Uncomment these lines for debugging
        // dd(Session::all());

        $prompt = (app()->getLocale() === 'en') ? "home/get-started-en" : "home/get-started-bn";
        $userInfo = getUserInfoFromSession();

        $data = [
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
            'title' => __('messages.home'),
            'prompt' => null,
        ];

        return view('front.home', $data);
    }

    public function cards()
    {

        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/cards'), 'label' => __('messages.cards')],
        ];

        $userInfo = getUserInfoFromSession();

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.cards'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.cards.index', $data);
    }

    public function accountAndLoan()
    {

        $userInfo = getUserInfoFromSession();

        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/account-and-loan'), 'label' => __('messages.account-loans')]
        ];

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.account-loans'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.account-and-loan.index', $data);
    }

    public function casasnd()
    {

        $userInfo = getUserInfoFromSession();

        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/account-and-loan'), 'label' => __('messages.account-loans')],
            ['url' => url('/casasnd'), 'label' => __('messages.CASASND-btn')],
        ];

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.CASASND-btn'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.account-and-loan.casasnd', $data);
    }

    public function fixedDeposit()
    {

        $userInfo = getUserInfoFromSession();
        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/account-and-loan'), 'label' => __('messages.account-loans')],
            ['url' => url('/fixed-deposit'), 'label' => __('messages.fixed-deposit')],
        ];

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.fixed-deposit'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.account-and-loan.fixed-deposit', $data);
    }

    public function accountDPS()
    {
        $userInfo = getUserInfoFromSession();

        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/account-and-loan'), 'label' => __('messages.account-loans')],
            ['url' => url('/account-dps'), 'label' => __('messages.DPS-btn')],
        ];

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.DPS-btn'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],

        ];

        return view('front.account-and-loan.dps', $data);
    }

    public function loansAdvances()
    {

        $userInfo = getUserInfoFromSession();
        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/account-and-loan'), 'label' => __('messages.account-loans')],
            ['url' => url('/loans-advances'), 'label' => __('messages.loans-advances')],
        ];

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.loans-advances'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.account-and-loan.loans-and-advances', $data);
    }

    public function agentBanking()
    {

        $userInfo = getUserInfoFromSession();

        $data = [
            'title' => __('messages.agent-banking'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.agent-banking', $data);
    }

    public function creditCard()
    {

        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/cards'), 'label' => __('messages.cards')],
            ['url' => url('/credit-card'), 'label' => __('messages.credit-card')],
        ];

        $userInfo = getUserInfoFromSession();

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.credit-card'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.cards.credit-card', $data);
    }

    public function debitCard()
    {

        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/cards'), 'label' => __('messages.cards')],
            ['url' => url('/debit-card'), 'label' => __('messages.debit-card')],
        ];

        $userInfo = getUserInfoFromSession();

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.debit-card'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.cards.debit-card', $data);
    }

    public function prePaidCard()
    {
        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/cards'), 'label' => __('messages.cards')],
            ['url' => url('/prepaid-card'), 'label' => __('messages.prepaid-card')],
        ];

        $userInfo = getUserInfoFromSession();

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.prepaid-card'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.cards.prepaid-card', $data);
    }

    public function eSheba()
    {

        $userInfo = getUserInfoFromSession();

        $data = [
            'title' => __('messages.eSheba-btn'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.esheba.index', $data);
    }

    public function eWallet()
    {
        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/ewallet'), 'label' => __('messages.eWallet-btn')]
        ];

        $userInfo = getUserInfoFromSession();


        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.eWallet-btn'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.eWallet.index', $data);
    }

    public function islamiBanking()
    {

        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/islami-banking'), 'label' => __('messages.islami-banking')],
        ];

        $userInfo = getUserInfoFromSession();

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.islami-banking'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.islami-banking.index', $data);
    }

    public function ibAccountRelated()
    {
        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/islami-banking'), 'label' => __('messages.islami-banking')],
            ['url' => url('/ib-account-related'), 'label' => __('messages.ib-account-related-btn')],
        ];

        $userInfo = getUserInfoFromSession();

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.ib-account-related-btn'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.islami-banking.account-related.index', $data);
    }

    public function ibLoansAdvances()
    {

        $breadcrumbs = [
            ['url' => url('/'), 'label' => __('messages.home')],
            ['url' => url('/islami-banking'), 'label' => __('messages.islami-banking')],
            ['url' => url('/ib-loans-advances'), 'label' => __('messages.ib-loans-advances')],
        ];

        $userInfo = getUserInfoFromSession();

        $data = [
            'breadcrumbs' => $breadcrumbs,
            'title' => __('messages.ib-loans-advances'),
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.islami-banking.loans-advances.index', $data);
    }

    public function sonaliBankProducts()
    {

        $userInfo = getUserInfoFromSession();

        $data = [
            'title' => 'Sonali Bank Products',
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.sonali-products.index', $data);
    }

    public function sonaliPaymentGateway()
    {

        $userInfo = getUserInfoFromSession();

        $data = [
            'title' => 'Sonali Payment Gateway',
            'prompt' => null,
            'name' => $userInfo['name'],
            'photo' => $userInfo['userImage'],
        ];

        return view('front.spg.index', $data);
    }

    public function sendOtp()
    {
        $prompt = (app()->getLocale() === 'en') ? "otp/input-phone-number-en" : "otp/input-phone-number-bn";

        $data = [
            'title' => __('messages.send-otp'),
            'prompt' => getPromptPath($prompt),
        ];
        return view('front.send-otp')->with($data);
    }

    public function verifyOtp()
    {
        $prompt = (app()->getLocale() === 'en') ? "otp/input-otp-code-en" : "otp/input-otp-code-bn";

        $data = [
            'title' => __('messages.verify-otp'),
            'prompt' => getPromptPath($prompt),
        ];
        return view('front.verify-otp')->with($data);
    }


    public function uploadUserPhoto(Request $request)
    {
        try {
            // Get the base64 image data from the request
            $base64Image = $request->input('photo');

            $userPhone = data_get(Session::get('logInfo'), 'otp_info.otp_phone');
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
