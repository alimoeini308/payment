<?php

namespace App\Http\Services\Gateways\Contracts;

use App\Models\Transaction;

interface Gateway
{
    public function payment($amount) : PaymentResult;
    public function verify(Transaction $transaction) : VerifyResult;
    public function reverse(Transaction $transaction) : bool;
}
