<?php

namespace App\Http\Controllers;

use App\Handlers\APIHandler;
use App\Handlers\EncryptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;


class ApiController extends ResponseController
{
    public function getBalance(Request $request)
    {
        // will be deleted this code later
        /*$responseOut = [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'balance' => 120
        ];
        return $this->sendResponse($responseOut);*/
        // will be deleted this code later

        $phoneNumber = Session::get('logInfo')['otp_info']['otp_phone'] ?? null;
        $response = self::fetchGetWalletDetails($phoneNumber);

        $responseOut = [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'balance' => 0
        ];

        if ($response['code'] === Response::HTTP_OK && $response['status'] === 'success') {
            $responseOut['code'] = Response::HTTP_OK;
            $responseOut['status'] = 'success';
            $responseOut['balance'] = number_format($response['data']['balanceAmount'], 2);
        }

        return $this->sendResponse($responseOut, $responseOut['code']);
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

            $isValidData = $this->decodeJsonIfValid($response['data']);
            if ($isValidData !== null) {
                $data = $this->decodeJsonIfValid($isValidData);
                $statusCode = intval($data['StatusCode']);
                if ($statusCode === Response::HTTP_BAD_REQUEST) {
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
                        'code' => Response::HTTP_EXPECTATION_FAILED,
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

        // WILL BE REMOVED LATER
        // call api to get user account name.
        $otpInfo = Session::get('otp');
        $statusCode = Response::HTTP_OK;
        $getAccountList = $this->fetchGetWalletDetails($otpInfo['otp_phone']);

        Session::put('logInfo', [
            'is_logged' => base64_encode(true),
            'otp_info' => $otpInfo,
            'account_info' => $getAccountList['data'],
        ]);

        Session::forget('otp');

        $responseOut = [
            'code' => $statusCode,
            'status' => 'success',
            'message' => 'Verification successful. Please proceed with your previous service request.',
            'prompt' => getPromptPath('account-verification-success'),
            'pn' => $mobileNo,
            'an' => $getAccountList['data']['accountName'] ?? null,
            'acn' => $getAccountList['data']['accountNo'] ?? null,
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
                    /*$getAccountList = self::fetchSavingsDeposits($otpInfo['otp_phone']);
                    Session::put('logInfo', [
                        'is_logged' => base64_encode(true),
                        'otp_info' => $otpInfo,
                        'account_info' => $getAccountList,
                    ]);*/

                    $getAccountList = $this->fetchGetWalletDetails($otpInfo['otp_phone']);

                    Session::put('logInfo', [
                        'is_logged' => base64_encode(true),
                        'otp_info' => $otpInfo,
                        'account_info' => $getAccountList['data'],
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
                    }

                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'success',
                        'message' => 'Success',
                        'pn' => $mobileNo,
                        'an' => $getAccountList[1]['AccountName'] ?? null,
                        'acn' => $getAccountList[1]['AccountNo'] ?? null,
                        'url' => url('/')
                    ];*/

                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'success',
                        'message' => 'Verification successful. Please proceed with your previous service request.',
                        'prompt' => getPromptPath('account-verification-success'),
                        'pn' => $mobileNo,
                        'an' => $getAccountList['data']['accountName'] ?? null,
                        'acn' => $getAccountList['data']['accountNo'] ?? null,
                        'url' => url('/')
                    ];

                    // Set the flash message
                    session()->flash('status', $responseOut['status']);
                    session()->flash('message', $responseOut['message']);

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

    public static function fetchSavingsDeposits($phoneNumber): array
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

    public static function fetchGetWalletDetails($phoneNumber): array
    {

        // will be removed this later
        return [
            'status' => 'success',
            'message' => 'Data Received',
            'code' => Response::HTTP_OK,
            'data' => [
                'name' => 'Md. Raqibul Hasan',
                'accountName' => 'Md. Raqibul Hasan',
                'accountNo' => '5158242353328',
                'balanceAmount' => 1000000,
                'walletStatus' => null,
                'accountList' => [
                    'name' => 'Md. Raqibul Hasan',
                    'accountName' => 'Md. Raqibul Hasan',
                    'accountNo' => '5158242353328',
                ],
            ]
        ];

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
            'code' => Response::HTTP_EXPECTATION_FAILED,
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

    public static function processApiCallingCardActivation($data): array
    {
        // dd($data);
        // will be remove later
        /*return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your account activation request was successful.',
            'prompt' => getPromptPath('account-activation-successful')
        ];*/

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

    public static function processApiCallingDebitCardActivation($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your debit card activation request was successful.',
            'prompt' => getPromptPath('debit-card-activation-successful')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Debit card activation failed.',
            'prompt' => getPromptPath('debit-card-activation-failed')
        ];

        // will be removed later

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
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => 'Your debit card activation request was successful.',
                    'prompt' => getPromptPath('debit-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your debit card activation request has failed.',
            'prompt' => getPromptPath('debit-card-activation-failed')
        ];
    }

    public static function processApiCallingCreditCardActivation($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your credit card activation request was successful.',
            'prompt' => getPromptPath('credit-card-activation-successful')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Your credit card activation failed.',
            'prompt' => getPromptPath('credit-card-activation-failed')
        ];
        // will be removed later

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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('credit-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('credit-card-activation-failed')
        ];
    }

    public static function processApiCallingPrepaidCardActivation($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your prepaid card activation request was successful.',
            'prompt' => getPromptPath('prepaid-card-activation-successful')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Prepaid card activation failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];
        // will be removed later

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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];
    }

    public static function processApiCallingChequeBookLeaf($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your cheque book leaf request was successful.',
            'prompt' => getPromptPath('cheque-book-leaf-request-successful')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Cheque book leaf request failed.',
            'prompt' => getPromptPath('cheque-book-leaf-request-failed')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingCASAActivateSMSBanking($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your SMS banking activation request was successful.',
            'prompt' => getPromptPath('sms-banking-activate-request-successful')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'SMS banking activation request failed.',
            'prompt' => getPromptPath('sms-banking-activate-request-failed')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingLADueDateInstallment($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your Due date installment request was successful.',
            'prompt' => getPromptPath('voice-for-la-due-date-installment-request-successful-en')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Due date installment request failed.',
            'prompt' => getPromptPath('voice-for-la-due-date-installment-request-failed-en')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingLALoanDetails($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your request was successful.',
            'prompt' => getPromptPath('voice-for-la-loan-details-request-successful-en')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Your request has failed.',
            'prompt' => getPromptPath('voice-for-la-loan-details-request-failed-en')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingLAOutstandingLoanBalance($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your Outstanding loan balance request was successful.',
            'prompt' => getPromptPath('voice-for-la-loan-details-request-successful-en')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Outstanding loan balance request failed.',
            'prompt' => getPromptPath('voice-for-la-loan-details-request-failed-en')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingFDFixedDepositDetails($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your request was successful.',
            'prompt' => getPromptPath('voice-for-fd-fixed-deposit-details-request-successful-en')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Outstanding loan balance request failed.',
            'prompt' => getPromptPath('voice-for-fd-fixed-deposit-details-request-failed-en')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingFDMaturityDate($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your request was successful.',
            'prompt' => getPromptPath('voice-for-fd-maturity-date-request-successful-en')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Your request has failed.',
            'prompt' => getPromptPath('voice-for-fd-maturity-date-request-failed-en')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingEWChangeOrResetEWalletPIN($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your change or reset PIN request was successful.',
            'prompt' => getPromptPath('ew-change-reset-pin-request-successful')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'change or reset PIN request failed.',
            'prompt' => getPromptPath('ew-change-reset-pin-request-failed')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingEWDeviceBind($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your device bind request was successful.',
            'prompt' => getPromptPath('ew-device-bind-request-successful')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'change or reset PIN request failed.',
            'prompt' => getPromptPath('ew-device-bind-request-failed')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingEWLockBlock($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your eWallet Lock or Block request was successful.',
            'prompt' => getPromptPath('ew-lock-block-request-successful')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'eWallet Lock or Block request failed.',
            'prompt' => getPromptPath('ew-lock-block-request-failed')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingEWUnlockActive($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your eWallet Unlock/Active request was successful.',
            'prompt' => getPromptPath('ew-unlock-active-request-successful')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'eWallet Unlock/Active request failed.',
            'prompt' => getPromptPath('ew-unlock-active-request-failed')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingCASAAvailableBalance($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your request was successful.',
            'prompt' => getPromptPath('voice-for-casa-available-balance-request-successful-en')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'eWallet Unlock/Active request failed.',
            'prompt' => getPromptPath('voice-for-casa-available-balance-request-failed-en')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingCASAMiniStatement($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your request was successful.',
            'prompt' => getPromptPath('voice-for-casa-mini-statement-request-successful-en')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Your has request failed.',
            'prompt' => getPromptPath('voice-for-casa-mini-statement-request-failed-en')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingALAccountDPSAvailableBalance($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your request was successful.',
            'prompt' => getPromptPath('voice-for-al-account-dps-available-balance-request-successful-en')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Your has request failed.',
            'prompt' => getPromptPath('voice-for-al-account-dps-available-balance-request-failed-en')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingALDPSDetails($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your request was successful.',
            'prompt' => getPromptPath('voice-for-al-dps-details-request-successful-en')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Your request has failed.',
            'prompt' => getPromptPath('voice-for-al-dps-details-request-failed-en')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingALAccountDPSInstalmentDetails($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your request was successful.',
            'prompt' => getPromptPath('voice-for-al-account-dps-instalment-details-request-successful-en')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Your request has failed.',
            'prompt' => getPromptPath('voice-for-al-account-dps-instalment-details-request-failed-en')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processApiCallingIBARChequeBookLeafStopPaymentClick($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your cheque book leaf stop payment request was successful.',
            'prompt' => getPromptPath('ew-cheque-book-stop-payment-request-successful')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'eWallet Unlock/Active request failed.',
            'prompt' => getPromptPath('ew-cheque-book-stop-payment-request-failed')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processGetDropDownValues($data)
    {
        $callType = self::getDropDownForCallTypeApi();
        $callCategory = self::getDropDownForCallCategoryApi();

        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Data Found.',
            'prompt' => "",
            'data' => [
                'callType' => $callType,
                'callCategory' => $callCategory,
            ]
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'eWallet Unlock/Active request failed.',
            'prompt' => getPromptPath('ew-cheque-book-stop-payment-request-failed')
        ];
        // will be removed later

        /*$url = config('api.base_url') . config('api.active_wallet_url');
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
                    'message' => 'Your credit card activation request was successful.',
                    'prompt' => getPromptPath('prepaid-card-activation-successful')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed')
        ];*/
    }

    public static function processToCreateTicketInCRM($data)
    {
        self::defaultResponseForProcessToCreateTicketInCRM();

        $accessToken = Cache::get('crm_access_token');

        if (!$accessToken) {
            $accessToken = self::generateCRMLoginToken();
            if (false === $accessToken) {
                return [
                    'code' => Response::HTTP_EXPECTATION_FAILED,
                    'status' => 'failed',
                    'message' => 'Data not found.',
                    'prompt' => getPromptPath('issue-submission-failed'),
                    'data' => [
                        'ticketId' => null,
                        'ticketMessage' => null,
                    ]
                ];
            }
        }

        $url = config('api.crm_ticket_base_url') . config('api.crm_ticket_create_url');
        $apiHandler = new APIHandler();
        $response = $apiHandler->doPostCall($url, $data, [
            'Authorization' => 'Bearer ' . $accessToken,
        ]);

        if ($response['statusCode'] >= 200 && $response['statusCode'] <= 299) {
            $responseData = json_decode($response['data'], true);

            $ticketId = $responseData['data']['id'];
            $message = $responseData['data']['message'];

            return [
                'code' => Response::HTTP_OK,
                'status' => 'success',
                'message' => 'Data Found.',
                'prompt' => getPromptPath('issue-submission-success'),
                'data' => [
                    'ticketId' => $ticketId,
                    'ticketMessage' => $message,
                ]
            ];
        }

        // If unauthorized or internal server error, try to refresh the token and make the API call again
        if ($response['statusCode'] === Response::HTTP_UNAUTHORIZED || $response['statusCode'] === Response::HTTP_INTERNAL_SERVER_ERROR) {
            $accessToken = self::generateCRMLoginToken();
            if (false !== $accessToken) {
                $response = $apiHandler->doPostCall($url, $data, [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]);
                $responseData = json_decode($response['data'], true);
                $ticketId = $responseData['data']['id'];
                $message = $responseData['data']['message'];

                return [
                    'code' => Response::HTTP_OK,
                    'status' => 'success',
                    'message' => 'Data Found.',
                    'prompt' => getPromptPath('issue-submission-success'),
                    'data' => [
                        'ticketId' => $ticketId,
                        'ticketMessage' => $message,
                    ]
                ];
            }
        }

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'failed',
            'message' => 'Data not found.',
            'prompt' => getPromptPath('issue-submission-failed'),
            'data' => [
                'ticketId' => null,
                'ticketMessage' => null,
            ]
        ];
    }

    public static function defaultResponseForProcessToCreateTicketInCRM()
    {
        $response = '{
    "success": true,
    "data": {
        "id": "S1412061660",
        "message": "Dear Sir,Your Request/Query has been resolved successfully.Your tracking ID is #S1412061660.Thank you for Banking with Sonali Bank.For details Call 16639"
    },
    "message": null
}';

        $responseData = json_decode($response, true);
        $ticketId = $responseData['data']['id'];
        $message = $responseData['data']['message'];
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Data Found.',
            'prompt' => "",
            'data' => [
                'ticketId' => $ticketId,
                'ticketMessage' => $message,
            ]
        ];
    }

    public static function getDropDownForCallCategoryApi()
    {
        // will be removed later
        self::dummyResponseForGetDropDownForCallCategoryApi();

        $url = config('api.crm_ticket_base_url') . config('api.crm_ticket_call_category_url');
        $apiHandler = new APIHandler();
        $responseData = $apiHandler->doGetCall($url, [], [
            'Authorization' => 'Bearer ' . config('api.crm_ticket_authorization_token'),
        ]);

        $options = [];
        if ($responseData['status'] === 'success' && $responseData['statusCode'] === 200) {
            $response = json_decode($responseData['data'], true);

            if ($response['success']) {
                $callCategories = $response['data'] ?? [];
                foreach ($callCategories as $category) {
                    $options[$category['id'] . "|" . $category['call_type_id']] = $category['name'];
                }
            }

            return $options;
        }
        return $options;

    }

    public static function dummyResponseForGetDropDownForCallCategoryApi()
    {
        $dummyResponse = '{
    "success": true,
    "data": [
        {
            "id": "1",
            "name": "Card",
            "call_type_id": "4"
        },
        {
            "id": "2",
            "name": "BEFTN/RTGS/BACH/NPSB",
            "call_type_id": "3"
        },
        {
            "id": "3",
            "name": "BEFTN/RTGS/BACH/NPSB",
            "call_type_id": "2"
        },
        {
            "id": "4",
            "name": "BEFTN/RTGS/BACH/NPSB",
            "call_type_id": "1"
        },
        {
            "id": "5",
            "name": "Agent Banking",
            "call_type_id": "3"
        },
        {
            "id": "6",
            "name": "Agent Banking",
            "call_type_id": "2"
        },
        {
            "id": "8",
            "name": "Agent Banking",
            "call_type_id": "1"
        },
        {
            "id": "9",
            "name": "Bhata",
            "call_type_id": "3"
        },
        {
            "id": "10",
            "name": "Mobile App",
            "call_type_id": "4"
        },
        {
            "id": "11",
            "name": "Card",
            "call_type_id": "3"
        },
        {
            "id": "12",
            "name": "Challan/e-challan/A-challan",
            "call_type_id": "3"
        },
        {
            "id": "13",
            "name": "Challan/e-challan/A-challan",
            "call_type_id": "2"
        },
        {
            "id": "14",
            "name": "Challan/e-challan/A-challan",
            "call_type_id": "1"
        },
        {
            "id": "15",
            "name": "Retail Banking",
            "call_type_id": "3"
        },
        {
            "id": "16",
            "name": "Card",
            "call_type_id": "1"
        },
        {
            "id": "17",
            "name": "Card",
            "call_type_id": "2"
        },
        {
            "id": "18",
            "name": "Prank",
            "call_type_id": "3"
        },
        {
            "id": "19",
            "name": "Others",
            "call_type_id": "3"
        },
        {
            "id": "20",
            "name": "Open SR",
            "call_type_id": "3"
        },
        {
            "id": "21",
            "name": "System",
            "call_type_id": "3"
        },
        {
            "id": "22",
            "name": "Nagorik Sheba",
            "call_type_id": "3"
        },
        {
            "id": "23",
            "name": "Mobile banking",
            "call_type_id": "3"
        },
        {
            "id": "24",
            "name": "LGSP",
            "call_type_id": "3"
        },
        {
            "id": "25",
            "name": "Internet Banking (Upcoming)",
            "call_type_id": "3"
        },
        {
            "id": "26",
            "name": "Digital Banking (Upcoming)",
            "call_type_id": "3"
        },
        {
            "id": "27",
            "name": "Corporate Banking",
            "call_type_id": "3"
        },
        {
            "id": "28",
            "name": "Pension",
            "call_type_id": "3"
        },
        {
            "id": "29",
            "name": "Utility Payments",
            "call_type_id": "3"
        },
        {
            "id": "30",
            "name": "Utility Payments",
            "call_type_id": "1"
        },
        {
            "id": "31",
            "name": "ITFD",
            "call_type_id": "2"
        },
        {
            "id": "32",
            "name": "SME Covid-19 Packages",
            "call_type_id": "3"
        },
        {
            "id": "33",
            "name": "Food Procurement",
            "call_type_id": "3"
        },
        {
            "id": "34",
            "name": "SMS Service",
            "call_type_id": "3"
        },
        {
            "id": "35",
            "name": "SMS Service",
            "call_type_id": "1"
        },
        {
            "id": "36",
            "name": "SMS Service",
            "call_type_id": "2"
        },
        {
            "id": "37",
            "name": "Govt. Service",
            "call_type_id": "3"
        },
        {
            "id": "38",
            "name": "IPFD",
            "call_type_id": "3"
        },
        {
            "id": "39",
            "name": "IPFD",
            "call_type_id": "2"
        },
        {
            "id": "40",
            "name": "Retail Banking",
            "call_type_id": "2"
        },
        {
            "id": "41",
            "name": "MFS-Link Account",
            "call_type_id": "3"
        },
        {
            "id": "42",
            "name": "MFS-Link Account",
            "call_type_id": "1"
        },
        {
            "id": "43",
            "name": "MFS-Link Account",
            "call_type_id": "2"
        },
        {
            "id": "44",
            "name": "T.Bill/T.Bond/Sukuk",
            "call_type_id": "3"
        },
        {
            "id": "45",
            "name": "General Loan",
            "call_type_id": "3"
        },
        {
            "id": "46",
            "name": "Import/Export/Others",
            "call_type_id": "3"
        },
        {
            "id": "47",
            "name": "Sanchaypatra",
            "call_type_id": "3"
        },
        {
            "id": "48",
            "name": "Sanchaypatra",
            "call_type_id": "2"
        },
        {
            "id": "49",
            "name": "US Bond",
            "call_type_id": "3"
        },
        {
            "id": "50",
            "name": "General Loan",
            "call_type_id": "2"
        },
        {
            "id": "51",
            "name": "Micro Credit",
            "call_type_id": "3"
        },
        {
            "id": "52",
            "name": "Online Service",
            "call_type_id": "3"
        },
        {
            "id": "53",
            "name": "Micro Credit",
            "call_type_id": "2"
        },
        {
            "id": "54",
            "name": "SME",
            "call_type_id": "3"
        },
        {
            "id": "55",
            "name": "Online Service",
            "call_type_id": "1"
        },
        {
            "id": "56",
            "name": "Rural Credit",
            "call_type_id": "3"
        },
        {
            "id": "57",
            "name": "Rural Credit",
            "call_type_id": "2"
        },
        {
            "id": "58",
            "name": "SME",
            "call_type_id": "2"
        },
        {
            "id": "60",
            "name": "Online Service",
            "call_type_id": "2"
        },
        {
            "id": "61",
            "name": "Foreign Remittance",
            "call_type_id": "3"
        },
        {
            "id": "62",
            "name": "Mobile App",
            "call_type_id": "3"
        },
        {
            "id": "63",
            "name": "Mobile App",
            "call_type_id": "2"
        },
        {
            "id": "64",
            "name": "Mobile App",
            "call_type_id": "1"
        },
        {
            "id": "65",
            "name": "Islamic banking",
            "call_type_id": "3"
        },
        {
            "id": "66",
            "name": "Islamic banking",
            "call_type_id": "2"
        },
        {
            "id": "67",
            "name": "SME",
            "call_type_id": "5"
        },
        {
            "id": "68",
            "name": "Agent Banking",
            "call_type_id": "5"
        },
        {
            "id": "69",
            "name": "Card",
            "call_type_id": "5"
        },
        {
            "id": "70",
            "name": "Import/Export/Others",
            "call_type_id": "5"
        },
        {
            "id": "71",
            "name": "Islamic banking",
            "call_type_id": "5"
        },
        {
            "id": "72",
            "name": "Loan",
            "call_type_id": "5"
        },
        {
            "id": "73",
            "name": "Retail Banking",
            "call_type_id": "5"
        },
        {
            "id": "74",
            "name": "Rural and Micro Credit",
            "call_type_id": "5"
        },
        {
            "id": "75",
            "name": "Foreign Remittance",
            "call_type_id": "2"
        },
        {
            "id": "76",
            "name": "PO/DD/FDD",
            "call_type_id": "2"
        },
        {
            "id": "77",
            "name": "Customer Complaint",
            "call_type_id": "2"
        },
        {
            "id": "78",
            "name": "Bank Solvency Certificate",
            "call_type_id": "2"
        },
        {
            "id": "79",
            "name": "Schedule Payment Request",
            "call_type_id": "2"
        },
        {
            "id": "80",
            "name": "Auto debit loan repayment",
            "call_type_id": "2"
        },
        {
            "id": "81",
            "name": "PO/DD/FDD",
            "call_type_id": "3"
        },
        {
            "id": "82",
            "name": "Universal Pension Scheme",
            "call_type_id": "3"
        }
    ],
    "message": null
}';
        $response = json_decode($dummyResponse, true);

        $callCategories = $response['data'] ?? [];
        $options = [];
        foreach ($callCategories as $category) {
            $options[$category['id'] . "|" . $category['call_type_id']] = $category['name'];
        }

        return $options;

    }

    public static function getDropDownForCallTypeApi()
    {
        // will be removed later
        self::dummyResponseForGetDropDownForCallTypeApi();

        $url = config('api.crm_ticket_base_url') . config('api.crm_ticket_call_type_url');
        $apiHandler = new APIHandler();
        $responseData = $apiHandler->doGetCall($url, [], [
            'Authorization' => 'Bearer ' . config('api.crm_ticket_authorization_token'),
        ]);

        $options = [];
        if ($responseData['status'] === 'success' && $responseData['statusCode'] === 200) {
            $response = json_decode($responseData['data'], true);

            if ($response['success']) {
                $callTypes = $response['data'] ?? [];
                foreach ($callTypes as $type) {
                    $options[$type['id']] = $type['name'];
                }
            }

            return $options;
        }
        return $options;

    }

    public static function dummyResponseForGetDropDownForCallTypeApi()
    {
        $dummyResponse = '{
    "success": true,
    "data": [
        {
            "id": "1",
            "name": "Complaint"
        },
        {
            "id": "2",
            "name": "Service Request"
        },
        {
            "id": "3",
            "name": "Query"
        },
        {
            "id": "4",
            "name": "Request"
        },
        {
            "id": "5",
            "name": "Lead"
        }
    ],
    "message": null
}';
        $response = json_decode($dummyResponse, true);

        $callTypes = $response['data'] ?? [];
        $options = [];
        foreach ($callTypes as $type) {
            $options[$type['id']] = $type['name'];
        }

        return $options;
    }

    public static function processApiCallingResetPin($data): array
    {

        /*return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'PIN reset request successful.',
            'prompt' => getPromptPath('pin-reset-success')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'PIN reset request failed.',
            'prompt' => getPromptPath('pin-reset-failed')
        ];*/

        // dd($data);
        // will be remove later
        /*return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your account activation request was successful.',
            'prompt' => getPromptPath('account-activation-successful')
        ];*/

        /*return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Account activation failed.',
            'prompt' => getPromptPath('account-activation-failed')
        ];*/
        // will be remove later

        $url = config('api.base_url') . config('api.get_pin_reset_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $data['reason'] ?? "Get new phone",
            "refId" => randomDigits(5)
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);
            // dd($data, intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success');
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => 'PIN reset request successful.',
                    'prompt' => getPromptPath('pin-reset-success')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'PIN reset request failed.',
            'prompt' => getPromptPath('pin-reset-failed')
        ];
    }

    public static function processApiCallingDeviceBind($data): array
    {
        // will be removed later
        /*return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your device binding request was successful.',
            'prompt' => getPromptPath('device-bind-success')
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Your device binding request failed.',
            'prompt' => getPromptPath('device-bind-failed')
        ];*/

        // will be removed later

        $url = config('api.base_url') . config('api.device_bind_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $data['reason'] ?? "Get new phone",
            "refId" => randomDigits(5)
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);
            // dd($data, intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success');
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => 'Your device binding request was successful.',
                    'prompt' => getPromptPath('device-bind-success')
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your device binding request failed.',
            'prompt' => getPromptPath('device-bind-failed')
        ];
    }

    public static function processApiCallingCreateIssue($data): array
    {
        $callTypeId = $data['callType'];
        $callCategoryIdInfo = $data['callCategory']; // "9|3" here the first one is category id and the second one is call_type_id
        list($callCategoryId, $callCategoryTypeId) = explode("|", $callCategoryIdInfo);

        $appParamsData = [
            'channel_id' => 1,
            'idesk_agent_id' => 1,
            'cus_name' => 'Test Development Ticket',
            // 'cus_contact_no' => '01770430605',
            'call_type' => $callTypeId,
            'call_category' => $callCategoryId,
            'call_sub_category' => 2, // Need to talk about this field
            'call_sub_subcategory' => 2, // Need to talk about this field
            'account_no' => null, // or you can set a default value if needed
            // 'idesk_agent_name' => 'testName',
            // 'employee_id' => '11223344',
        ];

        $apiResponse = self::processToCreateTicketInCRM($appParamsData);
        $ticketId = $apiResponse['data']['ticketId'];
        $ticketMessage = $apiResponse['data']['ticketMessage'];

        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your issue has been successfully submitted.',
            'prompt' => getPromptPath('issue-submission-success'),
            'data' => [
                'issueId' => $ticketId,
            ]
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Your issue submission has failed. Please try again later.',
            'prompt' => getPromptPath('issue-submission-failed'),
            'issueId' => null
        ];

        // will be removed later

        $url = config('api.base_url') . config('api.create_issue_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "reason" => $data['reason'] ?? "Having a problem with my phone.",
            // "OtpCode" => "",
            "refId" => randomDigits(5)
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === Response::HTTP_OK) {
            $data = json_decode($response['data'], true);
            // dd($data, intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success');
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => 'Your issue has been successfully submitted...',
                    'prompt' => getPromptPath('issue-submission-success'),
                    'data' => [
                        'issueId' => $data['issueId'] ?? null
                    ]
                ];

            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your issue submission has failed. Please try again later.',
            'prompt' => getPromptPath('issue-submission-failed'),
            'issueId' => null
        ];
    }

    public static function processApiCallingLockWallet($data): array
    {
        // will be removed later
        /*return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your issue has been successfully submitted.',
            'prompt' => getPromptPath('issue-submission-success'),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Your issue submission has failed. Please try again later.',
            'prompt' => getPromptPath('issue-submission-failed'),
            'issueId' => null
        ];*/

        // will be removed later

        $url = config('api.base_url') . config('api.lock_wallet_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "reason" => $data['reason'] ?? "Having a problem with my phone.",
            // "OtpCode" => "",
            "refId" => randomDigits(5)
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === Response::HTTP_OK) {
            $data = json_decode($response['data'], true);

            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => 'Your wallet locking request has been successfully accepted.',
                    'prompt' => getPromptPath('lock-wallet-submission-success'),
                ];

            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your wallet lock request was unsuccessful.',
            'prompt' => getPromptPath('lock-wallet-submission-failed'),
        ];
    }

    public static function callDynamicApi(Request $request)
    {
        $request->validate([
            'purpose' => 'required',
            'page' => 'required',
            'button' => 'required',
        ]);

        $purpose = strtoupper($request->input('purpose'));
        $phoneNumber = Session::get('logInfo')['otp_info']['otp_phone'] ?? null;


        // Prepare data for API call
        // we need to add/pass additional data to array_merge 's first param
        $data = array_merge(['mobile_no' => $phoneNumber], $request->all());

        // Call the dynamic API based on the purpose
        $apiResponse = self::processDynamicAPICalling($purpose, $data);
        // Prepare the response based on the API response
        $responseOut = [
            'code' => $apiResponse['code'],
            'status' => $apiResponse['status'],
            'message' => $apiResponse['message'],
            'prompt' => $apiResponse['prompt'] ?? null
        ];

        if (!empty($apiResponse['data'])) {
            $responseOut['data'] = $apiResponse['data'];
        }

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
            case 'RESETPIN':
                return self::processApiCallingResetPin($data);
            case 'DEVICEBIND':
                return self::processApiCallingDeviceBind($data);
            case 'CREATEISSUE':
                return self::processApiCallingCreateIssue($data);
            case 'LOCKWALLET':
                return self::processApiCallingLockWallet($data);
            case 'DEBITCARDACTIVATION':
                return self::processApiCallingDebitCardActivation($data);
            case 'CREDITCARDACTIVATION':
                return self::processApiCallingCreditCardActivation($data);
            case 'PREPAIDCARDACTIVATION':
                return self::processApiCallingPrepaidCardActivation($data);
            case 'CHEQUEBOOKLEAF':
                return self::processApiCallingChequeBookLeaf($data);
            case 'CASAACTIVATESMSBANKING':
                return self::processApiCallingCASAActivateSMSBanking($data);
            case 'LA-DUE-DATE-INSTALLMENT':
                return self::processApiCallingLADueDateInstallment($data);
            case 'LA-LOAN-DETAILS':
                return self::processApiCallingLALoanDetails($data);
            case 'LA-OUTSTANDING-LOAN-BALANCE':
                return self::processApiCallingLAOutstandingLoanBalance($data);
            case 'FD-FIXED-DEPOSIT-DETAILS':
                return self::processApiCallingFDFixedDepositDetails($data);
            case 'FD-MATURITY-DATE':
                return self::processApiCallingFDMaturityDate($data);
            case 'EW-CHANGE-OR-RESET-PIN':
                return self::processApiCallingEWChangeOrResetEWalletPIN($data);
            case 'EW-DEVICE-BIND':
                return self::processApiCallingEWDeviceBind($data);
            case 'EW-LOCK-BLOCK':
                return self::processApiCallingEWLockBlock($data);
            case 'EW-UNLOCK-ACTIVE':
                return self::processApiCallingEWUnlockActive($data);
            case 'CASA-AVAILABLE-BALANCE':
                return self::processApiCallingCASAAvailableBalance($data);
            case 'CASA-MINI-STATEMENT':
                return self::processApiCallingCASAMiniStatement($data);
            case 'AL-ACCOUNT-DPS-AVAILABLE-BALANCE':
                return self::processApiCallingALAccountDPSAvailableBalance($data);
            case 'AL-DPS-DETAILS':
                return self::processApiCallingALDPSDetails($data);
            case 'AL-ACCOUNT-DPS-INSTALMENT-DETAILS':
                return self::processApiCallingALAccountDPSInstalmentDetails($data);
//            case 'LA-DUE-DATE-INSTALLMENT':
//                return self::processApiCallingLADueDateInstallment($data);
            case 'IB-AR-CHEQUE-BOOK-LEAF-STOP-PAYMENT':
                return self::processApiCallingIBARChequeBookLeafStopPaymentClick($data);
            case 'GET-DROP-DOWN-VALUES':
                return self::processGetDropDownValues($data);
            default:
                // Code to be executed if $purpose is different from all cases;
                return false;
        }
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

    public static function generateCRMLoginToken()
    {
        $url = config('api.crm_ticket_base_url') . config('api.crm_ticket_login_url');
        $apiHandler = new APIHandler();

        $loginData = config('api.crm_ticket_login_info');
        $response = $apiHandler->postCall($url, $loginData);

        if ($response['status'] === 'success' && $response['statusCode'] === Response::HTTP_OK) {
            $data = json_decode($response['data'], true);

            if ($data['success'] && !empty($data['data']['token'])) {
                Cache::put('crm_access_token', $data['data']['token'], now()->addMinutes(120));

                return $data['data']['token'];
            }
        } else {
            Log::error('CRM-LOGIN-API-ERROR : ' . json_encode($response['data']));
            return false;
        }

    }
}
