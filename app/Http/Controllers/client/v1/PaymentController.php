<?php

namespace App\Http\Controllers\client\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequests\CreatePaymentRequest;
use App\Http\Requests\PaymentRequests\VerifyPaymentRequest;
use App\Http\Services\Gateways\Contracts\Gateway;
use App\Models\Payment;
use Exception;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function create(CreatePaymentRequest $request, Gateway $gateway): JsonResponse
    {
        try {
            $paymentResult = $gateway->payment($request->get('amount'));
            $data=$request->validated();
            $data['token'] = $paymentResult->token;
            $payment=Payment::query()->create($data);
            return my_response([
                "payment_url" => $paymentResult->url,
                "payment" => $payment
            ]);
        }catch (Exception $exception){
            return my_response(message: $exception->getMessage(),status: 500);
        }
    }

    public function verify(VerifyPaymentRequest $request): JsonResponse
    {
        $gateway = $request->get('gateway');

        $gatewayFields = config('gateways.fields');

        $tokenField = $gatewayFields[$gateway]['token'];
        $statusField = $gatewayFields[$gateway]['status'];
        $successValue = $gatewayFields[$gateway]['success_value'];

        $token = $request->get($tokenField);
        $status = $request->get($statusField) == $successValue ? 'success' : 'failed';

        $payment = Payment::query()->where('gateway',$gateway)->where('status','pending')->where('token',$token)->first();
        $payment->update(['status'=>$status]);
        return my_response($payment);
    }
}
