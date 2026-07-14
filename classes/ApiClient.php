<?php
/**
 * Client minim pentru testarea conexiunii API fara generare de continut.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class IiromanestiAiSeoApiClient
{
    private $provider;
    private $apiKey;
    private $model;
    private $timeout;

    public function __construct($provider, $apiKey, $model, $timeout)
    {
        $this->provider = $provider;
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->timeout = (int) $timeout;
    }

    public function testConnection()
    {
        if ($this->provider !== 'openai') {
            throw new Exception('Furnizor AI neacceptat.');
        }
        if ($this->apiKey === '') {
            throw new Exception('Cheia API lipseste.');
        }

        $url = 'https://api.openai.com/v1/models/' . rawurlencode($this->model);
        $headers = array(
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
        );

        if (function_exists('curl_init')) {
            return $this->testWithCurl($url, $headers);
        }

        return $this->testWithTools($url, $headers);
    }

    private function testWithCurl($url, array $headers)
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_HEADER, false);
        curl_setopt($handle, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($handle);
        $statusCode = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        if ($response === false || $statusCode < 200 || $statusCode >= 300) {
            throw new Exception($error !== '' ? $error : 'Status HTTP invalid: ' . $statusCode);
        }

        return true;
    }

    private function testWithTools($url, array $headers)
    {
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'timeout' => $this->timeout,
                'ignore_errors' => true,
            ),
        );
        $context = stream_context_create($options);
        $response = Tools::file_get_contents($url, false, $context);

        if ($response === false) {
            throw new Exception('Nu s-a putut deschide conexiunea HTTP.');
        }

        $statusCode = 0;
        if (isset($http_response_header[0]) && preg_match('/HTTP\/\S+\s+(\d+)/', $http_response_header[0], $matches)) {
            $statusCode = (int) $matches[1];
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new Exception('Status HTTP invalid: ' . $statusCode);
        }

        return true;
    }
}
