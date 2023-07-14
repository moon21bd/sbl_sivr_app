<?php

namespace App\Http\Controllers;


use App\Http\Controllers\API\status;
use App\Http\Controllers\API\validation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Auth;
use App\User;

class ResponseController extends Controller
{
    /**
     * error response codes.
     *
     * @return string
     */
    protected $commonSuccessCode = 200;
    protected $commonErrorCode = 400;

    protected function sendResponse($content = [], $status = 200)
    {
        return response()->json($content, $status);
        // return response()->json($content)->setStatusCode($status);
    }

    public function sendError($error, $code = 404)
    {
        $response = [
            'error' => $error,
        ];
        return response()->json($response, $code);
    }

    /**
     * function name: makeResponse
     * return array or object response
     * Created by Raqibul Hasan
     *
     * @param status,code,message,data
     */
    protected function makeResponse(string $status, int $code, $message = '', $data = '')
    {
        $obj = [
            'code' => $code,
            'status' => $status,
            'message' => $message,
        ];

        if (!empty($data)) {
            $obj['data'] = $data;
        }

        return $obj;
    }

    protected function getIPAddress()
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    // trim for safety measures
                    $ip = trim($ip);
                    // attempt to validate IP
                    if ($this->validateIP($ip)) {
                        return $ip;
                    }
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? false;
    }

    protected function validateIP($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        return true;
    }

    /**
     * Return a validation rules
     * Created by Raqibul Hasan
     *
     * @return string error
     */
    protected function phoneValidationRules()
    {
        return 'required|numeric|digits:11|regex:/(01)[3456789]{1}\d{8}/';
    }

    /**
     * Return a validation error message
     * Created by Raqibul Hasan
     *
     * @return string[] error
     */
    protected function phoneValidationErrorMessages($mobileNo = 'mobile_no')
    {
        return [
            $mobileNo . '.required' => 'Phone number is required.',
            $mobileNo . '.numeric' => 'Please provide only numeric value.',
            $mobileNo . '.digits' => 'Phone number field must be 11 digits.',
            $mobileNo . '.regex' => 'Your phone number does not match with Bangladeshi operators.',
        ];
    }

    protected function randomDigits($len = 3): string
    {
        return str_pad(mt_rand(0, 999), $len, '0', STR_PAD_LEFT);
    }

    protected function decodeJsonIfValid($jsonString)
    {
        // Check if the string is a valid JSON
        if (is_string($jsonString) && !empty($jsonString) && $this->isJson($jsonString)) {
            // Decode the JSON string
            $decodedData = json_decode($jsonString, true);

            // Check if the JSON decoding was successful
            if ($decodedData !== null && json_last_error() === JSON_ERROR_NONE) {
                return $decodedData;
            } else {
                // Error occurred while decoding JSON
                // Handle the error if needed
                return null;
            }
        } else {
            // Not a valid JSON string
            // Return the original value
            return $jsonString;
        }
    }

    // Function to check if a string is a valid JSON
    protected function isJson($string)
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    protected function hidePhoneNumber($phoneNumber)
    {
        $length = strlen($phoneNumber);
        if ($length >= 10) {
            return substr($phoneNumber, 0, 3) . '******' . substr($phoneNumber, -2);
        } else {
            return $phoneNumber;
        }

    }

}
