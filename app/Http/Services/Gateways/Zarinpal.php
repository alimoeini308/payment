<?php

namespace App\Http\Services\Gateways;

use App\Http\Services\Gateways\Contracts\Gateway;
use App\Http\Services\Gateways\Contracts\PaymentGatewayResult;
use App\Http\Services\Gateways\Contracts\VerifyGatewayResult;
use App\Models\Transaction;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

class Zarinpal implements Gateway
{
    protected Client $client;
    protected string $url;
    protected string $merchantId;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('APP_ENV')=='production'?'payment':'sandbox';
        $this->merchantId = env('ZARINPAL_MERCHANT_ID',Str::uuid()->toString());
    }

    public function payment($amount): PaymentGatewayResult
    {
        $response = $this->client->post("https://".$this->url.".zarinpal.com/pg/v4/payment/request.json", [
            'json' => [
                'merchant_id' => $this->merchantId,
                'amount' => $amount,
                'callback_url' => route('transaction.verify.v1',['gateway' => 'zarinpal']),
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
        $paymentUrl="https://".$this->url.".zarinpal.com/pg/StartPay/$paymentToken" ;

        return new PaymentGatewayResult($paymentToken,$paymentUrl);
    }

    public function verify(Transaction $transaction): VerifyGatewayResult
    {
        $url = "https://".$this->url.".zarinpal.com/pg/v4/payment/verify.json";
        $response = $this->client->post($url, [
            'json' => [
                'merchant_id' => $this->merchantId,
                'amount' => $transaction->getAttributeValue('amount'),
                'authority' => $transaction->getAttributeValue("token"),
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);

        $body = $response->getBody();
        $response = json_decode($body, true);
        $responseData = $response["data"];
        if ($responseData['code'] == 100){
            return new VerifyGatewayResult($responseData['ref_id']);
        }else{
            return new VerifyGatewayResult(null, $response['errors']);
        }
    }

    function reverse(Transaction $transaction): bool
    {
        $url = "https://".$this->url.".zarinpal.com/pg/v4/payment/reverse.json";

        $response = $this->client->post($url, [
            'json' => [
                'merchant_id' => $this->merchantId,
                'authority'   => $transaction->getAttributeValue('token'),
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
        $responseBody = json_decode($response->getBody(),true)['data'];
        return $responseBody["code"] == 100;
    }

}
