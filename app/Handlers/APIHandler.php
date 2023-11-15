<?php

namespace app\Handlers;

use App\Models\ApiLog;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class APIHandler
{
    /*
     * Here $isSSLVerify value is by default false. we got some issues in sonali bank api "cURL error 60: SSL certificate problem: certificate has expired" that's why passing false to bypassing the ssl certificate false. after production implementation this will change to true
     * */
    public function postCall($url, $params = [], $isSSLVerify = false): array
    {
        $options = [
            'verify' => $isSSLVerify,
            'headers' => [
                'Accept' => '*/*',
                'Content-Type' => 'application/json',
            ],
            'json' => $params,
        ];

        $this->addBasicAuthHeader($options);
        $client = new Client();

        $responseData = []; // Initialize an empty array

        try {
            $startTime = microtime(true);
            $response = $client->post($url, $options);
            $endTime = microtime(true);

            $responseData = [
                'status' => 'success',
                'statusCode' => $response->getStatusCode(),
                'data' => $response->getBody()->getContents(),
                'exceptionType' => 'NONE',
            ];

            // Validate the status code received in the API response
            if (!is_numeric($responseData['statusCode']) || $responseData['statusCode'] === 0) {
                $responseData['statusCode'] = Response::HTTP_EXPECTATION_FAILED;
            }

        } catch (Exception $e) {
            $responseData = $this->handleException($url, $e);
        } catch (ConnectException $e) {
            $responseData = $this->handleException($url, $e);
        } catch (GuzzleException $e) {
            $responseData = $this->handleException($url, $e);
        }

        $responseTime = microtime(true) - $startTime;

        $this->storeApiLog(getIPAddress(), $url, $options, $responseData, $responseTime, $this->getServerInfo());

        return $responseData;
    }

    public function doPostCall($url, $params = [], $headers = [], $isSSLVerify = false): array
    {
        $options = [
            'verify' => $isSSLVerify,
            'headers' => array_merge([
                'Accept' => '*/*',
                'Content-Type' => 'application/json',
            ], $headers),
            'json' => $params,
        ];

        $client = new Client();
        $responseData = []; // Initialize an empty array

        try {
            $startTime = microtime(true);
            $response = $client->post($url, $options);
            $endTime = microtime(true);

            $responseData = [
                'status' => 'success',
                'statusCode' => $response->getStatusCode(),
                'data' => $response->getBody()->getContents(),
                'exceptionType' => 'NONE',
            ];

            // Validate the status code received in the API response
            if (!is_numeric($responseData['statusCode']) || $responseData['statusCode'] === 0) {
                $responseData['statusCode'] = Response::HTTP_EXPECTATION_FAILED;
            }

        } catch (Exception $e) {
            $responseData = $this->handleException($url, $e);
        } catch (ConnectException $e) {
            $responseData = $this->handleException($url, $e);
        } catch (GuzzleException $e) {
            $responseData = $this->handleException($url, $e);
        }

        $responseTime = microtime(true) - $startTime;

        $this->storeApiLog(getIPAddress(), $url, $options, $responseData, $responseTime, $this->getServerInfo());

        return $responseData;
    }

    public function doGetCall($url, $params = [], $headers = [], $isSSLVerify = false): array
    {
        $options = [
            'verify' => $isSSLVerify,
            'headers' => array_merge([
                'Accept' => '*/*',
                'Content-Type' => 'application/json',
            ], $headers),
            'query' => $params,
        ];

        $client = new Client();
        $responseData = []; // Initialize an empty array

        try {
            $startTime = microtime(true);
            $response = $client->get($url, $options);

            $responseData = [
                'status' => 'success',
                'statusCode' => $response->getStatusCode(),
                'data' => $response->getBody()->getContents(),
                'exceptionType' => 'NONE',
            ];

            // Validate the status code received in the API response
            if (!is_numeric($responseData['statusCode']) || $responseData['statusCode'] === 0) {
                $responseData['statusCode'] = Response::HTTP_EXPECTATION_FAILED;
            }
        } catch (Exception $e) {
            $responseData = $this->handleException($url, $e);
        } catch (ConnectException $e) {
            $responseData = $this->handleException($url, $e);
        } catch (GuzzleException $e) {
            $responseData = $this->handleException($url, $e);
        }

        $responseTime = microtime(true) - $startTime;

        $this->storeApiLog(getIPAddress(), $url, $options, $responseData, $responseTime, $this->getServerInfo());

        return $responseData;
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
        $statusCode = $e->getCode();
        if (!is_numeric($statusCode) || $statusCode === 0) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $exceptions = [
            'status' => 'error',
            'statusCode' => $statusCode,
            'data' => null,
            'api_url' => $url,
            'exceptionType' => get_class($e),
            'exceptionMessage' => $e->getMessage(),
        ];

        Log::error('EXCEPTION-HAPPEN-DURING-API-CALL:: ' . json_encode($exceptions));
        return $exceptions;
    }

    protected function storeApiLog($ip, $url, $request, $response, $responseTime, $serverInfo)
    {
        return ApiLog::create([
            'ip' => $ip,
            'url' => $url,
            'status_code' => $response['statusCode'] ?? Response::HTTP_EXPECTATION_FAILED,
            'request' => json_encode($request),
            'response' => $response['data'] ?? null,
            'exception_type' => $response['exceptionType'] ?? null,
            'server_info' => $serverInfo,
            'response_time' => $responseTime,
        ]);
    }

    protected function getServerInfo()
    {
        $serverInfo = [
            'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'],
            'SERVER_NAME' => $_SERVER['SERVER_NAME'],
            'SERVER_PROTOCOL' => $_SERVER['SERVER_PROTOCOL'],
            'SERVER_ADDR' => $_SERVER['SERVER_ADDR'] ?? $_SERVER['REMOTE_ADDR'],
            'SERVER_PORT' => $_SERVER['SERVER_PORT'] ?? 0,
            'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
        ];

        return json_encode($serverInfo);
    }

}
