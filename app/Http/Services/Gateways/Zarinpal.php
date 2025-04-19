<?php

namespace App\Http\Services\Gateways;

use App\Http\Services\Gateways\Contracts\Gateway;
use App\Http\Services\Gateways\Contracts\PaymentResult;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class Zarinpal implements Gateway
{

    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function payment($amount)
    {
        $url=env('APP_ENV')=='production'?'payment':'sandbox';
        $merchantId=env('ZARINPAL_MERCHANT_ID',Str::uuid()->toString());
        $response = $this->client->post("https://$url.zarinpal.com/pg/v4/payment/request.json", [
            'json' => [
                'merchant_id' => $merchantId,
                'amount' => $amount,
                'callback_url' => route('payment.verify.v1',['gateway' => 'zarinpal']),
                'description' => 'Transaction description.',
                'metadata' => [
                    'gateway' => 'zarinpal',
                    'mobile' => '09121234567',
                    'email' => 'info.test@example.com',
                ]
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $body = $response->getBody();
        $data = json_decode($body, true);

        $paymentToken=$data['data']["authority"];
        $paymentUrl="https://$url.zarinpal.com/pg/StartPay/$paymentToken" ;

        return new PaymentResult($paymentToken,$paymentUrl);
    }
}
