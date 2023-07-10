<?php

namespace App\Repositories;

use Exception;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class APIHandler
{
    public function postCall($url, $params = [], $isSSLVerify = true, $associative = false)
    {
        $options = [];
        if ($isSSLVerify == false) {
            $options = ['verify' => false];
        }

        $client = new Client($options);
        try {
            $response = $client->post($url, [
                'headers' => [
                    'Accept' => '*/*',
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($params),
                // RequestOptions::JSON => $params,
            ]);
            return [
                'status' => 'success',
                'statusCode' => $response->getStatusCode(),
                'data' => tryJsonDecode($response->getBody()->getContents(), $associative),
                'exceptionType' => 'NONE',
            ];
        } catch (Exception $e) {
            $exceptions = [
                'status' => 'error',
                'statusCode' => $e->getCode(),
                'data' => null,
                'api' => $url,
                'exceptionType' => 'Exception',
                'exceptions' => tryJsonDecode($e->getResponse()->getBody()->getContents(), $associative),
                'exceptionMessage' => $e->getMessage(),
            ];
            Log::info('EXCEPTION-HAPPEN-DURING-API-CALL:: ' . json_encode($exceptions));
            return $exceptions;
        } catch (ConnectException $e) {
            $exceptions = [
                'status' => 'error',
                'statusCode' => $e->getCode(),
                'data' => null,
                'api_url' => $url,
                'exceptionType' => 'ConnectException',
                'exceptions' => tryJsonDecode($e->getResponse()->getBody()->getContents(), $associative),
                'exceptionMessage' => $e->getMessage(),
            ];
            Log::info('CONNECT-EXCEPTION-HAPPEN-DURING-API-CALL:: ' . json_encode($exceptions));
            return $exceptions;
        } catch (GuzzleException $e) {
            $exceptions = [
                'status' => 'error',
                'statusCode' => $e->getCode(),
                'data' => null,
                'api_url' => $url,
                'exceptionType' => 'GuzzleException',
                'exceptions' => tryJsonDecode($e->getResponse()->getBody()->getContents(), $associative),
                'exceptionMessage' => $e->getMessage(),
            ];
            Log::info('GUZZLE-EXCEPTION-HAPPEN-DURING-API-CALL:: ' . json_encode($exceptions));
            return $exceptions;
        }

    }

    public static function sendMail($data)
    {
        $mailBody = $data['mail_body'] ?? "";
        $mailSubject = $data['mail_subject'] ?? "";
        $mailFrom = $data['mail_from'] ?? env('MAIL_FROM_ADDRESS');
        $emailIds = $data['emails'] ?? "";

        try {
            Mail::send([], [], function ($message) use ($mailBody, $mailFrom, $emailIds, $mailSubject) {
                $message->to($emailIds)
                    ->from($mailFrom)
                    ->subject($mailSubject)
                    ->setBody($mailBody, 'text/html'); // for HTML rich messages
            });

            $status = true;
            $msg = "Mail send successfully";
        } catch (\Exception $e) {
            $status = false;
            $msg = "Mail not send! contact with administrator";
            Log::error("MAIL_SENDING_ERROR_EXCEPTION:: " . $e->getMessage());
        }

        return [
            'status' => $status,
            'msg' => $msg,
        ];
    }
}
