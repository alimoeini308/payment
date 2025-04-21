<?php

namespace App\Http\Services\Gateways;

use App\Http\Services\Gateways\Contracts\Gateway;
use App\Http\Services\Gateways\Contracts\PaymentGatewayResult;
use App\Http\Services\Gateways\Contracts\VerifyGatewayResult;
use App\Models\Transaction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Zibal implements Gateway
{
    protected Client $client;
    protected string $merchantId;

    public function __construct()
    {
        $this->client = new Client();
        $this->merchantId = env('ZIBAL_MERCHANT_ID','zibal');
    }

    public function payment($amount): PaymentGatewayResult
    {
        $response = $this->client->post('https://gateway.zibal.ir/v1/request', [
            'json' => [
                'merchant' => $this->merchantId,
                'amount' => $amount,
                'callbackUrl' => route('transaction.verify.v1',['gateway' => 'zibal']),
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
        return new PaymentGatewayResult($token,"https://gateway.zibal.ir/start/".$token);
    }
    public function verify(Transaction $transaction): VerifyGatewayResult
    {
        $response = $this->client->post('https://gateway.zibal.ir/v1/verify', [
            'json' => [
                'merchant' => $this->merchantId,
                'trackId' => $transaction->getAttributeValue('token'),
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $body = $response->getBody()->getContents();
        $response = json_decode($body, true);
        if ($response['result'] == 100) {
            return new VerifyGatewayResult($response['refNumber']);
        }else{
            return new VerifyGatewayResult(null,$response['message']);
        }
    }

    public function reverse(Transaction $transaction): bool
    {
        //TODO:: zibal doesnt have refund yet
        return false;
    }
}
