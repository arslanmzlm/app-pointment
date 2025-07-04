<?php

namespace App\Services;

use App\Enums\ApiProvider;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Http;

class SmsService
{
    private static $client = null;

    public static function send(string $phone, string $message)
    {
        if (!app()->isProduction()) {
            return 0;
        }

        $client = self::getClient();

        $requestData = [
            'msgheader' => config('sms.header'),
            'encoding' => 'TR',
            'messages' => [
                [
                    'no' => $phone,
                    'msg' => $message,
                ]
            ],
        ];

        $url = self::getUrl('send');

        try {
            $response = $client->post($url, $requestData);

            self::log($url, $requestData, $response->object(), null);

            if ($response->successful() && $response->object()) {
                return $response->object()->jobid;
            }
        } catch (\Exception $e) {
            self::log($url, $requestData, isset($response) ? $response->object() : null, $e->getMessage());
        }

        return null;
    }

    public static function sendMany(array $messages)
    {
        if (!app()->isProduction()) {
            return 0;
        }

        $client = self::getClient();

        $requestData = [
            'msgheader' => config('sms.header'),
            'encoding' => 'TR',
            'messages' => [],
        ];

        foreach ($messages as $message) {
            $requestData['messages'][] = [
                'no' => $message['phone'],
                'msg' => $message['message'],
            ];
        }

        $url = self::getUrl('send');

        try {
            $response = $client->post($url, $requestData);

            self::log($url, $requestData, $response->object(), null);

            if ($response->successful() && $response->object()) {
                return $response->object()->jobid;
            }
        } catch (\Exception $e) {
            self::log($url, $requestData, isset($response) ? $response->getBody() : null, $e->getMessage());
        }

        return null;
    }

    private static function getClient()
    {
        if (self::$client === null) {
            self::$client = Http::withBasicAuth(config('sms.user'), config('sms.password'));
        }

        return self::$client;
    }

    private static function getUrl(string $endpoint): string
    {
        return trim(config('sms.url'), '/') . "/{$endpoint}";
    }

    private static function log($url, $request, $response, $error)
    {
        $apiLog = new ApiLog();
        $apiLog->provider = ApiProvider::NET_GSM;
        $apiLog->url = $url;
        $apiLog->request = $request;
        $apiLog->response = $response;
        $apiLog->exception = $error;
        $apiLog->save();
    }
}
