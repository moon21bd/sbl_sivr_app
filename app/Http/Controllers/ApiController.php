<?php

namespace App\Http\Controllers;

use App\Handlers\APIHandler;
use App\Handlers\EncryptionHandler;
use App\Models\OtpHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;


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

        $phoneNumber = Session::get('logInfo.otp_info.otp_phone') ?? null;
        $response = self::fetchGetWalletDetails($phoneNumber);
        $responseOut = [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'balance' => 0
        ];

        if ($response['code'] === Response::HTTP_OK && $response['status'] === 'success') {
            $responseOut['code'] = $response['code'];
            $responseOut['status'] = 'success';
            $responseOut['balance'] = number_format($response['data']['balanceAmount'] ?? 0, 2);
        }

        return $this->sendResponse($responseOut, $responseOut['code']);
    }

    /*public function sendOtpWrapper(Request $request)
    {
        $request->validate([
            'mobile_no' => $this->phoneValidationRules()
        ], $this->phoneValidationErrorMessages());

        $mobileNo = $request->input('mobile_no');

        // will be removed later
//        $mobileNo = '01710455990';
//        $strRefId = $mobileNo . randomDigits();
//        Session::put('otp', [
//            'phone_masked' => $this->hidePhoneNumber($mobileNo),
//            'otp_phone' => $mobileNo,
//            'strRefId' => $strRefId
//        ]);
//
//        $responseOut = [
//            'code' => Response::HTTP_OK,
//            'status' => 'success',
//            'message' => 'Success.',
//            'url' => url('verify-otp')
//        ];
//        return $this->sendResponse($responseOut, $responseOut['code']);
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
                        'message' => __('messages.entered-phone-number-invalid'),
                        'prompt' => null
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
                        'message' => __('messages.apologies-something-went-wrong'),
                        'prompt' => getPromptPath('common/request-failed-en')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                }

            } else {
                $responseOut = [
                    'code' => Response::HTTP_EXPECTATION_FAILED,
                    'status' => 'error',
                    'message' => __('messages.apologies-something-went-wrong'), // Null response
                    'prompt' => getPromptPath('common/request-failed-en')
                ];
                return $this->sendResponse($responseOut, $responseOut['code']);
            }

        } else {

            $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
            Log::error('API ERROR:: ' . $msg);
            $responseOut = [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => __('messages.apologies-something-went-wrong'),
                'prompt' => getPromptPath('common/request-failed-en')
            ];
            return $this->sendResponse($responseOut, $responseOut['code']);
        }


    }*/

    public function sendOtpWrapper(Request $request)
    {
        $request->validate([
            'mobile_no' => $this->phoneValidationRules()
        ], $this->phoneValidationErrorMessages());

        $mobileNo = $request->input('mobile_no');
        $response = $this->sendOtp($mobileNo);

        return new JsonResponse($response->getData(true), $response->getStatusCode());
    }

    public function resendOtp(Request $request)
    {

        /*$request->validate([
            'mobile_no' => $this->phoneValidationRules()
        ], $this->phoneValidationErrorMessages());
        $mobileNo = $request->input('mobile_no');*/

        $mobileNo = data_get(Session::get('otp'), 'otp_phone', "NA");
        $response = $this->sendOtp($mobileNo, true);

        return new JsonResponse($response->getData(true), $response->getStatusCode());
    }

    private function sendOtp($mobileNo, $isResend = false)
    {
        $apiHandler = new APIHandler();
        $url = config('api.base_url') . config('api.send_otp_url');
        $strRefId = $mobileNo . randomDigits();

        $apiPayload = [
            'strRefId' => $strRefId,
            'strMobileNo' => $mobileNo,
            'isEncPwd' => true,
        ];

        if ($isResend) {
            Log::info('RE-SEND-OT-API-CALLED : ' . json_encode($apiPayload));
        }

        $response = $apiHandler->postCall($url, $apiPayload);

        if ($response['status'] === 'success' && $response['statusCode'] === Response::HTTP_OK) {
            $isValidData = $this->decodeJsonIfValid($response['data']);
            if ($isValidData !== null) {
                $data = $this->decodeJsonIfValid($isValidData);
                $statusCode = intval($data['StatusCode']);
                if ($statusCode === Response::HTTP_BAD_REQUEST) {
                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'error',
                        'message' => __('messages.entered-phone-number-invalid'),
                        'prompt' => null
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                } elseif ($statusCode === Response::HTTP_OK) {
                    Session::put('otp', [
                        'phone_masked' => $this->hidePhoneNumber($mobileNo),
                        'otp_phone' => $mobileNo,
                        'strRefId' => $strRefId
                    ]);

                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'success',
                        'message' => __('messages.otp-send-success'),
                        'url' => url('verify-otp')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                } else {
                    $responseOut = [
                        'code' => Response::HTTP_EXPECTATION_FAILED,
                        'status' => 'error',
                        'message' => __('messages.apologies-something-went-wrong'),
                        'prompt' => getPromptPath('common/request-failed-en')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                }
            } else {
                $responseOut = [
                    'code' => Response::HTTP_EXPECTATION_FAILED,
                    'status' => 'error',
                    'message' => __('messages.apologies-something-went-wrong'), // Null response
                    'prompt' => getPromptPath('common/request-failed-en')
                ];
                return $this->sendResponse($responseOut, $responseOut['code']);
            }
        } else {
            $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
            Log::error('API ERROR:: ' . $msg);
            $responseOut = [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => __('messages.apologies-something-went-wrong'),
                'prompt' => getPromptPath('common/request-failed-en')
            ];
            return $this->sendResponse($responseOut, $responseOut['code']);
        }
    }

    public function sendOtpWrapperNew(Request $request)
    {
        $request->validate([
            'mobile_no' => $this->phoneValidationRules()
        ], $this->phoneValidationErrorMessages());

        $mobileNo = $request->input('mobile_no');
        $otpHistory = OtpHistory::where('phone_number', $mobileNo)->first();
        $otpStatus = self::isOtpSendingAllowed($otpHistory);

        if (self::isFirstTimeVisit($otpHistory) || $otpStatus['allowed']) {

            $apiHandler = new APIHandler();
            $url = config('api.base_url') . config('api.send_otp_url');
            $strRefId = $mobileNo . randomDigits();

            $startTime = now();
            $response = $apiHandler->postCall($url, [
                'strRefId' => $strRefId,
                'strMobileNo' => $mobileNo,
                'isEncPwd' => true,
            ]);

            if ($response['status'] === 'success' && $response['statusCode'] === Response::HTTP_OK) { // success response found from handler.

                $isValidData = $this->decodeJsonIfValid($response['data']);
                if ($isValidData !== null) { // valid json for the first time

                    $data = $this->decodeJsonIfValid($isValidData); // second time json validation
                    $statusCode = intval($data['StatusCode']);
                    if ($statusCode === Response::HTTP_BAD_REQUEST) { // API RESPONSE ERROR

                        $responseOut = [
                            'code' => $statusCode,
                            'status' => 'error',
                            'message' => __('messages.entered-phone-number-invalid'),
                            'prompt' => null
                        ];
                        return $this->sendResponse($responseOut, $responseOut['code']);

                    } else if ($statusCode === Response::HTTP_OK) { // API SEND SUCCESS RESPONSE

                        Session::put('otp', [
                            'phone_masked' => $this->hidePhoneNumber($mobileNo),
                            'otp_phone' => $mobileNo,
                            'strRefId' => $strRefId
                        ]);

                        // Capture response information
                        $responseTime = now()->diffInSeconds($startTime);
                        $otpSentSuccess = $response['statusCode'] === Response::HTTP_OK;

                        if (self::isFirstTimeVisit($otpHistory)) {
                            // If it's the first-time visit, create a new record
                            OtpHistory::create([
                                'phone_number' => $mobileNo,
                                'otp_sent_count' => 1,
                                'last_sent_at' => now(),
                                'otp_sent_success' => $otpSentSuccess,
                                'response_status_code' => $response['statusCode'] ?? null,
                                'response_received_at' => now(),
                                'response_data' => json_encode($response),
                                'response_time_seconds' => $responseTime,
                            ]);
                        } else {
                            // If it's not the first-time visit, create a new record and update the existing record
                            OtpHistory::create([
                                'phone_number' => $mobileNo,
                                'otp_sent_count' => $otpHistory->otp_sent_count + 1,
                                'last_sent_at' => now(),
                                'otp_sent_success' => $otpSentSuccess,
                                'response_status_code' => $response['statusCode'] ?? null,
                                'response_received_at' => now(),
                                'response_data' => json_encode($response),
                                'response_time_seconds' => $responseTime,
                            ]);

                            // Update the existing record
                            $this->updateOtpHistory($otpHistory, $otpSentSuccess, $response, $responseTime);
                        }

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
                            'message' => __('messages.apologies-something-went-wrong'),
                            'prompt' => getPromptPath('common/request-failed-en')
                        ];
                        return $this->sendResponse($responseOut, $responseOut['code']);
                    }

                } else {
                    $responseOut = [
                        'code' => Response::HTTP_EXPECTATION_FAILED,
                        'status' => 'error',
                        'message' => __('messages.apologies-something-went-wrong'), // Null response
                        'prompt' => getPromptPath('common/request-failed-en')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                }

            } else {

                $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
                Log::error('API ERROR:: ' . $msg);
                $responseOut = [
                    'code' => Response::HTTP_EXPECTATION_FAILED,
                    'status' => 'error',
                    'message' => __('messages.apologies-something-went-wrong'),
                    'prompt' => getPromptPath('common/request-failed-en')
                ];
                return $this->sendResponse($responseOut, $responseOut['code']);
            }

        } else {

            if ($otpStatus['reason'] === 'daily_limit_exceeded') {
                $response = [
                    'code' => Response::HTTP_FORBIDDEN,
                    'status' => 'error',
                    'message' => 'Max daily OTP count exceeded.',
                    'prompt' => null
                ];
            } else if ($otpStatus['reason'] === 'overall_limit_exceeded') {
                $response = [
                    'code' => Response::HTTP_FORBIDDEN,
                    'status' => 'error',
                    'message' => 'Max OTP SMS count exceeded.',
                    'prompt' => null
                ];
            } else {
                $response = [
                    'code' => Response::HTTP_FORBIDDEN,
                    'status' => 'error',
                    'message' => 'Sending OTP not allowed.',
                    'prompt' => null
                ];
            }

            return $this->sendResponse($response, $response['code']);
        }

    }

    public static function isOtpSendingAllowed($otpHistory)
    {
        $maxAllowedOtpSmsCount = config('otp.max_allowed_otp_sms_count');
        $maxAllowedDailyOtpCount = config('otp.max_allowed_daily_otp_count');

        Log::info('MAX_ALLOWED_OTP_SMS_COUNT: ' . $maxAllowedOtpSmsCount);
        Log::info('MAX_ALLOWED_DAILY_OTP_COUNT: ' . $maxAllowedDailyOtpCount);

        $todaySentCount = OtpHistory::where('phone_number', $otpHistory->phone_number)
            ->whereDate('created_at', Carbon::today())
            // ->count()
            ->sum('otp_sent_count');

        Log::info('TODAY_SENT_COUNT: ' . $todaySentCount);
        Log::info('TOTAL_SENT_COUNT: ' . $otpHistory->otp_sent_count);

        if ($todaySentCount >= $maxAllowedDailyOtpCount) {
            // Daily count exceeded
            return ['allowed' => false, 'reason' => 'daily_limit_exceeded'];
        }

        if ($otpHistory->otp_sent_count >= $maxAllowedOtpSmsCount) {
            // Overall count exceeded
            return ['allowed' => false, 'reason' => 'overall_limit_exceeded'];
        }

        return ['allowed' => true, 'reason' => null];
    }

    public static function updateOtpHistory($otpHistory, $otpSentSuccess, $response, $responseTime)
    {
        $otpHistory->update([
            'otp_sent_count' => $otpHistory->otp_sent_count + 1,
            'last_sent_at' => now(),
            'otp_sent_success' => $otpSentSuccess,
            'response_status_code' => $response['statusCode'] ?? null,
            'response_received_at' => now(),
            'response_data' => json_encode($response),
            'response_time_seconds' => $responseTime,
        ]);
    }

    public static function isFirstTimeVisit($otpHistory)
    {
        return $otpHistory === null;
    }

    public function verifyOtpWrapper(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $mobileNo = Session::get('otp.otp_phone');
        $strRefId = Session::get('otp.strRefId');

        // WILL BE REMOVED LATER
        /*// call api to get user account name.
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
            'message' => __('messages.verification-success-after-login'),
            'prompt' => null,
            'pn' => $mobileNo,
            'an' => $getAccountList['data']['accountName'] ?? null,
            'acn' => $getAccountList['data']['accountNo'] ?? null,
            'url' => url('/')
        ];

        // Set the flash message
        session()->flash('status', $responseOut['status']);
        session()->flash('message', $responseOut['message']);

        return $this->sendResponse($responseOut, $responseOut['code']);*/
        // WILL BE REMOVED LATER

        $apiHandler = new APIHandler();
        $url = config('api.base_url') . config('api.verify_otp_url');
        $response = $apiHandler->postCall($url, [
            'strRequstId' => $strRefId,
            'strAcMobileNo' => $mobileNo,
            'strReOTP' => $request->input('code') ?? null,
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) { // successful api response found from apihandler end.

            $firstData = json_decode($response['data']);
            $secondData = json_decode($firstData);
            $apiStatus = (bool)$secondData->Status;
            $statusCode = $response['statusCode'];

            if ($statusCode === Response::HTTP_OK) {
                if ($apiStatus === false) {
                    // Verification failed. Possible reason, OTP expired.
                    $responseOut = [
                        'code' => Response::HTTP_EXPECTATION_FAILED,
                        'status' => 'error',
                        'message' => __('messages.apologies-something-went-wrong'),
                        'prompt' => getPromptPath('common/request-failed-en')
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                } else { // success

                    // After Verification
                    // Make the user as logged-in user, set a flag to verify the user.
                    // call api to get user account name.

                    /*$otpInfo = Session::get('otp');
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
                        'message' => __('messages.verification-success-after-login'),
                        'prompt' => null,
                        'pn' => $mobileNo,
                        'an' => $getAccountList['data']['accountName'] ?? null,
                        'acn' => $getAccountList['data']['accountNo'] ?? null,
                        'url' => url('/')
                    ];

                    // Set the flash message
                    session()->flash('status', $responseOut['status']);
                    session()->flash('message', $responseOut['message']);*/

                    // $getAccountList = $this->fetchGetWalletDetails($mobileNo);
                    $getAccountList = $this->fetchSavingsDeposits($mobileNo);

                    $acLists = $getAccountList['accountList'] ?? [];
                    $acListArr = self::processMaskedAccountLists($acLists);

                    /*// will be removed later.
                    $acNoTest = "5107801027727";
                    $testNewArray = [
                        "accountNo" => self::maskAccountNumber($acNoTest),
                        "accountName" => "Raqibul Hasan Moon",
                        "branchCode" => "ABC123",
                        "accEnc" => openSSLEncryptDecrypt($acNoTest)
                    ];

                    $acListArr['acList'][] = $testNewArray;
                    // will be removed later.
//                    array:1 [ // app/Http/Controllers/ApiController.php:338
//  "acList" => array:1 [
//    0 => array:4 [
//      "accountNo" => "5107******828"
//      "accountName" => "MD RAQIBUL HASAN"
//      "branchCode" => "ZgR7u65sZWcpywR0Cvq1BThpVi84UWUzbTJ1RWNpWTUzeVFCcUhXOGpZMDc2UG5IZTlNV29nN1l1cFE9"
//      "accEnc" => "DNzEPECCXLac4h+MR7OPAWkxQ1F4RldkWGhkU2RsMkF4aXJyaWc9PQ=="
//    ]
//  ]
//]
                     */

                    // store encrypted accountList in session
                    self::storeAcListInSession($acListArr);

                    /*$otpInfo = Session::get('otp');
                    $getAccountList = $this->fetchGetWalletDetails($otpInfo['otp_phone']);

                    Session::put('logInfo', [
                        'is_logged' => base64_encode(true),
                        'otp_info' => $otpInfo,
                        'account_info' => $getAccountList['data'],
                    ]);
                    Session::forget('otp');*/

                    $responseOut = [
                        'code' => $statusCode,
                        'status' => 'success',
                        'message' => __('messages.verification-success-after-login'),
                        'prompt' => null,
                        'acLists' => $acListArr,
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                }
            } else {
                $responseOut = [
                    'code' => $statusCode,
                    'status' => 'error',
                    'message' => __('messages.apologies-something-went-wrong'),
                    'prompt' => getPromptPath('common/request-failed-en')
                ];
                return $this->sendResponse($responseOut, $responseOut['code']);
            }

        } else {

            $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
            Log::error('API ERROR:: ' . $msg);
            $responseOut = [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => __('messages.apologies-something-went-wrong'),
                'prompt' => getPromptPath('common/request-failed-en')
            ];
            return $this->sendResponse($responseOut, $responseOut['code']);
        }

    }

    public static function storeAcListInSession($acList)
    {
        $encryptedAcList = encrypt($acList);
        Session::put('encrypted_acList', $encryptedAcList);
    }

    public function getSavedAccountInfo()
    {
        $acListArr = [];
        $acList = data_get(decrypt(Session::get('encrypted_acList')), 'acList', []);

        $acListArr['acList'] = $acList;
        $responseOut = [
            'code' => 200,
            'status' => 'success',
            'message' => __('messages.verification-success-after-login'),
            'prompt' => null,
            'acLists' => $acListArr,
        ];
        return $this->sendResponse($responseOut, $responseOut['code']);
    }

    public static function getAcListFromSession()
    {
        // Retrieve and decrypt from session
        $encryptedAcList = Session::get('encrypted_acList');
        return decrypt($encryptedAcList);
    }

    public function saveAccountInfo(Request $request)
    {
        $request->validate([
            'ac' => ['required'],
            'purpose' => ['nullable'],
        ]);

        $selectedAccount = $request->input('ac');
        $purpose = $request->input('purpose') ?? null;
        $getSelected = self::processSelectedAccount($selectedAccount);
        $accountAsData = self::getAccountListArray($getSelected);

        if ($getSelected) {

            $otpInfo = Session::get('otp');
            Session::put('logInfo', [
                'is_logged' => base64_encode(true),
                'otp_info' => $otpInfo,
                'account_info' => $accountAsData,
            ]);

            Session::forget('otp');

            $mobileNo = Session::get('logInfo.otp_info.otp_phone');
            $purpose = $request->input('purpose');
            $responseOut = [
                'code' => Response::HTTP_OK,
                'status' => 'success',
                'message' => (filled($purpose) && $purpose == 'ACCOUNT-SWITCH') ? __('messages.account-switching-success') : __('messages.verification-success-after-login'),
                'prompt' => null,
                'pn' => $mobileNo,
                'an' => $accountAsData['accountName'] ?? null,
                'acn' => $accountAsData['accountNo'] ?? null,
                'url' => url('/')
            ];

            session()->flash('status', $responseOut['status']);
            session()->flash('message', $responseOut['message']);

            return $this->sendResponse($responseOut, $responseOut['code']);
        }

        $responseOut = [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => __('messages.apologies-something-went-wrong'),
            'prompt' => getPromptPath('common/request-failed-en')
        ];

        return $this->sendResponse($responseOut, $responseOut['code']);

    }

    public static function getAccountListArray($data)
    {
        return [
            'accountName' => $data['accountName'] ?? null,
            'accountNo' => $data['accountNo'] ?? null,
        ];
    }

    public static function processSelectedAccount($selectedAccountId)
    {
        $acList = data_get(decrypt(Session::get('encrypted_acList')), 'acList', []);

        return collect($acList)->firstWhere('accEnc', $selectedAccountId) ?: false;
    }

    public static function processMaskedAccountLists($acLists)
    {
        return ['acList' => collect($acLists)->map(function ($account) {
            $accountNo = $account['AccountNo'];
            return [
                'accountNo' => self::maskAccountNumber($accountNo),
                'accountName' => trim($account['AccountName']),
                'productCode' => openSSLEncryptDecrypt($account['ProductCode']),
                'accEnc' => openSSLEncryptDecrypt($accountNo),
            ];
        })->values()->toArray()];
    }

    private static function maskAccountNumber($accountNo)
    {
        $visibleDigits = 4;
        $maskedDigits = strlen($accountNo) - $visibleDigits - 3;
        $maskedPart = str_repeat('*', $maskedDigits);
        $firstDigits = substr($accountNo, 0, $visibleDigits);
        $lastDigits = substr($accountNo, -3);
        return "{$firstDigits}{$maskedPart}{$lastDigits}";
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

                /*$savingsDeposits = array_filter($accountList, function ($account) {
                    return $account['ProductName'] === 'Savings Deposit';
                });*/

                $returnArr['accountList'] = array_map(function ($account) {
                    return [
                        'AccountName' => $account['AccountName'],
                        'AccountNo' => $account['AccountNo'],
                        'ProductCode' => $account['ProductCode'],
                        'ProductName' => $account['ProductName'],
                    ];
                }, $accountList);
                return $returnArr;
            }
        }

        return [];
    }

    public static function fetchGetWalletDetails($phoneNumber): array
    {

        // will be removed this later
        /*return [
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
        ];*/

        $url = config('api.base_url') . config('api.get_wallet_details_url');
        $apiHandler = new APIHandler();
        $response = $apiHandler->postCall($url, ['mobileNo' => $phoneNumber, 'userId' => 'Agx01254']);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);

            if ($data['status'] === '200' && $data['statsDetails'] === 'success' && isset($data['acList'][0])) {
                $accountList = $data['acList'];
                return [
                    'status' => 'success',
                    'message' => 'Data Received.',
                    'code' => Response::HTTP_OK,
                    'data' => [
                        'name' => $data['name'] ?? null,
                        'accountName' => $accountList[0]['accountName'] ?? null,
                        'accountNo' => $accountList[0]['accountNo'] ?? null,
                        'balanceAmount' => $data['balanceAmount'] ?? 0,
                        // 'walletStatus' => $data['walletStatus'] ?? null,
                        'PID' => $data['PID'] ?? null,
                        'dateOfBirth' => $data['dateOfBirth'] ?? null,
                        'email' => $data['email'] ?? null,
                        'accountList' => $accountList,
                    ]
                ];
            }
        }

        return [
            'status' => 'error',
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'message' => 'Data not found.',
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
                    'prompt' => null
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => null
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
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
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
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
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
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
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

        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
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
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
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
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedText = __('messages.common-request-failed-text');

        // will be removed later
        /*return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];*/
        // will be removed later

        $reason = $data['reason'];
        $url = config('api.base_url') . config('api.get_pin_reset_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $reason,
            "refId" => $mobileNo . randomDigits()
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);
            // dd($data, intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success');
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt)
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];
    }

    public static function processApiCallingEWApproveOrReject($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedText = __('messages.common-request-failed-text');

        // will be removed later
        /*return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];*/
        // will be removed later

        $reason = $data['reason'];
        $url = config('api.base_url') . config('api.approve_wallet_request_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $reason,
            "refId" => $mobileNo . randomDigits()
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);
            // dd($data, intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success');
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt)
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];
    }

    public static function processApiCallingUserInfoVerify($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $successText = __('messages.account-verify-by-nid-dob-success');
        $failedText = __('messages.account-verify-by-nid-dob-failed');

        $phoneNumber = trim($data['mobile_no']);
        $account = trim($data['account']);
        $dob = $data['dob'];

        $response = self::fetchGetWalletDetails($phoneNumber);
        if ($response['status'] === 'success' && $response['code'] === Response::HTTP_OK) { // success

            $userActualDOB = $response['data']['dateOfBirth'] ?? null;
            $userActualACNo = $response['data']['accountNo'];

            if ($userActualACNo == $account && self::compareDateOfBirths($dob, $userActualDOB)) {

                return [
                    'code' => Response::HTTP_OK,
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt),
                ];
            } else {
                return [
                    'code' => Response::HTTP_EXPECTATION_FAILED,
                    'status' => 'error',
                    'message' => $failedText,
                    'prompt' => getPromptPath($failedPrompt),
                ];
            }
        } else {
            return [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => $failedText,
                'prompt' => getPromptPath($failedPrompt)
            ];
        }

    }

    public static function compareDateOfBirths($userInputedDOB, $userActualDob)
    {
        try {
            $userInputedDOBDateTime = new \DateTime($userInputedDOB);
            if (is_string($userActualDob)) {
                $userActualDobDateTime = new \DateTime($userActualDob);
            } elseif ($userActualDob instanceof \DateTime) {
                $userActualDobDateTime = $userActualDob;
            } else {
                throw new \Exception("Invalid data type for user actual DOB.");
            }

            return $userInputedDOBDateTime->format('Y-m-d') == $userActualDobDateTime->format('Y-m-d');
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function processApiCallingReSendOTP($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $successText = __('messages.account-verify-by-nid-dob-success');
        $failedText = __('messages.account-verify-by-nid-dob-failed');

        $phoneNumber = $data['mobile_no'];
        $nid = $data['nid'];
        $dob = $data['dob'];

        $response = self::fetchGetWalletDetails($phoneNumber);

        if ($response['status'] === 'success' && $response['code'] === Response::HTTP_OK) { // success
            if ($response['data']['PID'] === $nid && $dob === date('Y-m-d', strtotime($response['data']['dateOfBirth']))) {

                return [
                    'code' => Response::HTTP_OK,
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt),
                ];
            }
        }

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];

    }

    public static function processApiCallingEWDeviceBind($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        // will be removed later
        /*return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];*/
        // will be removed later

        $reason = $data['reason'];
        $url = config('api.base_url') . config('api.device_bind_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $reason,
            "refId" => $mobileNo . randomDigits()
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);
            // dd($data, intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success');
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt)
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];
    }

    public static function processApiCallingEWCloseWallet($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        // will be removed later
        /*return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];*/
        // will be removed later

        $reason = $data['reason'];
        $url = config('api.base_url') . config('api.close_wallet_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $reason,
            "OtpCode" => "",
            "refId" => $mobileNo . randomDigits()
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);
            // dd($data, intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success');
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt)
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];
    }

    public static function processApiCallingEWLockBlock($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        // will be removed later
        /*return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];*/
        // will be removed later
        $reason = $data['reason'];
        $url = config('api.base_url') . config('api.lock_wallet_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "reason" => $reason,
            "OtpCode" => "",
            "refId" => $mobileNo . randomDigits()
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);
            // dd($data, intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success');
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt)
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];
    }

    public static function processApiCallingEWUnlockActive($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        // will be removed later
        /*return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];*/
        // will be removed later

        $reason = $data['reason'];
        $url = config('api.base_url') . config('api.active_wallet_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $reason,
            "refId" => $mobileNo . randomDigits()
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);
            // dd($data, intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success');
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt)
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
        ];
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
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
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
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
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
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
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
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => $successText,
            'prompt' => getPromptPath($successPrompt)
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt)
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

    public static function processGetCallTypesDropDownValues($data)
    {
        $callType = self::getDropDownForCallTypeApi();
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Data Found.',
            'prompt' => null,
            'data' => $callType
        ];

    }

    public static function processGetCallCategoryDropDownValues($data)
    {
        $callCategory = self::getDropDownForCallCategoryApi($data);
        // dd($callCategory);
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Data Found.',
            'prompt' => null,
            'data' => $callCategory
        ];

    }

    public static function processToCreateTicketInCRM($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        // self::defaultResponseForProcessToCreateTicketInCRM();

        $accessToken = Cache::get('crm_access_token');

        if (!$accessToken) {
            $accessToken = self::generateCRMLoginToken();
            if (false === $accessToken) {
                return [
                    'code' => Response::HTTP_EXPECTATION_FAILED,
                    'status' => 'failed',
                    'message' => $failedText,
                    'prompt' => getPromptPath($failedPrompt),
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

            $ticketId = $responseData['data']['id'] ?? "NA";
            $message = $responseData['data']['message'] ?? "NA";

            return [
                'code' => Response::HTTP_OK,
                'status' => 'success',
                'message' => $successText,
                'prompt' => getPromptPath($successPrompt),
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
                $ticketId = $responseData['data']['id'] ?? "NA";
                $message = $responseData['data']['message'] ?? "";

                return [
                    'code' => Response::HTTP_OK,
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt),
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
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
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

    public static function getDropDownForCallCategoryApi($data)
    {
        /*// will be removed later
        $responseData['status'] = 'success';
        $responseData['statusCode'] = 200;
        $responseData['data'] = self::dummyResponseForGetDropDownForCallCategoryApi();
        // will be removed later*/


        // $selectedValues = $data['selectedValues']['callType'];
        $selectedValues = 2; // hardcoded call-type value for Service Request
        $url = config('api.crm_ticket_base_url') .
            config('api.crm_ticket_call_category_url');

        $apiHandler = new APIHandler();
        $responseData = $apiHandler->doGetCall($url, [
            "call_type_id" => $selectedValues
        ], [
            'Authorization' => 'Bearer ' . config('api.crm_ticket_authorization_token'),
        ]);

        $options = [];
        if ($responseData['status'] === 'success' && $responseData['statusCode'] === 200) {
            $response = json_decode($responseData['data'], true);

            if ($response['success']) {
                $callCategories = $response['data'] ?? [];
                foreach ($callCategories as $category) {
                    // if ($category['call_type_id'] === $selectedValues) {
                    $options[$category['id']] = $category['name'];
                    // }
                }
            }

            return $options;
        }
        return $options;

    }

    /*public static function dummyResponseForGetDropDownForCallCategoryApi()
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
        return $dummyResponse;
        $response = json_decode($dummyResponse, true);

        $callCategories = $response['data'] ?? [];
        $options = [];
        foreach ($callCategories as $category) {
            $options[$category['id'] . "|" . $category['call_type_id']] = $category['name'];
        }

        return $options;

    }*/

    public static function getDropDownForCallTypeApi()
    {
        $url = config('api.crm_ticket_base_url') . config('api.crm_ticket_call_type_url');
        $apiHandler = new APIHandler();
        $responseData = $apiHandler->doGetCall($url, [], [
            'Authorization' => 'Bearer ' . config('api.crm_ticket_authorization_token'),
        ]);

        /*// will be removed later
        $responseData['status'] = 'success';
        $responseData['statusCode'] = 200;
        $responseData['data'] = self::dummyResponseForGetDropDownForCallTypeApi();
        // will be removed later*/

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


    public static function processGetSubCategoryDropDownValues($data)
    {
        $callSubCategory = self::getSubCategoryDropDownValues($data);
        // dd($callCategory);
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Data Found.',
            'prompt' => null,
            'data' => $callSubCategory
        ];


    }

    public static function getSubCategoryDropDownValues($data)
    {
        /*// will be removed later
        $responseData['status'] = 'success';
        $responseData['statusCode'] = 200;
        $responseData['data'] = self::dummyResponseForGetSubCategoryDropDownForCallCategpryApi();
        // will be removed later*/

        $callTypeValue = $data['selectedValues']['callType'];
        $callCategoryValue = $data['selectedValues']['callCategory'];

        $url = config('api.crm_ticket_base_url') .
            config('api.crm_ticket_call_sub_category_url');

        $apiHandler = new APIHandler();
        $responseData = $apiHandler->doGetCall($url, [
            "call_type_id" => $callTypeValue,
            "call_category_id" => $callCategoryValue
        ], [
            'Authorization' => 'Bearer ' . config('api.crm_ticket_authorization_token'),
        ]);

        $options = [];
        if ($responseData['status'] === 'success' && $responseData['statusCode'] === 200) {
            $response = json_decode($responseData['data'], true);

            if ($response['success']) {
                $callCategories = $response['data'] ?? [];
                foreach ($callCategories as $category) {
                    // if ($category['call_category_id'] === $callCategoryValue && $category['call_type_id'] === $callTypeValue) {
                    $options[$category['id']] = $category['name'];
                    // }
                }
            }

            return $options;
        }
        return $options;

    }

    public static function getSubSubCategoryDropDownValues($data)
    {
        /*// will be removed later
        $responseData['status'] = 'success';
        $responseData['statusCode'] = 200;
        $responseData['data'] = self::dummyResponseForGetSubSubCategoryDropDownForCallCategoryApi();
        // will be removed later*/

        $callTypeValue = $data['selectedValues']['callType'];
        $callCategoryValue = $data['selectedValues']['callCategory'];
        $callSubCategoryValue = $data['selectedValues']['callSubCategory'];

        $url = config('api.crm_ticket_base_url') . config('api.crm_ticket_call_sub_sub_category_url');
        $apiHandler = new APIHandler();
        $responseData = $apiHandler->doGetCall($url, [
            "call_type_id" => $callTypeValue,
            "call_category_id" => $callCategoryValue,
            "call_sub_category_id" => $callSubCategoryValue,
        ], [
            'Authorization' => 'Bearer ' . config('api.crm_ticket_authorization_token'),
        ]);

        $options = [];
        if ($responseData['status'] === 'success' && $responseData['statusCode'] === 200) {
            $response = json_decode($responseData['data'], true);

            if ($response['success']) {
                $callSubSubCategories = $response['data'] ?? [];
                foreach ($callSubSubCategories as $category) {
                    // if ($category['call_category_id'] === $callCategoryValue && $category['call_type_id'] === $callTypeValue && $category['call_sub_category_id'] === $callSubCategoryValue) {
                    $options[$category['id']] = $category['name'];
                    // }
                }
            }

            return $options;
        }
        return $options;
    }

    public static function processGetSubSubCategoryDropDownValues($data)
    {
        $callSubSubCategory = self::getSubSubCategoryDropDownValues($data);
        // dd($callSubSubCategory);
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Data Found.',
            'prompt' => null,
            'data' => $callSubSubCategory
        ];

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
        return $dummyResponse;
        $response = json_decode($dummyResponse, true);

        $callTypes = $response['data'] ?? [];
        $options = [];
        foreach ($callTypes as $type) {
            $options[$type['id']] = $type['name'];
        }

        return $options;
    }

    public static function dummyResponseForGetSubCategoryDropDownForCallCategpryApi()
    {
        $dummyResponse = '{
    "success": true,
    "data": [
        {
            "id": "1",
            "call_category_id": "1",
            "call_type_id": "4",
            "name": "Prepaid Card"
        },
        {
            "id": "2",
            "call_category_id": "2",
            "call_type_id": "3",
            "name": "BACH"
        },
        {
            "id": "3",
            "call_category_id": "2",
            "call_type_id": "3",
            "name": "RTGS"
        },
        {
            "id": "4",
            "call_category_id": "1",
            "call_type_id": "4",
            "name": "Debit Card"
        },
        {
            "id": "5",
            "call_category_id": "2",
            "call_type_id": "3",
            "name": "BEFTN"
        },
        {
            "id": "6",
            "call_category_id": "1",
            "call_type_id": "4",
            "name": "Credit Card"
        },
        {
            "id": "7",
            "call_category_id": "2",
            "call_type_id": "3",
            "name": "NPSB"
        },
        {
            "id": "8",
            "call_category_id": "3",
            "call_type_id": "2",
            "name": "BACH"
        },
        {
            "id": "9",
            "call_category_id": "3",
            "call_type_id": "2",
            "name": "BEFTN"
        },
        {
            "id": "10",
            "call_category_id": "3",
            "call_type_id": "2",
            "name": "RTGS"
        },
        {
            "id": "11",
            "call_category_id": "3",
            "call_type_id": "2",
            "name": "NPSB"
        },
        {
            "id": "12",
            "call_category_id": "4",
            "call_type_id": "1",
            "name": "RTGS"
        },
        {
            "id": "13",
            "call_category_id": "4",
            "call_type_id": "1",
            "name": "NPSB"
        },
        {
            "id": "14",
            "call_category_id": "5",
            "call_type_id": "3",
            "name": "Agent Banking"
        },
        {
            "id": "15",
            "call_category_id": "5",
            "call_type_id": "3",
            "name": "Agent Banking  Entrepreneur"
        },
        {
            "id": "16",
            "call_category_id": "6",
            "call_type_id": "2",
            "name": "Agent Banking"
        },
        {
            "id": "17",
            "call_category_id": "8",
            "call_type_id": "1",
            "name": "Agent Banking"
        },
        {
            "id": "18",
            "call_category_id": "9",
            "call_type_id": "3",
            "name": "Widow Allowance"
        },
        {
            "id": "19",
            "call_category_id": "9",
            "call_type_id": "3",
            "name": "Social Safety Net Account"
        },
        {
            "id": "20",
            "call_category_id": "9",
            "call_type_id": "3",
            "name": "Freedom Fighter A/C & Allowance"
        },
        {
            "id": "21",
            "call_category_id": "10",
            "call_type_id": "4",
            "name": "e-Wallet"
        },
        {
            "id": "22",
            "call_category_id": "9",
            "call_type_id": "3",
            "name": "Freedom Fighter Allowance"
        },
        {
            "id": "23",
            "call_category_id": "9",
            "call_type_id": "3",
            "name": "Salary Disbursement"
        },
        {
            "id": "24",
            "call_category_id": "9",
            "call_type_id": "3",
            "name": "Disabled Allowance"
        },
        {
            "id": "25",
            "call_category_id": "9",
            "call_type_id": "3",
            "name": "Disabled Students Stipend"
        },
        {
            "id": "26",
            "call_category_id": "11",
            "call_type_id": "3",
            "name": "Credit Card"
        },
        {
            "id": "27",
            "call_category_id": "9",
            "call_type_id": "3",
            "name": "Transgender Allowance"
        },
        {
            "id": "28",
            "call_category_id": "9",
            "call_type_id": "3",
            "name": "Dalit/Harijan/Vede Allowance"
        },
        {
            "id": "29",
            "call_category_id": "9",
            "call_type_id": "3",
            "name": "Old Age Allowance"
        },
        {
            "id": "30",
            "call_category_id": "9",
            "call_type_id": "3",
            "name": "Tea Worker Allowance"
        },
        {
            "id": "31",
            "call_category_id": "12",
            "call_type_id": "3",
            "name": "A Challan"
        },
        {
            "id": "32",
            "call_category_id": "12",
            "call_type_id": "3",
            "name": "City Corporation Bills/Taxes"
        },
        {
            "id": "33",
            "call_category_id": "12",
            "call_type_id": "3",
            "name": "GTS Challan"
        },
        {
            "id": "34",
            "call_category_id": "12",
            "call_type_id": "3",
            "name": "IRC Challan"
        },
        {
            "id": "35",
            "call_category_id": "12",
            "call_type_id": "3",
            "name": "NBR Vat & Tax"
        },
        {
            "id": "36",
            "call_category_id": "12",
            "call_type_id": "3",
            "name": "Travel Tax"
        },
        {
            "id": "37",
            "call_category_id": "13",
            "call_type_id": "2",
            "name": "A Challan"
        },
        {
            "id": "38",
            "call_category_id": "14",
            "call_type_id": "1",
            "name": "Travel Tax"
        },
        {
            "id": "39",
            "call_category_id": "14",
            "call_type_id": "1",
            "name": "IRC Challan"
        },
        {
            "id": "40",
            "call_category_id": "14",
            "call_type_id": "1",
            "name": "A Challan"
        },
        {
            "id": "41",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "BJMC worker Account"
        },
        {
            "id": "42",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "BMEB Account"
        },
        {
            "id": "43",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Branch/ Agent/ATM location"
        },
        {
            "id": "44",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Current Account"
        },
        {
            "id": "45",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "DDP"
        },
        {
            "id": "46",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Double Benefit Scheme (DBS)"
        },
        {
            "id": "47",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Education Deposit Scheme"
        },
        {
            "id": "48",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Farmer Account"
        },
        {
            "id": "49",
            "call_category_id": "11",
            "call_type_id": "3",
            "name": "Debit Card"
        },
        {
            "id": "50",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Fixed Deposit"
        },
        {
            "id": "51",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Freedom Fighter Account"
        },
        {
            "id": "52",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Garments Worker A/C"
        },
        {
            "id": "53",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Locker service"
        },
        {
            "id": "54",
            "call_category_id": "11",
            "call_type_id": "3",
            "name": "Prepaid Card"
        },
        {
            "id": "55",
            "call_category_id": "16",
            "call_type_id": "1",
            "name": "Credit Card"
        },
        {
            "id": "56",
            "call_category_id": "16",
            "call_type_id": "1",
            "name": "Debit Card"
        },
        {
            "id": "57",
            "call_category_id": "16",
            "call_type_id": "1",
            "name": "Prepaid Card"
        },
        {
            "id": "58",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Marriage Savings Scheme"
        },
        {
            "id": "59",
            "call_category_id": "17",
            "call_type_id": "2",
            "name": "Credit Card"
        },
        {
            "id": "60",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Medicare Deposit Scheme"
        },
        {
            "id": "61",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Monthly Earning Scheme(MES)"
        },
        {
            "id": "62",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Non-Resident Deposit Scheme"
        },
        {
            "id": "63",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Poor Women Account"
        },
        {
            "id": "64",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Railway Account"
        },
        {
            "id": "65",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Rural Deposit Scheme"
        },
        {
            "id": "66",
            "call_category_id": "17",
            "call_type_id": "2",
            "name": "Debit Card"
        },
        {
            "id": "67",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Retail Banking"
        },
        {
            "id": "68",
            "call_category_id": "17",
            "call_type_id": "2",
            "name": "Prepaid Card"
        },
        {
            "id": "69",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Savings Account"
        },
        {
            "id": "70",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "SBRSS"
        },
        {
            "id": "71",
            "call_category_id": "18",
            "call_type_id": "3",
            "name": "Prank Call"
        },
        {
            "id": "72",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "School Banking"
        },
        {
            "id": "73",
            "call_category_id": "19",
            "call_type_id": "3",
            "name": "Non SBL Related"
        },
        {
            "id": "74",
            "call_category_id": "20",
            "call_type_id": "3",
            "name": "Complaint Status"
        },
        {
            "id": "75",
            "call_category_id": "5",
            "call_type_id": "3",
            "name": "Shadheen Sanchay Scheme"
        },
        {
            "id": "76",
            "call_category_id": "21",
            "call_type_id": "3",
            "name": "No Response Call"
        },
        {
            "id": "77",
            "call_category_id": "21",
            "call_type_id": "3",
            "name": "Silent call"
        },
        {
            "id": "78",
            "call_category_id": "5",
            "call_type_id": "3",
            "name": "Shadheen Sanchay Scheme"
        },
        {
            "id": "79",
            "call_category_id": "21",
            "call_type_id": "3",
            "name": "Call Drop Before Issue Identify"
        },
        {
            "id": "80",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Shadheen Sanchay Scheme"
        },
        {
            "id": "81",
            "call_category_id": "22",
            "call_type_id": "3",
            "name": "Right Information Act"
        },
        {
            "id": "82",
            "call_category_id": "23",
            "call_type_id": "3",
            "name": "Mobile banking"
        },
        {
            "id": "83",
            "call_category_id": "24",
            "call_type_id": "3",
            "name": "LGSP"
        },
        {
            "id": "84",
            "call_category_id": "25",
            "call_type_id": "3",
            "name": "Internet Banking (Upcoming)"
        },
        {
            "id": "85",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "SND Account"
        },
        {
            "id": "86",
            "call_category_id": "26",
            "call_type_id": "3",
            "name": "Digital Banking"
        },
        {
            "id": "87",
            "call_category_id": "27",
            "call_type_id": "3",
            "name": "Corporate Banking"
        },
        {
            "id": "88",
            "call_category_id": "28",
            "call_type_id": "3",
            "name": "Civil Scheme"
        },
        {
            "id": "89",
            "call_category_id": "28",
            "call_type_id": "3",
            "name": "Army Scheme"
        },
        {
            "id": "90",
            "call_category_id": "29",
            "call_type_id": "3",
            "name": "BTCL Fees"
        },
        {
            "id": "91",
            "call_category_id": "30",
            "call_type_id": "1",
            "name": "BTCL Fees"
        },
        {
            "id": "92",
            "call_category_id": "31",
            "call_type_id": "2",
            "name": "International Trade Finance"
        },
        {
            "id": "93",
            "call_category_id": "32",
            "call_type_id": "3",
            "name": "Loan Rescheduling & Restructuring"
        },
        {
            "id": "94",
            "call_category_id": "33",
            "call_type_id": "3",
            "name": "Internal"
        },
        {
            "id": "95",
            "call_category_id": "33",
            "call_type_id": "3",
            "name": "Imported"
        },
        {
            "id": "96",
            "call_category_id": "34",
            "call_type_id": "3",
            "name": "SMS Banking"
        },
        {
            "id": "97",
            "call_category_id": "35",
            "call_type_id": "1",
            "name": "SMS Banking"
        },
        {
            "id": "98",
            "call_category_id": "36",
            "call_type_id": "2",
            "name": "SMS Banking"
        },
        {
            "id": "99",
            "call_category_id": "37",
            "call_type_id": "3",
            "name": "Jakat Fund"
        },
        {
            "id": "100",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Sonali Bank Daily Profit scheme"
        },
        {
            "id": "101",
            "call_category_id": "37",
            "call_type_id": "3",
            "name": "Hajj Fund"
        },
        {
            "id": "102",
            "call_category_id": "38",
            "call_type_id": "3",
            "name": "IPFD"
        },
        {
            "id": "103",
            "call_category_id": "38",
            "call_type_id": "3",
            "name": "Entrepreneurship Support Fund"
        },
        {
            "id": "104",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Sonali Bank Millionaire Scheme"
        },
        {
            "id": "105",
            "call_category_id": "39",
            "call_type_id": "2",
            "name": "Industrial Project Financing"
        },
        {
            "id": "106",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Sonali Deposite Scheme"
        },
        {
            "id": "107",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Student Savings Account"
        },
        {
            "id": "108",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Triple Benefit Scheme(TBS)"
        },
        {
            "id": "109",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "BJMC worker Account"
        },
        {
            "id": "110",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "BMEB Account"
        },
        {
            "id": "111",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Current Account"
        },
        {
            "id": "112",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Double Benefit Scheme (DBS)"
        },
        {
            "id": "113",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Education Deposit Scheme"
        },
        {
            "id": "114",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Farmer Account"
        },
        {
            "id": "115",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Fixed Deposit"
        },
        {
            "id": "116",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Freedom Fighter Account"
        },
        {
            "id": "117",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Kallyan Bhata (DDP) Account"
        },
        {
            "id": "118",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Marriage Savings Scheme"
        },
        {
            "id": "119",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Medicare Deposit Scheme"
        },
        {
            "id": "120",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Monthly Earning Scheme(MES)"
        },
        {
            "id": "121",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Non-Resident Deposit Scheme"
        },
        {
            "id": "122",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Poor Women Account"
        },
        {
            "id": "123",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Railway Account"
        },
        {
            "id": "124",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "RMG Workers Account"
        },
        {
            "id": "125",
            "call_category_id": "41",
            "call_type_id": "3",
            "name": "BKash Link"
        },
        {
            "id": "126",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Rural Deposit Scheme"
        },
        {
            "id": "127",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Savings Account"
        },
        {
            "id": "128",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "SBL Retirement Savings Scheme"
        },
        {
            "id": "129",
            "call_category_id": "42",
            "call_type_id": "1",
            "name": "BKash Link"
        },
        {
            "id": "130",
            "call_category_id": "43",
            "call_type_id": "2",
            "name": "BKash Link"
        },
        {
            "id": "131",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "School Banking Account"
        },
        {
            "id": "132",
            "call_category_id": "44",
            "call_type_id": "3",
            "name": "Govt Investment SUKUK"
        },
        {
            "id": "133",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Shadheen Sanchay Scheme"
        },
        {
            "id": "134",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "SND Account"
        },
        {
            "id": "135",
            "call_category_id": "44",
            "call_type_id": "3",
            "name": "Govt T-Bill/Bond"
        },
        {
            "id": "136",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Sonali Bank Daily Profit scheme"
        },
        {
            "id": "137",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Sonali Bank Millionaire Scheme"
        },
        {
            "id": "138",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Sonali Deposite Scheme"
        },
        {
            "id": "139",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Student Savings Account"
        },
        {
            "id": "140",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Triple Benefit Scheme(TBS)"
        },
        {
            "id": "141",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "CC Hypo"
        },
        {
            "id": "142",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Export"
        },
        {
            "id": "143",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Firm Contract Export Policy"
        },
        {
            "id": "144",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "CC Pledge"
        },
        {
            "id": "145",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Export Bill Negotiation"
        },
        {
            "id": "146",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Unrepatriated Export bill"
        },
        {
            "id": "147",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Usance Export Bill"
        },
        {
            "id": "148",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Exchange Risk"
        },
        {
            "id": "149",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Import"
        },
        {
            "id": "150",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "Education Loan"
        },
        {
            "id": "151",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Letter Of Credit"
        },
        {
            "id": "152",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Pre Import Loan"
        },
        {
            "id": "153",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "Foreign Education Loan Program"
        },
        {
            "id": "154",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Post Import Loan"
        },
        {
            "id": "155",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Post Import Financing"
        },
        {
            "id": "156",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Online Real Time Banking System"
        },
        {
            "id": "157",
            "call_category_id": "46",
            "call_type_id": "3",
            "name": "Loan Against Inland Bill"
        },
        {
            "id": "158",
            "call_category_id": "47",
            "call_type_id": "3",
            "name": "Sanchaypatra"
        },
        {
            "id": "159",
            "call_category_id": "48",
            "call_type_id": "2",
            "name": "Sanchaypatra"
        },
        {
            "id": "160",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "Foreign Employment Loan"
        },
        {
            "id": "161",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "Freedom Fighter Loan"
        },
        {
            "id": "162",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "HBL BPDB"
        },
        {
            "id": "163",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "HBL Construction"
        },
        {
            "id": "164",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "HBL Flat Purchase"
        },
        {
            "id": "165",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "HBL Govt Employee"
        },
        {
            "id": "166",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "HBL Justice Sir"
        },
        {
            "id": "167",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "HBL Public University"
        },
        {
            "id": "168",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "HBL Sonali Neer"
        },
        {
            "id": "169",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "Personal Loan"
        },
        {
            "id": "170",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "Secured Overdraft"
        },
        {
            "id": "171",
            "call_category_id": "49",
            "call_type_id": "3",
            "name": "USD Investment Bond"
        },
        {
            "id": "172",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "Small Business Loan"
        },
        {
            "id": "173",
            "call_category_id": "45",
            "call_type_id": "3",
            "name": "Special Small Credit"
        },
        {
            "id": "174",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "Education Loan"
        },
        {
            "id": "175",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "Foreign Education Loan Program"
        },
        {
            "id": "176",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "Foreign Employment Loan"
        },
        {
            "id": "177",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "Freedom Fighter Loan"
        },
        {
            "id": "178",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "HBL BPDB"
        },
        {
            "id": "179",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "HBL Construction"
        },
        {
            "id": "180",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "HBL Flat Purchase"
        },
        {
            "id": "181",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "HBL Govt Employee"
        },
        {
            "id": "182",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "HBL Justice Sir"
        },
        {
            "id": "183",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "HBL Public University"
        },
        {
            "id": "184",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "HBL Sonali Neer"
        },
        {
            "id": "185",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "Personal Loan"
        },
        {
            "id": "186",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "Small Business Loan"
        },
        {
            "id": "187",
            "call_category_id": "50",
            "call_type_id": "2",
            "name": "Special Small Credit"
        },
        {
            "id": "188",
            "call_category_id": "49",
            "call_type_id": "3",
            "name": "USD Premium Bond"
        },
        {
            "id": "189",
            "call_category_id": "49",
            "call_type_id": "3",
            "name": "Wage Earner Development Bond"
        },
        {
            "id": "190",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "10/50/100TK Refinance Scheme"
        },
        {
            "id": "191",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Bicycle Loan"
        },
        {
            "id": "192",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "BRDB Loan"
        },
        {
            "id": "193",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Covid-19 Re-finance"
        },
        {
            "id": "194",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "CUMED"
        },
        {
            "id": "195",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "Online Service"
        },
        {
            "id": "196",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "7 Colleges Fees"
        },
        {
            "id": "197",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "BHBFC"
        },
        {
            "id": "198",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Daridro Bimochon Loan"
        },
        {
            "id": "199",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Ghore Fera Refinance Scheme"
        },
        {
            "id": "200",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Gramin Khudro Babsa Loan"
        },
        {
            "id": "201",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Jago Nari"
        },
        {
            "id": "202",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Lobon Loan"
        },
        {
            "id": "203",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "NGO Linkage Revolving"
        },
        {
            "id": "204",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "NGO Linkage Wholesale"
        },
        {
            "id": "205",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Protibondhi Loan"
        },
        {
            "id": "206",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "BTEB Admission"
        },
        {
            "id": "207",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Small Farming Loan"
        },
        {
            "id": "208",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "BTEB Institute Payment"
        },
        {
            "id": "209",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Swanirvor Loan"
        },
        {
            "id": "210",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Unmesh Loan"
        },
        {
            "id": "211",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "BUET Fees"
        },
        {
            "id": "212",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "BUTEX Fees"
        },
        {
            "id": "213",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "Customs Bond Dhaka"
        },
        {
            "id": "214",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "Customs Duty"
        },
        {
            "id": "215",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "Customs duty e-Payment"
        },
        {
            "id": "216",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Swanirvor Loan"
        },
        {
            "id": "217",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "Dhaka Polytechnic Fees"
        },
        {
            "id": "218",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Small Farming Loan"
        },
        {
            "id": "219",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "Education"
        },
        {
            "id": "220",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "HSC Fees"
        },
        {
            "id": "221",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "10/50/100TK Refinance Scheme"
        },
        {
            "id": "222",
            "call_category_id": "51",
            "call_type_id": "3",
            "name": "Bicycle Loan"
        },
        {
            "id": "223",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "BRDB-UCC"
        },
        {
            "id": "224",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "Covid-19 Re-finance"
        },
        {
            "id": "225",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "CUMED"
        },
        {
            "id": "226",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "Daridro Bimochon Loan"
        },
        {
            "id": "227",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "Ghore Fera Refinance Scheme"
        },
        {
            "id": "228",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "Gramin Khudro Babsa Loan"
        },
        {
            "id": "229",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "Jago Nari"
        },
        {
            "id": "230",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "Lobon Loan"
        },
        {
            "id": "231",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "NGO Linkage Revolving"
        },
        {
            "id": "232",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "NGO Linkage Wholesale"
        },
        {
            "id": "233",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "Protibondhi Loan"
        },
        {
            "id": "234",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "Swanirvor Loan"
        },
        {
            "id": "235",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "Unmesh Loan"
        },
        {
            "id": "236",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "BB Start Up Fund"
        },
        {
            "id": "237",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Cluster Financing"
        },
        {
            "id": "238",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "CMS Credit Guarantee Scheme"
        },
        {
            "id": "239",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "CMSME"
        },
        {
            "id": "240",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "CMSME COVID-19 Finance"
        },
        {
            "id": "241",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "CMSME Credit Guarantee"
        },
        {
            "id": "242",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "CMSME Refinance Credit Guarantee"
        },
        {
            "id": "243",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "CMSME Refinance Scheme"
        },
        {
            "id": "244",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Covid-19 Incentives"
        },
        {
            "id": "245",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Good Borrower"
        },
        {
            "id": "246",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Interest Waiver"
        },
        {
            "id": "247",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Loan Classification"
        },
        {
            "id": "248",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "New Entrepreneur"
        },
        {
            "id": "249",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Nipuna"
        },
        {
            "id": "250",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Project Loans"
        },
        {
            "id": "251",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Refinance scheme green product"
        },
        {
            "id": "252",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "SBL Startup Fund"
        },
        {
            "id": "253",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Single Borrower & Large Loan Limit"
        },
        {
            "id": "254",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Sonali Alo"
        },
        {
            "id": "255",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Women Entrepreneurs"
        },
        {
            "id": "256",
            "call_category_id": "54",
            "call_type_id": "3",
            "name": "Working Capital Loan"
        },
        {
            "id": "257",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "SND Account"
        },
        {
            "id": "258",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "7 Colleges Fees"
        },
        {
            "id": "259",
            "call_category_id": "56",
            "call_type_id": "3",
            "name": "Floriculture Loan"
        },
        {
            "id": "260",
            "call_category_id": "56",
            "call_type_id": "3",
            "name": "Farming Off Farming Loan"
        },
        {
            "id": "261",
            "call_category_id": "57",
            "call_type_id": "2",
            "name": "Small Farming Loan"
        },
        {
            "id": "262",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "BB Start Up Fund"
        },
        {
            "id": "263",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "CMSME"
        },
        {
            "id": "264",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "CMSME Cluster Loan"
        },
        {
            "id": "265",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "CMSME COVID-19 Finance"
        },
        {
            "id": "266",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "CMSME COVID-19 Re-Finance"
        },
        {
            "id": "267",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "CMSME Refinance Scheme"
        },
        {
            "id": "268",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "Nari Uddokta"
        },
        {
            "id": "269",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "New Entrepreneur"
        },
        {
            "id": "270",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "Nipuna"
        },
        {
            "id": "271",
            "call_category_id": "56",
            "call_type_id": "3",
            "name": "Social Forestry Loan"
        },
        {
            "id": "272",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "Refinance scheme green product"
        },
        {
            "id": "273",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "SBL Start Up Fund"
        },
        {
            "id": "274",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "SME Women"
        },
        {
            "id": "275",
            "call_category_id": "58",
            "call_type_id": "2",
            "name": "Sonali Alo"
        },
        {
            "id": "276",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "BHBFC"
        },
        {
            "id": "277",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "BTEB Admission"
        },
        {
            "id": "278",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "BTEB Institute Payment"
        },
        {
            "id": "279",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "BUET Fees"
        },
        {
            "id": "280",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "BUTEX Fees"
        },
        {
            "id": "281",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "Customs Bond Dhaka"
        },
        {
            "id": "282",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "Customs Duty"
        },
        {
            "id": "283",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "Customs duty e-Payment"
        },
        {
            "id": "284",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "Dhaka Polytechnic Fees"
        },
        {
            "id": "285",
            "call_category_id": "56",
            "call_type_id": "3",
            "name": "Krishi Khamar Loan"
        },
        {
            "id": "286",
            "call_category_id": "56",
            "call_type_id": "3",
            "name": "Pukure Motso Chash Loan"
        },
        {
            "id": "287",
            "call_category_id": "56",
            "call_type_id": "3",
            "name": "RCD Policy"
        },
        {
            "id": "288",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "HSC Fees"
        },
        {
            "id": "289",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "Income Tax"
        },
        {
            "id": "290",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "JKKNIU Fees"
        },
        {
            "id": "291",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "Income Tax"
        },
        {
            "id": "293",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "JKKNIU Fees"
        },
        {
            "id": "294",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "National University College Fees"
        },
        {
            "id": "295",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "National University College Fees"
        },
        {
            "id": "296",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "NBR Sonali Bank e-Payment Portal"
        },
        {
            "id": "297",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "NBR Sonali Bank e-Payment Portal"
        },
        {
            "id": "298",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "Police Clearance"
        },
        {
            "id": "299",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "Police Clearance"
        },
        {
            "id": "300",
            "call_category_id": "60",
            "call_type_id": "2",
            "name": "Sonali Payment Gateway"
        },
        {
            "id": "301",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "TTC Fees"
        },
        {
            "id": "302",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "TTC Fees"
        },
        {
            "id": "303",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "VAT Online"
        },
        {
            "id": "304",
            "call_category_id": "55",
            "call_type_id": "1",
            "name": "VAT Online"
        },
        {
            "id": "305",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "XI Class Admission"
        },
        {
            "id": "306",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Agrani Exchange Singapore"
        },
        {
            "id": "307",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Agrani Remittance Malaysia"
        },
        {
            "id": "308",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Bahrain Financing Company"
        },
        {
            "id": "309",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "CBL Money Transfer Malaysia"
        },
        {
            "id": "310",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Exchange House List"
        },
        {
            "id": "311",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "FC Account"
        },
        {
            "id": "312",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "First Security Islami Exchange Italy"
        },
        {
            "id": "313",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Global Money Exchange Company LLC Oman"
        },
        {
            "id": "314",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Hello Paisa Pty Ltd South Africa"
        },
        {
            "id": "315",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Instant Cash FZE UAE"
        },
        {
            "id": "316",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Merchantrade Asia Malaysia"
        },
        {
            "id": "317",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "MoneyGram International"
        },
        {
            "id": "318",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "National Exchange Company"
        },
        {
            "id": "319",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "NBL Money Transfer Malaysia"
        },
        {
            "id": "320",
            "call_category_id": "57",
            "call_type_id": "2",
            "name": "Floriculture Loan"
        },
        {
            "id": "321",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "NBL Money Transfer Pte Ltd Singapore"
        },
        {
            "id": "322",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "NEC Money Transfer Ltd UK"
        },
        {
            "id": "323",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "NRB Account"
        },
        {
            "id": "324",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Placid Express Malaysia"
        },
        {
            "id": "325",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Prabhu Money Transfer USA"
        },
        {
            "id": "326",
            "call_category_id": "57",
            "call_type_id": "2",
            "name": "Farming-Off Farming Loan"
        },
        {
            "id": "327",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Prime Exchange Co Pte Ltd"
        },
        {
            "id": "328",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Purushottam Kanij Exchange Co. L.L.C Muscat Oman"
        },
        {
            "id": "329",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "RIA Financial Services"
        },
        {
            "id": "330",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "SAMBA Financial Group KSA"
        },
        {
            "id": "331",
            "call_category_id": "57",
            "call_type_id": "2",
            "name": "Social Forestry Loan"
        },
        {
            "id": "332",
            "call_category_id": "57",
            "call_type_id": "2",
            "name": "Krishi Khamar Loan"
        },
        {
            "id": "333",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Student File"
        },
        {
            "id": "334",
            "call_category_id": "57",
            "call_type_id": "2",
            "name": "Pukure Motso Chash Loan"
        },
        {
            "id": "335",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Tap Tap Send UK Limited"
        },
        {
            "id": "336",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Tranglo Sdn Bhd Malaysia"
        },
        {
            "id": "337",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Trans Fast"
        },
        {
            "id": "338",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Transcash International Pty Ltd Australia (Trading as iPay)"
        },
        {
            "id": "339",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "U Remit International Corporation Canada"
        },
        {
            "id": "340",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Western Union"
        },
        {
            "id": "341",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Worldwide West 2 East Service Ltd UK (Trading as SHA Global)"
        },
        {
            "id": "342",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Xpress Money"
        },
        {
            "id": "343",
            "call_category_id": "62",
            "call_type_id": "3",
            "name": "eSheba"
        },
        {
            "id": "344",
            "call_category_id": "63",
            "call_type_id": "2",
            "name": "eSheba"
        },
        {
            "id": "345",
            "call_category_id": "64",
            "call_type_id": "1",
            "name": "eSheba"
        },
        {
            "id": "346",
            "call_category_id": "62",
            "call_type_id": "3",
            "name": "e-Wallet"
        },
        {
            "id": "347",
            "call_category_id": "63",
            "call_type_id": "2",
            "name": "e-Wallet"
        },
        {
            "id": "348",
            "call_category_id": "64",
            "call_type_id": "1",
            "name": "e-Wallet"
        },
        {
            "id": "349",
            "call_category_id": "65",
            "call_type_id": "3",
            "name": "Mudaraba Savings Account"
        },
        {
            "id": "350",
            "call_category_id": "65",
            "call_type_id": "3",
            "name": "AL-Wadia Current Account"
        },
        {
            "id": "351",
            "call_category_id": "65",
            "call_type_id": "3",
            "name": "Mudaraba Short Notice Deposit"
        },
        {
            "id": "352",
            "call_category_id": "65",
            "call_type_id": "3",
            "name": "Hire Purchase Under Shirkatul Milk"
        },
        {
            "id": "353",
            "call_category_id": "65",
            "call_type_id": "3",
            "name": "Bai Muajjal HDS"
        },
        {
            "id": "354",
            "call_category_id": "65",
            "call_type_id": "3",
            "name": "Mudaraba Hajj Savings Scheme"
        },
        {
            "id": "355",
            "call_category_id": "65",
            "call_type_id": "3",
            "name": "Sonali Monthly Dowry Deposit Scheme"
        },
        {
            "id": "356",
            "call_category_id": "65",
            "call_type_id": "3",
            "name": "Sonali Monthly Deposit Scheme"
        },
        {
            "id": "357",
            "call_category_id": "65",
            "call_type_id": "3",
            "name": "Mudaraba Monthly Profit Scheme"
        },
        {
            "id": "358",
            "call_category_id": "65",
            "call_type_id": "3",
            "name": "Mudaraba Term Deposit Receipt"
        },
        {
            "id": "359",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "AL-Wadia Current Account"
        },
        {
            "id": "361",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "Bai Muajjal Hypothication"
        },
        {
            "id": "362",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "Bai Muajjal-Household Durables Scheme"
        },
        {
            "id": "363",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "Foreign Remittance"
        },
        {
            "id": "364",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "Hire Purchase Under Shirkatul Milk"
        },
        {
            "id": "365",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "Mudaraba Hajj Savings Scheme"
        },
        {
            "id": "366",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "Mudaraba Monthly Profit Scheme"
        },
        {
            "id": "367",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "Mudaraba Savings Account"
        },
        {
            "id": "368",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "Mudaraba Short Notice Deposit"
        },
        {
            "id": "369",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "Mudaraba Term Deposit Receipt"
        },
        {
            "id": "370",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "Sonali Monthly Deposit Scheme"
        },
        {
            "id": "371",
            "call_category_id": "66",
            "call_type_id": "2",
            "name": "Sonali Monthly Dowry Deposit Scheme"
        },
        {
            "id": "372",
            "call_category_id": "53",
            "call_type_id": "2",
            "name": "Bicycle Loan"
        },
        {
            "id": "373",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "BB Startup Fund"
        },
        {
            "id": "374",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "Cluster Financing"
        },
        {
            "id": "375",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "CMSME"
        },
        {
            "id": "376",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "CMSME COVID-19 Finance"
        },
        {
            "id": "377",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "CMSME Refinance Scheme"
        },
        {
            "id": "378",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "New Entrepreneurs"
        },
        {
            "id": "379",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "Nipuna"
        },
        {
            "id": "380",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "Project Loans"
        },
        {
            "id": "381",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "Refinance scheme green products"
        },
        {
            "id": "382",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "SBL Startup Fund"
        },
        {
            "id": "383",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "Sonali Alo"
        },
        {
            "id": "384",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "Women Entrepreneurs"
        },
        {
            "id": "385",
            "call_category_id": "67",
            "call_type_id": "5",
            "name": "Working Capital Loan"
        },
        {
            "id": "386",
            "call_category_id": "68",
            "call_type_id": "5",
            "name": "Agent Banking  Entrepreneur"
        },
        {
            "id": "387",
            "call_category_id": "69",
            "call_type_id": "5",
            "name": "Credit Card"
        },
        {
            "id": "388",
            "call_category_id": "69",
            "call_type_id": "5",
            "name": "Prepaid Card"
        },
        {
            "id": "389",
            "call_category_id": "69",
            "call_type_id": "5",
            "name": "Debit Card"
        },
        {
            "id": "390",
            "call_category_id": "70",
            "call_type_id": "5",
            "name": "Loan Against Inland Bill"
        },
        {
            "id": "391",
            "call_category_id": "70",
            "call_type_id": "5",
            "name": "Pre Import Loan"
        },
        {
            "id": "392",
            "call_category_id": "70",
            "call_type_id": "5",
            "name": "Post Import Loan"
        },
        {
            "id": "393",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "AL-Wadia Current Account"
        },
        {
            "id": "394",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "Bai Muajjal HDS"
        },
        {
            "id": "395",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "Hire Purchase Under Shirkatul Milk"
        },
        {
            "id": "396",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "Mudaraba Hajj Savings Scheme"
        },
        {
            "id": "397",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "Mudaraba Monthly Profit Scheme"
        },
        {
            "id": "398",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "Mudaraba Savings Account"
        },
        {
            "id": "399",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "Mudaraba Short Notice Deposit"
        },
        {
            "id": "400",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "Mudaraba Term Deposit Receipt"
        },
        {
            "id": "401",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "Sonali Monthly Deposit Scheme"
        },
        {
            "id": "402",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "Sonali Monthly Dowry Deposit Scheme"
        },
        {
            "id": "403",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "CC Hypo"
        },
        {
            "id": "404",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "CC Hypo"
        },
        {
            "id": "405",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "CC Pledge"
        },
        {
            "id": "406",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "Education Loan"
        },
        {
            "id": "407",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "Foreign Education Loan Program"
        },
        {
            "id": "408",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "Foreign Employment Loan"
        },
        {
            "id": "409",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "Freedom Fighter Loan"
        },
        {
            "id": "410",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "HBL BPDB"
        },
        {
            "id": "411",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "HBL Construction"
        },
        {
            "id": "412",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "HBL Flat Purchase"
        },
        {
            "id": "413",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "HBL Govt EMP"
        },
        {
            "id": "414",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "HBL Justice Sir"
        },
        {
            "id": "415",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "HBL Public University"
        },
        {
            "id": "416",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "HBL Sonali Neer"
        },
        {
            "id": "417",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "IPFD"
        },
        {
            "id": "418",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "NGO Linkage Revolving"
        },
        {
            "id": "419",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "Personal Loan"
        },
        {
            "id": "420",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "Secured Overdraft"
        },
        {
            "id": "421",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "Small Business Loan"
        },
        {
            "id": "422",
            "call_category_id": "72",
            "call_type_id": "5",
            "name": "Special Small Credit"
        },
        {
            "id": "423",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "BJMC worker A/C"
        },
        {
            "id": "424",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "BMEB teacher A/C"
        },
        {
            "id": "425",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Current Account"
        },
        {
            "id": "426",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "DDP"
        },
        {
            "id": "427",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Doubble Benefit Scheme"
        },
        {
            "id": "428",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Education Deposit Scheme"
        },
        {
            "id": "429",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Farmer A/C"
        },
        {
            "id": "430",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Fixed Deposit Receipt"
        },
        {
            "id": "431",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Freedom Fighter A/C"
        },
        {
            "id": "432",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Garments Worker A/C"
        },
        {
            "id": "433",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Marriage Savings Scheme"
        },
        {
            "id": "434",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Medicare Deposit Scheme"
        },
        {
            "id": "435",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Monthly Earning Scheme"
        },
        {
            "id": "436",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "NRDS"
        },
        {
            "id": "437",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Poor Women A/C"
        },
        {
            "id": "438",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Railway A/C"
        },
        {
            "id": "439",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Rural Deposit Scheme"
        },
        {
            "id": "440",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Savings Account"
        },
        {
            "id": "441",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "SB Daily Profit Scheme"
        },
        {
            "id": "442",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "SB Millionaire Scheme"
        },
        {
            "id": "443",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "SBRSS"
        },
        {
            "id": "444",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "School Banking"
        },
        {
            "id": "445",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Shadheen Sanchay Scheme"
        },
        {
            "id": "446",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "SND Account"
        },
        {
            "id": "447",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Sonali Deposit Scheme"
        },
        {
            "id": "448",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "10/50/100 TK Refinance Loan"
        },
        {
            "id": "449",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Bicycle Loan"
        },
        {
            "id": "450",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "BRDB Loan"
        },
        {
            "id": "451",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Daridro Bimochon Loan"
        },
        {
            "id": "452",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Farming Off Farming Loan"
        },
        {
            "id": "453",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Floriculture Loan"
        },
        {
            "id": "454",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Ghore Fira Loan"
        },
        {
            "id": "455",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Gramin Khudro Babsa Loan"
        },
        {
            "id": "456",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Jago Nari"
        },
        {
            "id": "457",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Krishi Khamar Loan"
        },
        {
            "id": "458",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Lobon Loan"
        },
        {
            "id": "459",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "MCD Covid-19 Refinance"
        },
        {
            "id": "460",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "NGO Linkage Wholesale"
        },
        {
            "id": "461",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Protibondhi Loan"
        },
        {
            "id": "462",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Pukure Motso Chash Loan"
        },
        {
            "id": "463",
            "call_category_id": "71",
            "call_type_id": "5",
            "name": "Small Farming Loan"
        },
        {
            "id": "464",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Social Forestry Loan"
        },
        {
            "id": "465",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Swanirvor Loan"
        },
        {
            "id": "466",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Unmesh Loan"
        },
        {
            "id": "467",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Student Account"
        },
        {
            "id": "468",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Triple Benefit Scheme"
        },
        {
            "id": "469",
            "call_category_id": "75",
            "call_type_id": "2",
            "name": "Foreign Remittance"
        },
        {
            "id": "470",
            "call_category_id": "76",
            "call_type_id": "2",
            "name": "PO"
        },
        {
            "id": "471",
            "call_category_id": "76",
            "call_type_id": "2",
            "name": "DD"
        },
        {
            "id": "472",
            "call_category_id": "76",
            "call_type_id": "2",
            "name": "FDD"
        },
        {
            "id": "473",
            "call_category_id": "77",
            "call_type_id": "2",
            "name": "Complaint Against Branch"
        },
        {
            "id": "474",
            "call_category_id": "77",
            "call_type_id": "2",
            "name": "Complaint Against Agent"
        },
        {
            "id": "475",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Garments Worker A/C"
        },
        {
            "id": "476",
            "call_category_id": "78",
            "call_type_id": "2",
            "name": "Issuing Certificates"
        },
        {
            "id": "477",
            "call_category_id": "79",
            "call_type_id": "2",
            "name": "Schedule Payment Request"
        },
        {
            "id": "478",
            "call_category_id": "80",
            "call_type_id": "2",
            "name": "Auto debit loan repayment"
        },
        {
            "id": "479",
            "call_category_id": "74",
            "call_type_id": "5",
            "name": "Small Farming Loan"
        },
        {
            "id": "480",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Foreign currency"
        },
        {
            "id": "481",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Sonali Lakhpoti Deposit Scheme"
        },
        {
            "id": "482",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Sonali Lakhpoti Deposit Scheme"
        },
        {
            "id": "483",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "DPS Info"
        },
        {
            "id": "484",
            "call_category_id": "65",
            "call_type_id": "3",
            "name": "Hajj and Omrah Investment"
        },
        {
            "id": "485",
            "call_category_id": "61",
            "call_type_id": "3",
            "name": "Bonus on Remittance"
        },
        {
            "id": "486",
            "call_category_id": "81",
            "call_type_id": "3",
            "name": "Pay Order"
        },
        {
            "id": "487",
            "call_category_id": "81",
            "call_type_id": "3",
            "name": "Demand Draft"
        },
        {
            "id": "488",
            "call_category_id": "81",
            "call_type_id": "3",
            "name": "Foreign Demand Draft"
        },
        {
            "id": "489",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Sonali Lakhpoti Deposit Scheme"
        },
        {
            "id": "490",
            "call_category_id": "15",
            "call_type_id": "3",
            "name": "Sonali Ananna Deposit Scheme"
        },
        {
            "id": "491",
            "call_category_id": "73",
            "call_type_id": "5",
            "name": "Sonali Ananna Deposit Scheme"
        },
        {
            "id": "492",
            "call_category_id": "40",
            "call_type_id": "2",
            "name": "Sonali Ananna Deposit Scheme"
        },
        {
            "id": "493",
            "call_category_id": "4",
            "call_type_id": "1",
            "name": "BEFTN"
        },
        {
            "id": "494",
            "call_category_id": "4",
            "call_type_id": "1",
            "name": "BACH"
        },
        {
            "id": "495",
            "call_category_id": "82",
            "call_type_id": "3",
            "name": "Universal Pension Scheme"
        },
        {
            "id": "496",
            "call_category_id": "19",
            "call_type_id": "3",
            "name": "Sonali Bank Related"
        },
        {
            "id": "497",
            "call_category_id": "11",
            "call_type_id": "3",
            "name": "Taka Pay"
        },
        {
            "id": "498",
            "call_category_id": "62",
            "call_type_id": "3",
            "name": "Sonali Exchange Mobile App"
        },
        {
            "id": "499",
            "call_category_id": "52",
            "call_type_id": "3",
            "name": "Probashi Kallyan Bank"
        }
    ],
    "message": null
}';
        return $dummyResponse;

        $response = json_decode($dummyResponse, true);

        $callTypes = $response['data'] ?? [];
        $options = [];
        foreach ($callTypes as $type) {
            $options[$type['id']] = $type['name'];
        }

        return $options;
    }

    public static function dummyResponseForGetSubSubCategoryDropDownForCallCategoryApi()
    {
        return config('get-call-sub-sub.response');

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
        // dd('data', $data);
        /*array:9 [ // app/Http/Controllers/ApiController.php:5550
        "mobile_no" => null
        "purpose" => "CREATEISSUE"
        "page" => "home"
        "button" => "btnCreateIssue"
        "callTypeOpts" => "1"
        "callCategoryOpts" => "8"
        "callSubCategoryOpts" => "17"
        "callSubSubCategoryOpts" => "111"
        "reason" => "asdadadad"
        ]*/

        $callTypeId = $data['callTypeOpts'];
        $callCategoryIdInfo = $data['callCategoryOpts'];
        $callSubCategoryInfo = $data['callSubCategoryOpts'];
        $callSubSubCategoryInfo = $data['callSubSubCategoryOpts'];
        $remarks = $data['reason'];

        $logInfo = Session::get('logInfo');
        $userName = trim(data_get($logInfo, 'account_info.accountName', "Guest-User-From-VIVR"));
        $userPhone = trim(data_get($logInfo, 'otp_info.otp_phone', "NA"));

        $appParamsData = [
            'channel_id' => 1,
            'idesk_agent_id' => 1,
            'cus_name' => $userName,
            'cus_contact_no' => $userPhone,
            'call_type' => $callTypeId,
            'call_category' => $callCategoryIdInfo,
            'call_sub_category' => $callSubCategoryInfo,
            'call_sub_subcategory' => $callSubSubCategoryInfo,
            'remarks' => $remarks,
            'account_no' => null, // or you can set a default value if needed
        ];

        /*// dummy response. will be removed later.
        return [
            'code' => 200,
            'status' => 'success',
            'message' => 'Data Found.',
            'prompt' => null,
            'data' => [
                'issueId' => uniqid(),
            ]
        ];*/

        $apiResponse = self::processToCreateTicketInCRM($appParamsData);
        $ticketId = $apiResponse['data']['ticketId'] ?? "";
        // $ticketMessage = $apiResponse['data']['ticketMessage'] ?? "";

        return [
            'code' => $apiResponse['code'],
            'status' => $apiResponse['status'],
            'message' => $apiResponse['message'],
            'prompt' => $apiResponse['prompt'],
            'data' => [
                'issueId' => $ticketId,
            ]
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
            'page' => 'nullable',
            'button' => 'nullable',
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
        /*if ($purpose === "CARDACTIVATE") {
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
        }*/

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
            case 'EW-APPROVE-OR-REJECT':
                return self::processApiCallingEWApproveOrReject($data);
            case 'EW-DEVICE-BIND':
                return self::processApiCallingEWDeviceBind($data);
            case 'EW-CLOSE-WALLET':
                return self::processApiCallingEWCloseWallet($data);
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
            case 'IB-AR-CHEQUE-BOOK-LEAF-STOP-PAYMENT':
                return self::processApiCallingIBARChequeBookLeafStopPaymentClick($data);
            case 'GET-CALL-TYPES-DROPDOWN-VALUES':
                return self::processGetCallTypesDropDownValues($data);
            case 'GET-CALL-CATEGORY-OPTIONS':
                return self::processGetCallCategoryDropDownValues($data);
            case 'GET-SUB-CATEGORY-OPTIONS':
                return self::processGetSubCategoryDropDownValues($data);
            case 'GET-SUB-SUB-CATEGORY-OPTIONS':
                return self::processGetSubSubCategoryDropDownValues($data);
            case 'CREATEISSUE':
                return self::processApiCallingCreateIssue($data);
            case 'USER-INFO-VERIFY':
                return self::processApiCallingUserInfoVerify($data);
            case 'RESEND-OTP':
                return self::processApiCallingReSendOTP($data);
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
