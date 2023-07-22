<?php

namespace App\Http\Controllers;

use App\Handlers\APIHandler;
use App\Handlers\EncryptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class ApiController extends ResponseController
{
    public function getBalance(Request $request)
    {
        // will be deleted this code later
        $responseOut = [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'balance' => 120
        ];
        return $this->sendResponse($responseOut);
        // will be deleted this code later

        $response = self::fetchGetWalletDetails('01710455990');
        if ($response['code'] === Response::HTTP_OK && $response['status'] === 'success') {
            $responseOut = [
                'code' => Response::HTTP_OK,
                'status' => 'success',
                'balance' => number_format($response['data']['balanceAmount'], 2),
            ];
            return $this->sendResponse($responseOut, Response::HTTP_OK);
        }

        $responseOut = [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'balance' => 0
        ];
        return $this->sendResponse($responseOut);
    }

    public function endpoint(Request $request)
    {
        // Retrieve the encrypted request payload and IV
        $encryptedData = base64_decode($request->input('encryptedData'));
        $iv = hex2bin($request->input('iv'));

        // Get the encryption key from the Laravel app config
        $encryptionKey = config('app.encryption_key');

        // Decrypt the request payload
        $decryptedPayload = openssl_decrypt($encryptedData, 'AES-256-CBC', $encryptionKey, OPENSSL_RAW_DATA, $iv);

        // Process the decrypted data
        $payload = json_decode($decryptedPayload, true);
        // ...

        // Prepare the response data
        $responseData = [
            'result' => 'Data received and processed successfully',
        ];

        // Encrypt the response data with the same encryption key and a new IV
        $responseIv = Str::random(16);
        $encryptedResponseData = openssl_encrypt(json_encode($responseData), 'AES-256-CBC', $encryptionKey, OPENSSL_RAW_DATA, $responseIv);

        // Encode the encrypted response and IV using base64
        $encodedEncryptedResponseData = base64_encode($encryptedResponseData);
        $encodedResponseIv = bin2hex($responseIv);

        // Return the encoded encrypted response and IV
        return response()->json([
            'encryptedResponseData' => $encodedEncryptedResponseData,
            'iv' => $encodedResponseIv,
        ]);
    }

    public function sendOtpWrapper(Request $request)
    {
        $request->validate([
            'mobile_no' => $this->phoneValidationRules()
        ], $this->phoneValidationErrorMessages());

        $mobileNo = $request->input('mobile_no');

        // will be removed later
        $mobileNo = '01710455990';
        $strRefId = $mobileNo . randomDigits();
        Session::put('otp', [
            'phone_masked' => $this->hidePhoneNumber($mobileNo),
            'otp_phone' => $mobileNo,
            'strRefId' => $strRefId
        ]);

        $responseOut = [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Success.',
            'url' => url('verify-otp')
        ];
        return $this->sendResponse($responseOut, $responseOut['code']);
        // will be removed later


        $apiHandler = new APIHandler();
        $url = config('api.base_url') . config('api.send_otp_url');
        $strRefId = $mobileNo . randomDigits();
        $response = $apiHandler->postCall($url, [
            'strRefId' => $strRefId,
            'strMobileNo' => $mobileNo,
            'isEncPwd' => true,
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === Response::HTTP_OK) { // successful api response found from apihandler end.

            Log::info('API RESPONSE:: ' . json_encode($response['data']));

            $isValidData = $this->decodeJsonIfValid($response['data']);
            if ($isValidData !== null) {
                $data = $this->decodeJsonIfValid($isValidData);
                $statusCode = intval($data['StatusCode']);
                if ($statusCode === 400) {
                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'error',
                        'message' => 'The phone number entered is invalid or an unexpected error has occurred.',
                        'prompt' => getPromptPath('phone-number-invalid')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);

                } else if ($statusCode === Response::HTTP_OK) {
                    Session::put('otp', [
                        'phone_masked' => $this->hidePhoneNumber($mobileNo),
                        'otp_phone' => $mobileNo,
                        'strRefId' => $strRefId
                    ]);

                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'success',
                        'message' => 'Success.',
                        'url' => url('verify-otp')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);

                } else {
                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'error',
                        'message' => 'Apologies, something went wrong. Please try again later.',
                        'prompt' => getPromptPath('apologies-error')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                }

            } else {
                $responseOut = [
                    'code' => Response::HTTP_EXPECTATION_FAILED,
                    'status' => 'error',
                    'message' => 'Null response',
                    'prompt' => getPromptPath('apologies-error')
                ];
                return $this->sendResponse($responseOut, $responseOut['code']);
            }

        } else {

            $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
            Log::error('API ERROR:: ' . $msg);
            $responseOut = [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => 'Apologies, something went wrong. Please try again later.',
                'prompt' => getPromptPath('apologies-error')
            ];
            return $this->sendResponse($responseOut, $responseOut['code']);
        }


    }

    public function verifyOtpWrapper(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $mobileNo = Session::get('otp.otp_phone');
        $strRefId = Session::get('otp.strRefId');

        // will be removed later

        // call api to get user account name.
        $otpInfo = Session::get('otp');
        $statusCode = Response::HTTP_OK;
        // $getAccountList = self::fetchSavingsDeposits($otpInfo['otp_phone']);
        $getAccountList = [
            'AccountName' => 'Md Raqibul Hasan',
            'AccountNo' => '5107801028828',
        ];

        Session::put('logInfo', [
            'is_logged' => base64_encode(true),
            'otp_info' => $otpInfo,
            'account_info' => $getAccountList,
        ]);

        Session::forget('otp');

        /*$apiCalling = Session::get('api_calling');
        if (isset($apiCalling['purpose'])) {
            $purpose = $apiCalling['purpose'];
            if (strtoupper($purpose) === "CARDACTIVATE") {
                $resp = $this->processApiCallingCardActivation($mobileNo);

                session(['api_response' => [
                    'purpose' => $purpose,
                    'response' => $resp
                ]]);
            }
        }*/

        $responseOut = [
            'code' => $statusCode,
            'status' => 'success',
            'message' => 'Your account verification was successful.',
            'prompt' => getPromptPath('account-verification-success'),
            'pn' => $mobileNo,
            'an' => $getAccountList[1]['AccountName'] ?? null,
            'acn' => $getAccountList[1]['AccountNo'] ?? null,
            'url' => url('/')
        ];

        // Set the flash message
        session()->flash('status', $responseOut['status']);
        session()->flash('message', $responseOut['message']);

        return $this->sendResponse($responseOut, $responseOut['code']);

        // will be removed later

        $apiHandler = new APIHandler();
        $url = config('api.base_url') . config('api.verify_otp_url');
        $response = $apiHandler->postCall($url, [
            'strRequstId' => $strRefId,
            'strAcMobileNo' => $mobileNo,
            'strReOTP' => $request->input('code'),
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) { // successful api response found from apihandler end.

            // Log::info('API RESPONSE:: ' . json_encode($response['data']));
            $firstData = json_decode($response['data']);
            $secondData = json_decode($firstData);
            $apiStatus = (bool)$secondData->Status;
            $statusCode = $response['statusCode'];

            if ($statusCode === Response::HTTP_OK) {
                if ($apiStatus === false) {
                    // Verification failed
                    $responseOut = [
                        'code' => Response::HTTP_EXPECTATION_FAILED,
                        'status' => 'error',
                        'message' => 'Apologies, something went wrong. Please try again later.',
                        'prompt' => getPromptPath('apologies-error')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                } else { // success
                    // After Verification
                    // Make the user as logged-in user, set flag to verify user.
                    // call api to get user account name.
                    $otpInfo = Session::get('otp');
                    $getAccountList = self::fetchSavingsDeposits($otpInfo['otp_phone']);
                    Session::put('logInfo', [
                        'is_logged' => base64_encode(true),
                        'otp_info' => $otpInfo,
                        'account_info' => $getAccountList,
                    ]);
                    Session::forget('otp');

                    /*$apiCalling = Session::get('api_calling');
                    if (isset($apiCalling['purpose'])) {
                        $purpose = $apiCalling['purpose'];
                        if (strtoupper($purpose) === "CARDACTIVATE") {
                            $resp = $this->processApiCallingCardActivation($mobileNo);

                            session(['api_response' => [
                                'purpose' => $purpose,
                                'response' => $resp
                            ]]);
                        }
                    }*/

                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'success',
                        'message' => 'Success',
                        'pn' => $mobileNo,
                        'an' => $getAccountList[1]['AccountName'] ?? null,
                        'acn' => $getAccountList[1]['AccountNo'] ?? null,
                        'url' => url('/')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                }
            } else {
                $responseOut = [
                    'code' => $statusCode,
                    'status' => 'error',
                    'message' => 'Apologies, something went wrong. Please try again later.',
                    'prompt' => getPromptPath('apologies-error')
                ];
                return $this->sendResponse($responseOut, $responseOut['code']);
            }

        } else {

            $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
            Log::error('API ERROR:: ' . $msg);
            $responseOut = [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => 'Apologies, something went wrong. Please try again later.',
                'prompt' => getPromptPath('apologies-error')
            ];
            return $this->sendResponse($responseOut, $responseOut['code']);
        }

    }

    public static function fetchSavingsDeposits($phoneNumber)
    {
        $url = config('api.base_url') . config('api.get_account_list_url');
        $apiHandler = new APIHandler();
        $response = $apiHandler->postCall($url, ['MobileNo' => $phoneNumber]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);
            if (isset($data['StatusCode']) && intval($data['StatusCode']) === Response::HTTP_OK) {
                $accountList = $data['GetAccountList'];

                $savingsDeposits = array_filter($accountList, function ($account) {
                    return $account['ProductName'] === 'Savings Deposit';
                });

                return array_map(function ($account) {
                    return [
                        'AccountName' => $account['AccountName'],
                        'AccountNo' => $account['AccountNo'],
                    ];
                }, $savingsDeposits);
            }
        }

        return [];
    }

    public static function fetchGetWalletDetails($phoneNumber)
    {
        $url = config('api.base_url') . config('api.get_wallet_details_url');
        $apiHandler = new APIHandler();
        $response = $apiHandler->postCall($url, ['mobileNo' => $phoneNumber, 'userId' => 'Agx01254']);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);

            if ($data['status'] === '200' && $data['statsDetails'] === 'success' && isset($data['acList'][0])) {
                $accountList = $data['acList'];

                return [
                    'status' => 'success',
                    'message' => 'Data Received',
                    'code' => Response::HTTP_OK,
                    'data' => [
                        'name' => $data['name'] ?? null,
                        'accountName' => $accountList[0]['accountName'] ?? null,
                        'accountNo' => $accountList[0]['accountNo'] ?? null,
                        'balanceAmount' => $data['balanceAmount'] ?? 0,
                        'walletStatus' => $data['walletStatus'] ?? null,
                        'accountList' => $accountList,
                    ]
                ];
            }
        }

        return [

            'status' => 'error',
            'code' => Response::HTTP_NOT_FOUND,
            'message' => 'Data not found',
            'data' => [
                'name' => null,
                'accountName' => null,
                'accountNo' => null,
                'balanceAmount' => 0,
                'walletStatus' => null,
                'accountList' => [],
            ]

        ];
    }

    public static function processApiCallingCardActivation($data)
    {
        // will be remove later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your account activation request was successful.',
            'prompt' => getPromptPath('account-activation-successful')
        ];

        /*return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Account activation failed.',
            'prompt' => getPromptPath('account-activation-failed')
        ];*/
        // will be remove later

        $url = config('api.base_url') . config('api.active_wallet_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => "for lost and reback customer",
            "refId" => $mobileNo . randomDigits()
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);
            // dd($data, intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success');
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => 'Your account activation request was successful.',
                    'prompt' => getPromptPath('account-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('account-activation-failed')
        ];
    }

    public function decrypt(Request $request)
    {
        $data = $request->input('data');
        $key = $request->input('key');
        // dd($request->all());

        // Instantiate the EncryptionHandler
        $encryption = new EncryptionHandler();

        // Get the hashed key from the secret key
        $secretKey = $key;
        $keyHashed = $encryption->getKeyHashed($secretKey);

        // Encrypted data
        $encryptedData = $data;

        // Decrypt the data
        $decryptedData = $encryption->decrypt($encryptedData, $keyHashed);

        // Output the decrypted data
        if ($decryptedData !== false) {
            return $this->sendResponse(['data' => $decryptedData], Response::HTTP_OK);
            // echo 'Decrypted Data: ' . $decryptedData;
        } else {
            return $this->sendError('Decryption failed.', Response::HTTP_EXPECTATION_FAILED);
        }

    }

    public static function callDynamicApi(Request $request)
    {
        $request->validate([
            'purpose' => 'required',
            'page' => 'required',
            'button' => 'required',
        ]);

        $purpose = strtoupper($request->input('purpose'));
        $phoneNumber = Session::get('logInfo')['otp_info']['otp_phone'];

        // Prepare data for API call
        $data = ['mobile_no' => $phoneNumber];

        // Call the dynamic API based on the purpose
        $apiResponse = self::processDynamicAPICalling($purpose, $data);

        // Prepare the response based on the API response
        $responseOut = [
            'code' => $apiResponse['code'],
            'status' => $apiResponse['status'],
            'message' => $apiResponse['message'],
            'prompt' => $apiResponse['prompt'] ?? null,
        ];

        // Additional handling for CARDACTIVATE purpose
        if ($purpose === "CARDACTIVATE") {
            $responseOut = ($apiResponse['code'] === Response::HTTP_OK && $apiResponse['status'] === 'success')
                ? [
                    'code' => $apiResponse['code'],
                    'status' => 'success',
                    'message' => 'Your account activation request was successful.',
                    'prompt' => $apiResponse['prompt']
                ]
                : [
                    'code' => $apiResponse['code'],
                    'status' => 'error',
                    'message' => 'Your account activation request has failed.',
                    'prompt' => $apiResponse['prompt']
                ];
        }

        return (new ApiController)->sendResponse($responseOut, $responseOut['code']);
    }

    public static function processDynamicAPICalling($purpose, $data = [])
    {
        switch ($purpose) {
            case 'CARDACTIVATE':
                return self::processApiCallingCardActivation($data);
            default:
                // Code to be executed if $purpose is different from all cases;
                return false;
        }
    }

}
