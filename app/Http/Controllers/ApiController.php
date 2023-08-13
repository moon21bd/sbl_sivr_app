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
        /*$mobileNo = '01710455990';
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
        return $this->sendResponse($responseOut, $responseOut['code']);*/
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
        /*$otpInfo = Session::get('otp');
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

        return $this->sendResponse($responseOut, $responseOut['code']);*/

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
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your issue has been successfully submitted.',
            'prompt' => getPromptPath('issue-submission-success'),
            'data' => [
                'issueId' => randomDigits(10),
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

}
