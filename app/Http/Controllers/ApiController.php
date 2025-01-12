<?php

namespace App\Http\Controllers;

use App\Handlers\APIHandler;
use App\Handlers\EncryptionHandler;
use App\Models\OtpHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ApiController extends ResponseController
{
    const OTP_PHONE_KEY = 'otp_info.otp_phone';
    const ACCOUNT_VERIFICATION_STATUS_KEY = 'account_verification_status';

    public function getBalance(Request $request)
    {
        $phoneNumber = data_get(Session::get('logInfo'), 'otp_info.otp_phone') ?? 'NA';
        $getSelectedAcc = data_get(Session::get('logInfo'), 'selected_accEnc');
        $getDecryptedAccount = openSSLEncryptDecrypt($getSelectedAcc, 'decrypt');
        $type = 'ACCOUNT_BALANCE';

        $responseOut = [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'balance' => 0,
            'prompt' => null,
            // 'getAcc' => $getDecryptedAccount
        ];

        // $response = self::fetchGetWalletDetails($phoneNumber);
        $response = self::fetchAccountFullDetails($getDecryptedAccount);

        if ($response['code'] === Response::HTTP_OK && $response['status'] === 'success') {

            if (!createUserTicketHistory($type, $phoneNumber, $type, $getDecryptedAccount)) { // Ticket Creation Failed

                ['message' => $message] = getExecutionTime($type);
                return [
                    'code' => Response::HTTP_FORBIDDEN,
                    'status' => 'error',
                    'message' => $message,
                    'prompt' => null,
                    'balance' => null,
                ];
            }

            $balance = !empty($response['data']['balance']) ? $response['data']['balance'] : 0;
            $responseOut['code'] = $response['code'];
            $responseOut['status'] = 'success';
            $responseOut['balance'] = number_format($balance, 2);
        }

        return $this->sendResponse($responseOut, $responseOut['code']);
    }

    public function sendOtpWrapper(Request $request)
    {
        $request->validate([
            'mobile_no' => $this->phoneValidationRules(),
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
        if (!$mobileNo) {
            return;
        }

        // if mobile number isn't matched with sonali phone, then send an error message.
        $getAccountList = $this->fetchSavingsDeposits($mobileNo);
        $acLists = $getAccountList['accountList'] ?? [];
        if (empty($acLists) || !is_array($acLists)) { // no account matched with the phone number.
            $responseOut = [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => __('messages.no-account-matched-with-phone'),
                'prompt' => null,
            ];
            Log::info('NO-ACCOUNT-MATCHED-WITH-PHONE-DURING-SEND-OTP|' . $mobileNo);
            return $this->sendResponse($responseOut, $responseOut['code']);
        }

        try {
            $apiHandler = new APIHandler();
            $url = config('api.base_url') . config('api.send_otp_url');
            $strRefId = $mobileNo . randomDigits();

            $apiPayload = [
                'strRefId' => $strRefId,
                'strMobileNo' => $mobileNo,
                'isEncPwd' => true,
            ];

            if ($isResend) {
                Log::info('RE-SEND-OTP-API-CALLED : ' . json_encode($apiPayload));
            }
            $response = $apiHandler->postCall($url, $apiPayload);
            if ($response['status'] === 'success' && $response['statusCode'] === Response::HTTP_OK) {

                Log::info('SEND-OTP-API-RESPONSE: ' . json_encode($response['data']));

                $firstSendData = json_decode($response['data']);
                $secondSendData = json_decode($firstSendData);
                $sendStatusCode = (int) $secondSendData->StatusCode;

                if ($sendStatusCode === Response::HTTP_OK) { // OTP SEND SUCCESS
                    Session::put('otp', [
                        'phone_masked' => $this->hidePhoneNumber($mobileNo),
                        'otp_phone' => $mobileNo,
                        'strRefId' => $strRefId,
                    ]);

                    $responseOut = [
                        'code' => $sendStatusCode,
                        'status' => 'success',
                        'message' => __('messages.otp-send-success'),
                        'url' => url('verify-otp'),
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);

                } else {
                    $responseOut = [
                        'code' => Response::HTTP_EXPECTATION_FAILED,
                        'status' => 'error',
                        'message' => __('messages.apologies-something-went-wrong'),
                        'prompt' => getPromptPath('common/request-failed-en'),
                    ];
                    return $this->sendResponse($responseOut, $responseOut['code']);
                }

            } else { // UNEXPECTED API RESPONSE
                $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
                Log::error('API ERROR:: ' . $msg);
                $responseOut = [
                    'code' => Response::HTTP_EXPECTATION_FAILED,
                    'status' => 'error',
                    'message' => __('messages.apologies-something-went-wrong'),
                    'prompt' => getPromptPath('common/request-failed-en'),
                ];
                return $this->sendResponse($responseOut, $responseOut['code']);
            }
        } catch (Exception $e) {
            $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
            Log::error('SEND-OTP-API ERROR:: ' . $msg);
            $responseOut = [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => __('messages.apologies-something-went-wrong'),
                'prompt' => getPromptPath('common/request-failed-en'),
            ];
            return $this->sendResponse($responseOut, $responseOut['code']);
        }

    }

    public function sendOtpWrapperNew(Request $request)
    {
        $request->validate([
            'mobile_no' => $this->phoneValidationRules(),
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
                            'prompt' => null,
                        ];
                        return $this->sendResponse($responseOut, $responseOut['code']);

                    } else if ($statusCode === Response::HTTP_OK) { // API SEND SUCCESS RESPONSE

                        Session::put('otp', [
                            'phone_masked' => $this->hidePhoneNumber($mobileNo),
                            'otp_phone' => $mobileNo,
                            'strRefId' => $strRefId,
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
                            'url' => url('verify-otp'),
                        ];

                        return $this->sendResponse($responseOut, $responseOut['code']);

                    } else {

                        $responseOut = [
                            'code' => Response::HTTP_EXPECTATION_FAILED,
                            'status' => 'error',
                            'message' => __('messages.apologies-something-went-wrong'),
                            'prompt' => getPromptPath('common/request-failed-en'),
                        ];
                        return $this->sendResponse($responseOut, $responseOut['code']);
                    }

                } else {
                    $responseOut = [
                        'code' => Response::HTTP_EXPECTATION_FAILED,
                        'status' => 'error',
                        'message' => __('messages.apologies-something-went-wrong'), // Null response
                        'prompt' => getPromptPath('common/request-failed-en'),
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
                    'prompt' => getPromptPath('common/request-failed-en'),
                ];
                return $this->sendResponse($responseOut, $responseOut['code']);
            }

        } else {

            if ($otpStatus['reason'] === 'daily_limit_exceeded') {
                $response = [
                    'code' => Response::HTTP_FORBIDDEN,
                    'status' => 'error',
                    'message' => 'Max daily OTP count exceeded.',
                    'prompt' => null,
                ];
            } else if ($otpStatus['reason'] === 'overall_limit_exceeded') {
                $response = [
                    'code' => Response::HTTP_FORBIDDEN,
                    'status' => 'error',
                    'message' => 'Max OTP SMS count exceeded.',
                    'prompt' => null,
                ];
            } else {
                $response = [
                    'code' => Response::HTTP_FORBIDDEN,
                    'status' => 'error',
                    'message' => 'Sending OTP not allowed.',
                    'prompt' => null,
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

        $url = config('api.base_url') . config('api.verify_otp_url');

        try {
            $apiHandler = new APIHandler();

            $response = $apiHandler->postCall($url, [
                'strRequstId' => $strRefId,
                'strAcMobileNo' => $mobileNo,
                'strReOTP' => $request->input('code') ?? null,
            ]);

            if ($response['status'] === 'success' && $response['statusCode'] === 200) { // successful api response found from api handler end.

                $firstData = json_decode($response['data']);
                $secondData = json_decode($firstData);
                $apiStatus = (bool) $secondData->Status;
                $statusCode = $response['statusCode'];

                if ($statusCode === Response::HTTP_OK) {
                    if ($apiStatus === false) {
                        // Verification failed. Possible reason, OTP expired.
                        $responseOut = [
                            'code' => Response::HTTP_EXPECTATION_FAILED,
                            'status' => 'error',
                            'message' => __('messages.apologies-something-went-wrong'),
                            'prompt' => getPromptPath('common/request-failed-en'),
                        ];
                        return $this->sendResponse($responseOut, $responseOut['code']);
                    } else { // success

                        // After Verification
                        // Make the user as logged-in user, set a flag to verify the user.
                        // Call api to get user account name.

                        $getAccountList = $this->fetchSavingsDeposits($mobileNo);

                        $acLists = $getAccountList['accountList'] ?? [];
                        $acListArr = self::processMaskedAccountLists($acLists);

                        // store encrypted accountList in session
                        self::storeAcListInSession($acListArr);

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
                        'prompt' => getPromptPath('common/request-failed-en'),
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
                    'prompt' => getPromptPath('common/request-failed-en'),
                ];
                return $this->sendResponse($responseOut, $responseOut['code']);
            }
        } catch (Exception $e) {
            $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
            Log::error('API ERROR:: ' . $msg);
            $responseOut = [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => __('messages.apologies-something-went-wrong'),
                'prompt' => getPromptPath('common/request-failed-en'),
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
                'selected_accEnc' => $getSelected['accEnc'],
            ]);

            // Session::forget('otp');

            // $mobileNo = Session::get('logInfo.otp_info.otp_phone');
            $mobileNo = data_get(Session::get('logInfo'), 'otp_info.otp_phone') ?? 'NA';

            $eWalletAccountReport = $this->processEWalletAccountVerification($selectedAccount);
            Log::info('EWALLET-ACCOUNT-REPORT|' . $eWalletAccountReport . "|SELECTED-ACCOUNT|" . $selectedAccount);

            $responseOut = [
                'code' => Response::HTTP_OK,
                'status' => 'success',
                'message' => (filled($purpose) && $purpose == 'ACCOUNT-SWITCH') ? __('messages.account-switching-success') : __('messages.verification-success-after-login'),
                'prompt' => null,
                'pn' => $mobileNo,
                'an' => $accountAsData['accountName'] ?? null,
                'acn' => $accountAsData['accountNo'] ?? null,
                'url' => url('/'),
            ];

            session()->flash('status', $responseOut['status']);
            session()->flash('message', $responseOut['message']);

            return $this->sendResponse($responseOut, $responseOut['code']);
        }

        $responseOut = [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => __('messages.apologies-something-went-wrong'),
            'prompt' => getPromptPath('common/request-failed-en'),
        ];

        return $this->sendResponse($responseOut, $responseOut['code']);

    }

    private function processEWalletAccountVerification($encryptedAccountId)
    {
        try {
            $decryptedAccount = openSSLEncryptDecrypt($encryptedAccountId, 'decrypt');
            $phoneNumber = data_get(Session::get('logInfo'), self::OTP_PHONE_KEY) ?? 'NA';
            $getWalletResponse = $this->fetchGetWalletDetails($phoneNumber);

            // Log::info('DECRYPTED-ACCOUNT|' . $decryptedAccount . '|PHONE-NUMBER|' . $phoneNumber . '|GET-WALLET-RESPONSE|' . json_encode($getWalletResponse));
            $isAccountVerified = false;

            if ($getWalletResponse['status'] !== 'error') {
                $acLists = $getWalletResponse['data']['accountList'] ?? [];

                foreach ($acLists as $account) {
                    if ($account['accountNo'] === $decryptedAccount) {
                        $isAccountVerified = true;
                        break;
                    }
                }
            }

            Session::put(self::ACCOUNT_VERIFICATION_STATUS_KEY, $isAccountVerified);
            // Log::info('isAccountVerified ' . $isAccountVerified);
            return $isAccountVerified;
        } catch (Exception $e) {
            // Handle exceptions (log or rethrow if needed)
            Log::error('Error in processEWalletAccountVerification: ' . $e->getMessage());
            return false;
        }
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

        try {

            $apiHandler = new APIHandler();
            $response = $apiHandler->postCall($url, ['MobileNo' => $phoneNumber]);
            if ($response['status'] === 'success' && $response['statusCode'] === 200) {
                $data = json_decode($response['data'], true);
                if (isset($data['StatusCode']) && intval($data['StatusCode']) === Response::HTTP_OK) {
                    $accountList = $data['GetAccountList'];

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

        } catch (Exception $e) {
            $msg = $response['exceptionMessage'] ?? "Unexpected response structure.";
            Log::error('API ERROR:: ' . $msg);
            return [];
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
                    ],
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
            ],
        ];
    }

    public static function fetchAccountFullDetails($accountNumber)
    {
        if (!$accountNumber) {
            return;
        }

        $url = config('api.base_url') . config('api.get_call_center_data_url');

        try {
            $apiHandler = new APIHandler();
            $response = $apiHandler->postCall($url, [
                'ChannelId' => "SPS", 'AccountNo' => $accountNumber,
            ]);

            if ($response['status'] === 'success' && $response['statusCode'] === Response::HTTP_OK) {

                $data = json_decode($response['data'], true);
                $detailsField = $data['detailsField'][0] ?? [];

                $balanceField = $detailsField['aCC_TOTAL_BALANCEField'] ?? 0;
                $installmentSizeField = $detailsField['iNSTALLMENT_SIZEField'] ?? 0;

                $outstandingField = $detailsField['aCC_OUTSTANDING_BALANCEField'] ?? 0;
                $totalOrOutstanding = empty($balanceField) ? $outstandingField : $balanceField;

                $statementField = $detailsField['tRANSATION_DETAILSField'] ?? [];

                if (!isset($detailsField['aCC_TOTAL_BALANCEField']) || !isset($detailsField['aCC_OUTSTANDING_BALANCEField'])) {
                    Log::warning("Some required fields are missing in detailsField during Balance API Call: " . json_encode($detailsField));

                    return [
                        'status' => 'error',
                        'code' => Response::HTTP_EXPECTATION_FAILED,
                        'message' => 'Data not found.',
                        'data' => [
                            'balance' => 0,
                            'statement' => [],
                            'additional_info' => [],

                        ],
                    ];
                }

                Log::info("USER-BALANCE-FOR-ACCOUNT|$accountNumber|USER-aCC_TOTAL_BALANCEField|$balanceField|USER-aCC_OUTSTANDING_BALANCEField|$outstandingField|USER-tRANSATION_DETAILSField|" . json_encode($statementField));

                return [
                    'status' => 'success',
                    'message' => 'Data Received.',
                    'code' => Response::HTTP_OK,
                    'data' => [
                        'balance' => $totalOrOutstanding ?? 0,
                        'statement' => $statementField,
                        'additional_info' => [
                            'installment_size_field' => $installmentSizeField,
                        ],

                    ],
                ];

            }
        } catch (Exception $e) {

            return [
                'status' => 'error',
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'message' => 'Data not found.',
                'data' => [
                    'balance' => 0,
                    'statement' => [],
                    'additional_info' => [],
                ],
            ];

        }

    }

    public static function processApiCallingCardActivation($data): array
    {
        $url = config('api.base_url') . config('api.active_wallet_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => "for lost and reback customer",
            "refId" => $mobileNo . randomDigits(),
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);

            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => 'Your account activation request was successful.',
                    'prompt' => null,
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your account activation request has failed.',
            'prompt' => null,
        ];
    }

    public static function processApiCallingDebitCardActivation($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your debit card activation request was successful.',
            'prompt' => getPromptPath('debit-card-activation-successful'),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Debit card activation failed.',
            'prompt' => getPromptPath('debit-card-activation-failed'),
        ];

        // will be removed later
    }

    public static function processApiCallingCreditCardActivation($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your credit card activation request was successful.',
            'prompt' => getPromptPath('credit-card-activation-successful'),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Your credit card activation failed.',
            'prompt' => getPromptPath('credit-card-activation-failed'),
        ];
        // will be removed later

    }

    public static function processApiCallingPrepaidCardActivation($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your prepaid card activation request was successful.',
            'prompt' => getPromptPath('prepaid-card-activation-successful'),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Prepaid card activation failed.',
            'prompt' => getPromptPath('prepaid-card-activation-failed'),
        ];
        // will be removed later

    }

    public static function processApiCallingChequeBookLeaf($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your cheque book leaf request was successful.',
            'prompt' => getPromptPath('cheque-book-leaf-request-successful'),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'Cheque book leaf request failed.',
            'prompt' => getPromptPath('cheque-book-leaf-request-failed'),
        ];
        // will be removed later

    }

    public static function processApiCallingCASAActivateSMSBanking($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your SMS banking activation request was successful.',
            'prompt' => getPromptPath('sms-banking-activate-request-successful'),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'SMS banking activation request failed.',
            'prompt' => getPromptPath('sms-banking-activate-request-failed'),
        ];
        // will be removed later

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
            'prompt' => getPromptPath($successPrompt),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
        // will be removed later

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
            'prompt' => getPromptPath($successPrompt),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
        // will be removed later

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
            'prompt' => getPromptPath($successPrompt),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
        // will be removed later

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
            'prompt' => getPromptPath($successPrompt),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
        // will be removed later

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
            'prompt' => getPromptPath($successPrompt),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
        // will be removed later

    }

    public static function processApiCallingEWChangeOrResetEWalletPIN($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedText = __('messages.common-request-failed-text');

        $reason = $data['reason'];
        $mobileNo = $data['mobile_no'];

        if (!createUserTicketHistory($data['purpose'], $mobileNo)) { // Ticket Creation Failed
            // $message = __('messages.service-not-available');
            ['message' => $message] = getExecutionTime('EWALLET');
            return [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => $message,
                'prompt' => null,
            ];
        }

        $url = config('api.base_url') . config('api.get_pin_reset_url');
        $apiHandler = new APIHandler();
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $reason,
            "refId" => $mobileNo . randomDigits(),
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);

            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt),
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
    }

    public static function processTicketCreation($data)
    {
        if (!createUserTicketHistory($data['purpose'], $data['mobile_no'])) {
            // $message = __('messages.service-not-available');
            ['message' => $message] = getExecutionTime('EWALLET');
            return [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => $message,
                'prompt' => null,
            ];
        }
        return true;

    }

    public static function processApiCallingEWApproveOrReject($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedText = __('messages.common-request-failed-text');

        $reason = $data['reason'];
        $mobileNo = $data['mobile_no'];

        if (!createUserTicketHistory($data['purpose'], $mobileNo)) { // Ticket Creation Failed
            // $message = __('messages.service-not-available');
            ['message' => $message] = getExecutionTime('EWALLET');
            return [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => $message,
                'prompt' => null,
            ];
        }

        $url = config('api.base_url') . config('api.approve_wallet_request_url');
        $apiHandler = new APIHandler();
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $reason,
            "refId" => $mobileNo . randomDigits(),
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);
            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt),
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
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
            // $userActualACNo = $response['data']['accountNo'];

            $userInputAccountNo = $account;
            $accountList = $response['data']['accountList'] ?? [];
            $isAccountMatched = false;

            foreach ($accountList as $accountInfo) {
                if ($accountInfo['accountNo'] == $userInputAccountNo) {
                    // Match found
                    $isAccountMatched = true;
                    break;
                }
            }

            if ($isAccountMatched && self::compareDateOfBirths($dob, $userActualDOB)) {

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
                'prompt' => getPromptPath($failedPrompt),
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
                throw new Exception("Invalid data type for user actual DOB.");
            }

            return $userInputedDOBDateTime->format('Y-m-d') == $userActualDobDateTime->format('Y-m-d');
        } catch (Exception $e) {
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
            'prompt' => getPromptPath($failedPrompt),
        ];

    }

    public static function processApiCallingAccLoanDPSInstalmentDetails($data)
    {
        // $phoneNumber = data_get(Session::get('logInfo'), 'otp_info.otp_phone') ?? 'NA';
        $getSelectedAcc = data_get(Session::get('logInfo'), 'selected_accEnc');
        $getDecryptedAccount = openSSLEncryptDecrypt($getSelectedAcc, 'decrypt');

        $response = self::fetchAccountFullDetails($getDecryptedAccount);

        if ($response['code'] === Response::HTTP_OK && $response['status'] === 'success') {

            $installmentSizeField = !empty($response['data']['additional_info']) ? $response['data']['additional_info']['installment_size_field'] : 0;
            $balance = number_format(intval($installmentSizeField), 2);
            $responseOut['code'] = $response['code'];
            $responseOut['status'] = 'success';
            $responseOut['message'] = __('messages.acc-loan-dps-inst-details-balance-message') . " " . $balance;
            $responseOut['balance'] = $balance;

            return $responseOut;
        }

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => __('messages.apologies-something-went-wrong'),
            'prompt' => null,
        ];

    }

    public static function processApiCallingEWDeviceBind($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        $reason = $data['reason'];
        $mobileNo = $data['mobile_no'];

        if (!createUserTicketHistory($data['purpose'], $mobileNo)) { // Ticket Creation Failed
            // $message = __('messages.service-not-available');
            ['message' => $message] = getExecutionTime('EWALLET');
            return [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => $message,
                'prompt' => null,
            ];
        }

        $url = config('api.base_url') . config('api.device_bind_url');
        $apiHandler = new APIHandler();
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $reason,
            "refId" => $mobileNo . randomDigits(),
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);

            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt),
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
    }

    public static function processApiCallingEWCloseWallet($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        $reason = $data['reason'];
        $mobileNo = $data['mobile_no'];

        if (!createUserTicketHistory($data['purpose'], $mobileNo)) { // Ticket Creation Failed
            // $message = __('messages.service-not-available');
            ['message' => $message] = getExecutionTime('EWALLET');
            return [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => $message,
                'prompt' => null,
            ];
        }

        $url = config('api.base_url') . config('api.close_wallet_url');
        $apiHandler = new APIHandler();
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $reason,
            "OtpCode" => "",
            "refId" => $mobileNo . randomDigits(),
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);

            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt),
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
    }

    public static function processApiCallingEWLockBlock($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        $reason = $data['reason'];
        $mobileNo = $data['mobile_no'];

        if (!createUserTicketHistory($data['purpose'], $mobileNo)) { // Ticket Creation Failed
            // $message = __('messages.service-not-available');
            ['message' => $message] = getExecutionTime('EWALLET');
            return [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => $message,
                'prompt' => null,
            ];
        }

        $url = config('api.base_url') . config('api.lock_wallet_url');
        $apiHandler = new APIHandler();
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "reason" => $reason,
            "OtpCode" => "",
            "refId" => $mobileNo . randomDigits(),
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);

            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt),
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
    }

    public static function processApiCallingEWUnlockActive($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

        $reason = $data['reason'];
        $mobileNo = $data['mobile_no'];

        if (!createUserTicketHistory($data['purpose'], $mobileNo)) { // Ticket Creation Failed
            // $message = __('messages.service-not-available');
            ['message' => $message] = getExecutionTime('EWALLET');
            return [
                'code' => Response::HTTP_EXPECTATION_FAILED,
                'status' => 'error',
                'message' => $message,
                'prompt' => null,
            ];
        }

        $url = config('api.base_url') . config('api.active_wallet_url');
        $apiHandler = new APIHandler();
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $reason,
            "refId" => $mobileNo . randomDigits(),
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);

            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => $successText,
                    'prompt' => getPromptPath($successPrompt),
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
    }

    public static function processApiCallingCASAAvailableBalance($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your request was successful.',
            'prompt' => getPromptPath('voice-for-casa-available-balance-request-successful-en'),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'eWallet Unlock/Active request failed.',
            'prompt' => getPromptPath('voice-for-casa-available-balance-request-failed-en'),
        ];
        // will be removed later
    }

    public static function processApiCallingCASAMiniStatement($data)
    {
        $getSelectedAcc = data_get(Session::get('logInfo'), 'selected_accEnc');
        $getDecryptedAccount = openSSLEncryptDecrypt($getSelectedAcc, 'decrypt');

        $response = self::fetchAccountFullDetails($getDecryptedAccount);
        if ($response['code'] === Response::HTTP_OK && $response['status'] === 'success') {

            $statementArr = [];
            foreach ($response['data']['statement'] ?? [] as $key => $item) {
                $statementArr[$key]['tran_serial'] = $item['tRAN_SERIALField'];
                $statementArr[$key]['tran_origin_branch'] = $item['tRAN_ORIGIN_BRANCHField'];
                $statementArr[$key]['tran_date'] = date('F j, Y', strtotime($item['tRAN_DATEField']));
                $statementArr[$key]['tran_type'] = $item['tRAN_TYPEField'];
                $statementArr[$key]['tran_amount'] = $item['tRAN_CURRENCYField'] . " " . number_format(intval($item['tRAN_AMOUNTField']), 2);
                // $statementArr[$key]['tran_currency'] = $item['tRAN_CURRENCYField'];
                $statementArr[$key]['tran_narration'] = $item['tRAN_NARRATIONField'];
            }

            $responseOut['code'] = $response['code'];
            $responseOut['status'] = 'success';
            $responseOut['message'] = "Data Found.";
            $responseOut['data'] = ['statement' => $statementArr];

            return $responseOut;
        }

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => __('messages.apologies-something-went-wrong'),
            'prompt' => null,
            'statement' => [],
        ];

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
            'prompt' => getPromptPath($successPrompt),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
        // will be removed later

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
            'prompt' => getPromptPath($successPrompt),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
        // will be removed later

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
            'prompt' => getPromptPath($successPrompt),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => $failedText,
            'prompt' => getPromptPath($failedPrompt),
        ];
        // will be removed later

    }

    public static function processApiCallingIBARChequeBookLeafStopPaymentClick($data)
    {
        // will be removed later
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Your cheque book leaf stop payment request was successful.',
            'prompt' => getPromptPath('ew-cheque-book-stop-payment-request-successful'),
        ];

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'error',
            'message' => 'eWallet Unlock/Active request failed.',
            'prompt' => getPromptPath('ew-cheque-book-stop-payment-request-failed'),
        ];
        // will be removed later

    }

    public static function processGetCallTypesDropDownValues($data)
    {
        $callType = self::getDropDownForCallTypeApi();
        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Data Found.',
            'prompt' => null,
            'data' => $callType,
        ];

    }

    public static function processGetCallCategoryDropDownValues($data)
    {
        $callCategory = self::getDropDownForCallCategoryApi($data);

        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Data Found.',
            'prompt' => null,
            'data' => $callCategory,
        ];

    }

    public static function processToCreateTicketInCRM($data)
    {
        $localeSuffix = (app()->getLocale() === 'en') ? '-en' : '-bn';
        $successPrompt = "common/request-successful{$localeSuffix}";
        $successText = __('messages.common-request-successful-text');
        $failedPrompt = "common/request-failed{$localeSuffix}";
        $failedText = __('messages.common-request-failed-text');

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
                    ],
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
                ],
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
                    ],
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
            ],
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
            ],
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
            "call_type_id" => $selectedValues,
        ], [
            'Authorization' => 'Bearer ' . config('api.crm_ticket_authorization_token'),
        ]);

        $options = [];
        if ($responseData['status'] === 'success' && $responseData['statusCode'] === 200) {
            $response = json_decode($responseData['data'], true);

            if ($response['success']) {
                $callCategories = $response['data'] ?? [];
                foreach ($callCategories as $category) {
                    $options[$category['id']] = $category['name'];
                }
            }

            return $options;
        }
        return $options;

    }

    public static function getDropDownForCallTypeApi()
    {
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

    public static function processGetSubCategoryDropDownValues($data)
    {
        $callSubCategory = self::getSubCategoryDropDownValues($data);

        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Data Found.',
            'prompt' => null,
            'data' => $callSubCategory,
        ];

    }

    public static function getSubCategoryDropDownValues($data)
    {

        $callTypeValue = $data['selectedValues']['callType'];
        $callCategoryValue = $data['selectedValues']['callCategory'];

        $url = config('api.crm_ticket_base_url') .
        config('api.crm_ticket_call_sub_category_url');

        $apiHandler = new APIHandler();
        $responseData = $apiHandler->doGetCall($url, [
            "call_type_id" => $callTypeValue,
            "call_category_id" => $callCategoryValue,
        ], [
            'Authorization' => 'Bearer ' . config('api.crm_ticket_authorization_token'),
        ]);

        $options = [];
        if ($responseData['status'] === 'success' && $responseData['statusCode'] === 200) {
            $response = json_decode($responseData['data'], true);

            if ($response['success']) {
                $callCategories = $response['data'] ?? [];
                foreach ($callCategories as $category) {
                    $options[$category['id']] = $category['name'];
                }
            }

            return $options;
        }
        return $options;

    }

    public static function getSubSubCategoryDropDownValues($data)
    {

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
                    $options[$category['id']] = $category['name'];
                }
            }

            return $options;
        }
        return $options;
    }

    public static function processGetSubSubCategoryDropDownValues($data)
    {
        $callSubSubCategory = self::getSubSubCategoryDropDownValues($data);

        return [
            'code' => Response::HTTP_OK,
            'status' => 'success',
            'message' => 'Data Found.',
            'prompt' => null,
            'data' => $callSubSubCategory,
        ];

    }

    public static function processApiCallingResetPin($data): array
    {

        $url = config('api.base_url') . config('api.get_pin_reset_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $data['reason'] ?? "Get new phone",
            "refId" => randomDigits(5),
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);

            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => 'PIN reset request successful.',
                    'prompt' => getPromptPath('pin-reset-success'),
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'PIN reset request failed.',
            'prompt' => getPromptPath('pin-reset-failed'),
        ];
    }

    public static function processApiCallingDeviceBind($data): array
    {

        $url = config('api.base_url') . config('api.device_bind_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "requestDetails" => $data['reason'] ?? "Get new phone",
            "refId" => randomDigits(5),
        ]);

        if ($response['status'] === 'success' && $response['statusCode'] === 200) {
            $data = json_decode($response['data'], true);

            if (intval($data['status']) === Response::HTTP_OK && $data['statsDetails'] === 'success') {

                return [
                    'code' => $response['statusCode'],
                    'status' => 'success',
                    'message' => 'Your device binding request was successful.',
                    'prompt' => getPromptPath('device-bind-success'),
                ];
            }
        }

        return [
            'code' => $response['statusCode'],
            'status' => 'error',
            'message' => 'Your device binding request failed.',
            'prompt' => getPromptPath('device-bind-failed'),
        ];
    }

    public static function processApiCallingCreateIssue($data): array
    {
        $callTypeId = $data['callTypeOpts'];
        $callCategoryIdInfo = $data['callCategoryOpts'];
        $callSubCategoryInfo = $data['callSubCategoryOpts'];
        $callSubSubCategoryInfo = $data['callSubSubCategoryOpts'];
        $remarks = $data['reason'];
        $email = $data['email'] ?? null;

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
            'email' => $email,
            'account_no' => null, // or you can set a default value if needed
        ];

        Log::info('CREATE-TICKET-CRM-REQUEST|' . json_encode($appParamsData));

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
            ],
        ];
    }

    public static function processApiCallingLockWallet($data): array
    {

        $url = config('api.base_url') . config('api.lock_wallet_url');
        $apiHandler = new APIHandler();
        $mobileNo = $data['mobile_no'];
        $response = $apiHandler->postCall($url, [
            "mobileNo" => $mobileNo,
            "userId" => "Agx01254",
            "reason" => $data['reason'] ?? "Having a problem with my phone.",
            // "OtpCode" => "",
            "refId" => randomDigits(5),
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
        // $phoneNumber = Session::get('logInfo')['otp_info']['otp_phone'] ?? null;
        $phoneNumber = data_get(Session::get('logInfo'), 'otp_info.otp_phone');

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
            'prompt' => $apiResponse['prompt'] ?? null,
        ];

        if (!empty($apiResponse['data'])) {
            $responseOut['data'] = $apiResponse['data'];
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
            case 'ACC-LOAN-DPS-INST-DETAILS':
                return self::processApiCallingAccLoanDPSInstalmentDetails($data);
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

    /* public function generateGPIN(Request $request)
    {
        try {

            // $getSelectedAcc = data_get(Session::get('logInfo'), 'selected_accEnc');
            // $getDecryptedAccount = openSSLEncryptDecrypt($getSelectedAcc, 'decrypt');

            $cardNumber = "4689800031879243"; // $getDecryptedAccount;
            $baseUrl = config('api.base_url');
            $baseUrl = "https://sblapi2022.sblesheba.com:8877/";

            $response = Http::withHeaders([
                'x-api-key' => 'Basic Y2FsbGNlbjpkYkJhZFNibCRlcno=',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
                ->asForm()
                ->post($baseUrl . 'api/callcenter/oauth/token',
                    [
                        'grant_type' => 'password',
                        'scope' => 'read',
                        'client_id' => 'restapp',
                        'client_secret' => 'restapp',
                        'username' => 'gateAdmin',
                        'password' => 'PayWay123@',
                    ]);

            if ($response->failed()) {
                Log::error("Failed to obtain access token" . json_encode($response));
                return response()->json(['status' => 'failed', 'error' => 'Failed to obtain access token'], 400);
            }

            $accessToken = $response->json()['access_token'];

            $refId = str_pad(mt_rand(1000000000000000, 9999999999999999), 16, '0', STR_PAD_LEFT);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'x-api-key' => 'Basic Y2FsbGNlbjpkYkJhZFNibCRlcno=',
                'Content-Type' => 'application/json',
            ])
                ->post($baseUrl . 'api/callcenter/v1/ws/callWS', [
                    'header' => [
                        'serviceDetail' => [
                            'corrID' => Str::uuid()->toString(),
                            'domainName' => 'Domain_Paygate',
                            'serviceName' => 'PAYCA.PINCLIENTDELIVERY',
                        ],
                        'signonDetail' => [
                            'clientID' => 'SONALI',
                            'orgID' => '000200',
                            'userID' => 'gateAdmin',
                            'externalUser' => 'user1',
                        ],
                        'messageContext' => [
                            'clientDate' => now()->format('YmdHis'),
                            'bodyType' => 'Clear',
                        ],
                    ],
                    'body' => [
                        'refId' => $refId,
                        'cardNumber' => $cardNumber,
                        'operationReasonCode' => '',
                    ],
                ]);

            if ($response->failed()) {
                Log::error("Failed to retrieve PIN" . json_encode($response));
                return response()->json(['status' => 'failed', 'error' => 'Failed to retrieve PIN'], 400);
            }

            Log::info('Response::' . json_encode($response->json()));

            $pin = $response->json()['responseBody']['PIN'] ?? "N/A";

            return response()->json([
                'refId' => $refId,
                'pin' => $pin,
                'status' => 'success',
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to retrieve PIN. Exception: " . $e->getMessage() . " | Trace: " . json_encode($e->getTrace()));

            return response()->json(['status' => 'failed', 'error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    } */

    public function generateGPIN(Request $request, APIHandler $apiHandler)
    {
        try {
            $cardNumber = "4689800031879243"; // Placeholder, will replace this with actual value.
            // Will open this later
            // $baseUrl = config('api.base_url', 'https://sblapi2022.sblesheba.com:8877/');
            $baseUrl = "https://sblapi2022.sblesheba.com:8877/";
            
            // Obtain Access Token
            $tokenResponse = $apiHandler->doPostCall($baseUrl . 'api/callcenter/oauth/token', [
                'grant_type' => 'password',
                'scope' => 'read',
                'client_id' => 'restapp',
                'client_secret' => 'restapp',
                'username' => 'gateAdmin',
                'password' => 'PayWay123@',
            ], [
                'x-api-key' => 'Basic Y2FsbGNlbjpkYkJhZFNibCRlcno=',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ], true);

            
            if ($tokenResponse['status'] !== 'success' || empty($tokenResponse['data'])) {
                Log::error("Failed to obtain access token", $tokenResponse);
                return response()->json(['status' => 'failed', 'error' => 'Failed to obtain access token'], 400);
            }

            $accessToken = json_decode($tokenResponse['data'], true)['access_token'] ?? null;

            if (!$accessToken) {
                Log::error("Access token missing in the response", $tokenResponse);
                return response()->json(['status' => 'failed', 'error' => 'Access token missing'], 400);
            }

            // Generate Reference ID
            $refId = str_pad(mt_rand(1000000000000000, 9999999999999999), 16, '0', STR_PAD_LEFT);

            // Call GPIN Service
            $gpinResponse = $apiHandler->doPostCall($baseUrl . 'api/callcenter/v1/ws/callWS', [
                'header' => [
                    'serviceDetail' => [
                        'corrID' => Str::uuid()->toString(),
                        'domainName' => 'Domain_Paygate',
                        'serviceName' => 'PAYCA.PINCLIENTDELIVERY',
                    ],
                    'signonDetail' => [
                        'clientID' => 'SONALI',
                        'orgID' => '000200',
                        'userID' => 'gateAdmin',
                        'externalUser' => 'user1',
                    ],
                    'messageContext' => [
                        'clientDate' => now()->format('YmdHis'),
                        'bodyType' => 'Clear',
                    ],
                ],
                'body' => [
                    'refId' => $refId,
                    'cardNumber' => $cardNumber,
                    'operationReasonCode' => '',
                ],
            ], [
                'Authorization' => 'Bearer ' . $accessToken,
                'x-api-key' => 'Basic Y2FsbGNlbjpkYkJhZFNibCRlcno=',
                'Content-Type' => 'application/json',
            ]);

            if ($gpinResponse['status'] !== 'success' || empty($gpinResponse['data'])) {
                Log::error("Failed to retrieve PIN", $gpinResponse);
                return response()->json(['status' => 'failed', 'error' => 'Failed to retrieve PIN'], 400);
            }

            $gpinData = json_decode($gpinResponse['data'], true);
            $pin = $gpinData['responseBody']['PIN'] ?? "N/A";

            return response()->json([
                // 'refId' => $refId,
                'pin' => $pin,
                'status' => 'success',
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to retrieve PIN. Exception: " . $e->getMessage(), ['trace' => $e->getTrace()]);
            return response()->json(['status' => 'failed', 'error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
}
