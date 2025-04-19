<?php

namespace App\Http\Services\Gateways;

use App\Http\Services\Gateways\Contracts\Gateway;
use App\Http\Services\Gateways\Contracts\PaymentResult;
use GuzzleHttp\Client;

class Shepa implements Gateway
{

    public function __construct()
    {
        $this->client = new Client();
    }

    public function payment($amount)
    {
        $url=env('APP_ENV')=='production'?'merchant':'sandbox';
        $merchantId=env('SHEPA_API_KEY','sandbox');

        $client = new Client();

        $data = [
            'api' => $merchantId,
            'amount' => $amount,
            'callback' => route('payment.verify.v1',['gateway' => 'shepa']),
        ];

        try {
            $response = $client->post("https://$url.shepa.com/api/v1/token", [
                'form_params' => $data
            ]);

            $body = $response->getBody();
            $responseData = json_decode($body, true);

            if ($responseData['success'] === true) {
                $responseResult = $responseData['result'];
                return new PaymentResult($responseResult['token'],$responseResult['url']);
            } else {
                return response()->json([
                    'message' => 'Transaction failed',
                    'error' => $responseData['errors']
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred while processing payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
