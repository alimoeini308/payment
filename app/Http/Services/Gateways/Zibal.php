<?php

namespace App\Http\Services\Gateways;

use App\Http\Services\Gateways\Contracts\Gateway;
use App\Http\Services\Gateways\Contracts\PaymentResult;
use GuzzleHttp\Client;

class Zibal implements Gateway
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function payment($amount)
    {
        $merchantId=env('ZIBAL_MERCHANT_ID','zibal');

        $response = $this->client->post('https://gateway.zibal.ir/v1/request', [
            'json' => [
                'merchant' => $merchantId,
                'amount' => $amount,
                'callbackUrl' => route('payment.verify.v1',['gateway' => 'zibal']),
                'description' => 'Transaction description.',
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        $token = $data['trackId'];
        return new PaymentResult($token,"https://gateway.zibal.ir/start/".$token);
    }
}
