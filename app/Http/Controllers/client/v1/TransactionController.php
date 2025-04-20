<?php

namespace App\Http\Controllers\client\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequests\VerifyTransactionRequest;
use App\Http\Services\Gateways\Contracts\GatewayManager;
use App\Models\Payment;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function verify(VerifyTransactionRequest $request): JsonResponse
    {
        $gatewayType = $request->get('gateway',config('gateways.default'));

        $gatewayFields = config('gateways.fields');

        $tokenField = $gatewayFields[$gatewayType]['token'];
        $statusField = $gatewayFields[$gatewayType]['status'];
        $successValue = $gatewayFields[$gatewayType]['success_value'];

        $token = $request->get($tokenField);
        $temporaryStatus = $request->get($statusField) == $successValue;

        $transaction = Transaction::query()->where('gateway',$gatewayType)->where('token',$token)->first();
        if ($temporaryStatus){
            if ($transaction->getAttributeValue('status') == 'pending'){
                $gateway = app(GatewayManager::class)->resolve($request->get('gateway', config('gateways.default')));
                $gatewayVerify = $gateway->verify($transaction);
                if ($gatewayVerify->isSuccess()) {
                    $transaction->update(['status' => 'success', 'tracking_code' => $gatewayVerify->trackingCode]);
                }else{
                    $transaction->update(['status' => 'failed', 'detail' => $gatewayVerify->error]);
                }
            }
        }else{
            $transaction->update(['status' => 'failed']);
        }


        $payment = Payment::query()->find($transaction->getAttributeValue('payment_id'))->load('transactions')->loadSum(['transactions as total_paid' => function ($query) {
            $query->where('status','success');
        }],'amount');

        $nextTransaction = null;
        if (isset($gateway) && $payment->amount > $payment->total_paid) {
            $maxAmount = max_amount($payment->amount - $payment->total_paid);
            try {
                $gatewayPayment = $gateway->payment($maxAmount);
            }catch (Exception $exception){
                return my_response(message: $exception->getMessage(),status: 500);
            }
            $nextTransaction = Transaction::query()->create([
                'payment_id' => $payment->getAttributeValue('id'),
                'amount' => $maxAmount,
                'gateway' => $request->get("gateway",config("gateways.default")),
                'token' => $gatewayPayment->token,
                'link'  => $gatewayPayment->url
            ]);
        }elseif ($payment->amount > $payment->total_paid){
            $nextTransaction = Transaction::query()->where('payment_id',$payment->getAttributeValue('id'))->where('amount',max_amount($payment->amount - $payment->total_paid))->first();
        }

        return my_response([
            'current_transaction' => $transaction,
            'next_transaction' => $nextTransaction,
            'payment' => $payment,
        ]);
    }
}
