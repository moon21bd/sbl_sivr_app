<?php

namespace App\Http\Controllers;

use App\Handlers\APIHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class ApiController extends ResponseController
{

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
        // $mobileNo = '01293495606'; // will be removed later

        $apiHandler = new APIHandler();
        $url = config('api.base_url') . config('api.send_otp_url');
        $strRefId = $mobileNo . $this->randomDigits();
        $response = $apiHandler->postCall($url, [
            'strRefId' => $strRefId,
            'strMobileNo' => $mobileNo,
            'isEncPwd' => true,
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) { // successful api response found from apihandler end.

            Log::info('API RESPONSE:: ' . json_encode($response['data']));

            $isValidData = $this->decodeJsonIfValid($response['data']);
            if ($isValidData !== null) {
                $data = $this->decodeJsonIfValid($isValidData);
                $statusCode = intval($data['StatusCode']);
                if ($statusCode === 400) {
                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'error',
                        'message' => 'The phone number entered is invalid or an unexpected error has occurred.'
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);

                } else if ($statusCode === Response::HTTP_OK) {
                    Session::put('otp', [
                        'otp_phone' => $mobileNo,
                        'strRefId' => $strRefId
                    ]);

                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'success',
                        'message' => 'Apologies, something went wrong. Please try again later.',
                        'url' => url('verify-otp')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);

                } else {
                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'error',
                        'message' => 'Apologies, something went wrong. Please try again later.'
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                }

            } else {
                $responseOut = [
                    'code' => Response::HTTP_EXPECTATION_FAILED,
                    'status' => 'error',
                    'message' => 'Null response'
                ];
                return $this->sendResponse($responseOut, $responseOut['code']);
            }

        } else {

            $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
            Log::error('API ERROR:: ' . $msg);
            $responseOut = [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => 'Apologies, something went wrong. Please try again later.'
            ];
            return $this->sendResponse($responseOut, $responseOut['code']);
        }


    }

    public function verifyOtpWrapper(Request $request)
    {
        $request->validate([
            'code' => ['required', 'integer', 'numeric'],
        ]);

        $mobileNo = Session::get('otp.otp_phone');
        $strRefId = Session::get('otp.strRefId');

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
                        'message' => 'Apologies, something went wrong. Please try again later.'
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                } else {
                    // After Verification success
                    // Make the user as logged-in user, set flag to verify user.
                    // call api to get user account name.
                    $otpInfo = Session::get('otp');
                    $getAccountList = $this->fetchSavingsDeposits($otpInfo);
                    Session::put('logInfo', [
                        'is_logged' => base64_encode(true),
                        'otp_info' => $otpInfo,
                        'account_info' => $getAccountList,
                    ]);
                    Session::forget('otp');

                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'success',
                        'message' => 'Success',
                        'url' => url('/')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                }
            } else {
                $responseOut = [
                    'code' => $statusCode,
                    'status' => 'error',
                    'message' => 'Apologies, something went wrong. Please try again later.'
                ];
                return $this->sendResponse($responseOut, $responseOut['code']);
            }

        } else {

            $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
            Log::error('API ERROR:: ' . $msg);
            $responseOut = [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => 'Apologies, something went wrong. Please try again later.'
            ];
            return $this->sendResponse($responseOut, $responseOut['code']);
        }

    }

    protected function fetchSavingsDeposits($otpInfo)
    {
        $url = config('api.base_url') . config('api.get_account_list_url');
        $apiHandler = new APIHandler();
        $response = $apiHandler->postCall($url, ['MobileNo' => $otpInfo['otp_phone']]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = $this->decodeJsonIfValid($response['data']);
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

}
