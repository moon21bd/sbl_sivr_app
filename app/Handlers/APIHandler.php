<?php

namespace app\Handlers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class APIHandler
{
    public function postCall($url, $params = [], $isSSLVerify = true): array
    {
        $options = [
            'verify' => $isSSLVerify,
            // 'timeout' => 60, // Set the timeout value in seconds
            'headers' => [
                'Accept' => '*/*',
                'Content-Type' => 'application/json',
            ],
            'json' => $params,
        ];

        $this->addBasicAuthHeader($options);
        $client = new Client();

        try {
            $response = $client->post($url, $options);
            Log::info('API: ' . $url . ' options:' . json_encode($params) . 'response:' . json_encode($response));

            return [
                'status' => 'success',
                'statusCode' => $response->getStatusCode(),
                'data' => $response->getBody()->getContents(),
                'exceptionType' => 'NONE',
            ];
        } catch (Exception $e) {
            return $this->handleException($url, $e);
        } catch (ConnectException $e) {
            return $this->handleException($url, $e);
        } catch (GuzzleException $e) {
            return $this->handleException($url, $e);
        }
    }

    protected function addBasicAuthHeader(&$options)
    {
        $username = config('api.basic-auth')['username'];
        $password = config('api.basic-auth')['password'];

        $encodedCredentials = base64_encode($username . ':' . $password);
        $options['headers']['Authorization'] = 'Basic ' . $encodedCredentials;
    }

    protected function handleException($url, $e): array
    {
        $exceptions = [
            'status' => 'error',
            'statusCode' => $e->getCode(),
            'data' => null,
            'api_url' => $url,
            'exceptionType' => get_class($e),
            'exceptionMessage' => $e->getMessage(),
        ];

        Log::info('EXCEPTION-HAPPEN-DURING-API-CALL:: ' . json_encode($exceptions));
        return $exceptions;
    }

}
