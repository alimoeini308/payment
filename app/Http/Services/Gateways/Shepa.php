<?php

namespace App\Http\Services\Gateways;

use App\Http\Services\Gateways\Contracts\Gateway;
use App\Http\Services\Gateways\Contracts\PaymentResult;
use App\Http\Services\Gateways\Contracts\VerifyResult;
use App\Models\Transaction;
use Exception;
use GuzzleHttp\Client;

class Shepa implements Gateway
{

    protected Client $client;
    protected string $url;
    protected string $merchantId;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('APP_ENV')=='production'?'merchant':'sandbox';
        $this->merchantId = env('SHEPA_API_KEY','sandbox');
    }

    public function payment($amount): PaymentResult
    {
        $client = new Client();

        $response = $client->post("https://".$this->url.".shepa.com/api/v1/token", [
            'form_params' => [
                'api' => $this->merchantId,
                'amount' => $amount,
                'callback' => route('transaction.verify.v1',['gateway' => 'shepa']),
            ]
        ]);

        $body = $response->getBody();
        $responseData = json_decode($body, true);

        if ($responseData['success'] === true) {
            $responseResult = $responseData['result'];
            return new PaymentResult($responseResult['token'],$responseResult['url']);
        } else {
            throw new Exception($responseData['errors']);
        }
    }

    public function verify(Transaction $transaction): VerifyResult
    {
        $response = $this->client->post("https://".$this->url.".shepa.com/api/v1/verify", [
            'form_params' => [
                'api' => $this->merchantId,
                'token' => $transaction->getAttributeValue('token'),
                'amount' => $transaction->getAttributeValue('amount'),
            ]
        ]);

        $body = $response->getBody();
        $response = json_decode($body, true);
        if ($response['success']) {
            return new VerifyResult($response['result']['transaction_id']);
        } else {
            return new VerifyResult(null, $response['error']);
        }
    }

    public function reverse(Transaction $transaction): bool
    {
        $data = [
            'api' => $this->merchantId,
            'amount' => $transaction->getAttributeValue('amount'),
            'transaction_id' => $transaction->getAttributeValue('tracking_code'),
            'token' => $transaction->getAttributeValue('token'),
        ];

        $response = $this->client->post("https://".$this->url.".shepa.com/api/v1/refund-transaction", [
            'form_params' => $data
        ]);

        $body = $response->getBody();
        $responseData = json_decode($body, true);
        return $responseData['success'] === true;
    }
}
