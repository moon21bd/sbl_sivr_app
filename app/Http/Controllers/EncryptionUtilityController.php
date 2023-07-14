<?php

namespace App\Http\Controllers;

use App\Handlers\APIHandler;
use App\Handlers\EncryptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class EncryptionUtilityController extends ResponseController
{

    public function encrypt(Request $request)
    {
        $secretKey = '@@#$UEOE#$#$"{}][|!@#@@';
        $originalString = json_encode(['name' => 'Moon', 'email' => 'rhmoon21@gmail.com']);

        $Encryption = new EncryptionHandler();
        $keyFromPw = $Encryption->getKeyHashed($secretKey);
        $iv = $Encryption->getIV();
        $encryptedVal = $Encryption->encrypt($originalString, $keyFromPw, $iv);
        $decryptedVal = $Encryption->decrypt($encryptedVal, $keyFromPw);

        // Convert the data to UTF-8
        $keyFromPw = mb_convert_encoding($keyFromPw, 'UTF-8');
        $iv = mb_convert_encoding($iv, 'UTF-8');
        $encryptedVal = mb_convert_encoding($encryptedVal, 'UTF-8');
        $decryptedVal = mb_convert_encoding($decryptedVal, 'UTF-8');

        // Return the encrypted response and IV as a raw JSON string
        return response()->json([
            'keyFromPw' => $keyFromPw,
            'iv' => $iv,
            'encryptedVal' => $encryptedVal,
            'decryptedVal' => $decryptedVal,
        ], 200, [], JSON_UNESCAPED_UNICODE);

    }


}
