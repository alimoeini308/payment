<?php

namespace App\Http\Controllers\client\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequests\PaymentRequest;
use App\Http\Services\Gateways\Contracts\GatewayManager;
use App\Models\Payment;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function payment(Payment $payment,PaymentRequest $request): JsonResponse
    {
        if ($request->has('amount') && is_null($payment->getAttributeValue('amount'))) {
            if($payment->transactions()->doesntExist()){
                $maxAmount = max_amount($request->get('amount'));
                try {
                    $gateway = app(GatewayManager::class)->resolve($request->get('gateway', config('gateways.default')));
                    $gatewayResult = $gateway->payment($maxAmount);
                }catch (Exception $exception){
                    return my_response(message: $exception->getMessage(),status: 500);
                }
                $payment->update(["amount" => $request->get('amount')]);
                Transaction::query()->create([
                    'payment_id' => $payment->getAttributeValue('id'),
                    'amount' => $maxAmount,
                    'gateway' => $request->get("gateway",config("gateways.default")),
                    'token' => $gatewayResult->token,
                    'link'  => $gatewayResult->url
                ]);
            }
        }
        $payment->load('transactions')->loadSum(['transactions as total_paid' => function ($query) {
            $query->where('status','success');
        }],'amount');
        return my_response($payment);
    }
}
