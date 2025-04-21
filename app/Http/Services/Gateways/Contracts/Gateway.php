<?php

namespace App\Http\Services\Gateways\Contracts;

use App\Models\Transaction;

interface Gateway
{
    public function payment($amount) : PaymentGatewayResult;
    public function verify(Transaction $transaction) : VerifyGatewayResult;
    public function reverse(Transaction $transaction) : bool;
}
