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
