<?php

/**
 * Created by PhpStorm.
 * User: Raqibul Hasan Moon
 * Date: 14/07/2023
 * Time: 10:35 PM
 */

if (!function_exists('getPromptPath')) {
    function getPromptPath($name, $format = ".m4a"): string
    {
        return asset('uploads/prompts/' . $name . $format);
    }
}

if (!function_exists('getIPAddress')) {
    function getIPAddress()
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key])) {
                $ipAddresses = explode(',', $_SERVER[$key]);

                foreach ($ipAddresses as $ip) {
                    $ip = trim($ip);

                    if (validateIP($ip)) {
                        return $ip;
                    }
                }
            }
        }

        return request()->ip(); // Fallback to Laravel's default method
    }
}

if (!function_exists('validateIP')) {
    function validateIP($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        return true;
    }
}

if (!function_exists('randomDigits')) {
    function randomDigits($len = 3): string
    {
        return str_pad(mt_rand(0, 999), $len, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('openSSLEncryptDecrypt')) {
    function openSSLEncryptDecrypt($string, $action = 'encrypt')
    {
        $encrypt_method = 'AES-256-CBC';
        $secret_key = "!@#$@n@1iv1vr($%^&*)";
        $secret_iv = '^%$0n@l1v1vr%^';

        if (!in_array($action, ['encrypt', 'decrypt'])) {
            throw new InvalidArgumentException('Invalid action parameter');
        }

        $iv = $action == 'encrypt' ? openssl_random_pseudo_bytes(16) : substr(hash('sha256', $secret_iv), 0, 16);

        $key = hash('sha256', $secret_key);

        $output = null;

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            if ($output === false) {
                throw new RuntimeException('Encryption failed');
            }
            $output = base64_encode($iv . $output);
        } elseif ($action == 'decrypt') {
            $string = base64_decode($string);
            $iv = substr($string, 0, 16);
            $string = substr($string, 16);
            $output = openssl_decrypt($string, $encrypt_method, $key, 0, $iv);
            if ($output === false) {
                throw new RuntimeException('Decryption failed');
            }
        }

        return $output;
    }
}

if (!function_exists('getUserInfoFromSession')) {
    function getUserInfoFromSession(): array
    {
        $logInfo = \Illuminate\Support\Facades\Session::get('logInfo');
        $userPhone = data_get($logInfo, 'otp_info.otp_phone');
        $userAccountNo = data_get($logInfo, 'account_info.accountNo');
        $name = data_get($logInfo, 'account_info.accountName', __('messages.guest-user'));
        $getUserImage = \App\Models\SblUserImage::where('user_phone', $userPhone)
            ->orderBy('created_at', 'desc')
            ->value('path');
        $userImage = $getUserImage ? asset($getUserImage) : asset('img/icon/user.svg');
        $userImageMMenu = $getUserImage ? asset($getUserImage) : asset('img/icon/default-user.svg');

        return compact('name', 'userPhone', 'userImage', 'userImageMMenu', 'userAccountNo');
    }

}


if (!function_exists('createUserTicketHistory')) {
    function createUserTicketHistory($purpose, $mobileNo, $ticketType = 'EWALLET')
    {
        ['seconds' => $seconds] = getExecutionTime($ticketType);

        // $secondAllowForTicket = getExecutionTimeInSeconds($ticketType);
        $secondAllowForTicket = $seconds;
        $userTicketHistory = \App\Models\UserTicketHistory::latest()
            ->where(function ($query) use ($mobileNo, $purpose) {
                $query->where('mobile_no', $mobileNo);
                $query->where('purpose', $purpose);
            })
            ->lockForUpdate()
            ->first();

        $isUserEligible = $userTicketHistory && $userTicketHistory->created_at->diffInSeconds(now()) <= $secondAllowForTicket;

        if ($isUserEligible) { // User is not eligible for creating a ticket
            \Illuminate\Support\Facades\Log::info("user is not eligible for creating a ticket for the next $secondAllowForTicket seconds");
            return false;
        }

        saveUserTicketHistory($purpose, 'done', $mobileNo);
        return true;
    }
}

if (!function_exists('saveUserTicketHistory')) {
    function saveUserTicketHistory($purpose, $status = 0, $mobileNo)
    {
        \App\Models\UserTicketHistory::create([
            'purpose' => $purpose,
            'status' => $status,
            'mobile_no' => $mobileNo,
        ]);
    }
}

if (!function_exists('getExecutionTime')) {
    function getExecutionTime($type)
    {
        $defaultHours = 12; // Default hours in case the type is not 'EWALLET', 'CARDS', or 'ACCOUNT_BALANCE'

        $hours = $defaultHours; // Default hours

        if (strtoupper($type) === 'EWALLET' || strtoupper($type) === 'CARDS') {
            $hours = intval(config('EWALLET_CARDS_NEXT_TICKET_EXECUTION_TIME'));
        } else if (strtoupper($type) === 'ACCOUNT_BALANCE') {
            $hours = intval(config('ACCOUNT_BALANCE_NEXT_TICKET_EXECUTION_TIME'));
        }

        $seconds = $hours * 60 * 60; // Calculate seconds

        // Get localized message based on the app locale
        $message = \Illuminate\Support\Facades\Lang::get('messages.service-not-available', ['hours' => $hours]);

        return [
            'seconds' => $seconds,
            'message' => $message,
        ];
    }
}

