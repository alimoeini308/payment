<?php

namespace App\Http\Controllers\admin\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequests\CreatePaymentRequest;
use App\Http\Requests\PaymentRequests\ReversePaymentRequest;
use App\Http\Services\Gateways\Contracts\GatewayManager;
use App\Models\Payment;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function create(CreatePaymentRequest $request): JsonResponse
    {
        $insertPaymentData = $request->validated();
        unset($insertPaymentData['gateway']);
        $payment=Payment::query()->create($insertPaymentData);

        if($request->has('amount')){
            $maxAmount = max_amount($request->get('amount'));
            try {
                $gateway = app(GatewayManager::class)->resolve($request->get('gateway', config('gateways.default')));
                $gatewayResult = $gateway->payment($maxAmount);
            }catch (Exception $exception){
                return my_response(message: $exception->getMessage(),status: 500);
            }
            Transaction::query()->create([
                'payment_id' => $payment->id,
                'amount' => $maxAmount,
                'gateway' => $request->get("gateway",config("gateways.default")),
                'token' => $gatewayResult->token,
                'link'  => $gatewayResult->url
            ]);
        }
        return my_response($payment->load('transactions'));
    }
    public function reverse(ReversePaymentRequest $request): JsonResponse
    {
        $transactions = Transaction::query()->where('payment_id',$request->get('payment_id'))->get();
        foreach ($transactions as $transaction) {
            try {
                $gateway = app(GatewayManager::class)->resolve($transaction->getAttributeValue('gateway'));
            }catch (Exception $exception){
                return my_response(message: $exception->getMessage(),status: 500);
            }
            if ($transaction->status == 'success') {
                $reversed = $gateway->reverse($transaction);
                if ($reversed) {
                    $transaction->update(['status' => 'reversed']);
                }
            }
        }
        return my_response($transactions);
    }
    public function payments(): JsonResponse
    {
        return my_response(Payment::query()->with('transactions')->withSum(['transactions as total_paid' => function ($query) {
            $query->where('status','success');
        }],'amount')->latest()->paginate());
    }
}
