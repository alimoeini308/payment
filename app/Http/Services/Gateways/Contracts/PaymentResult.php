<?php

namespace App\Http\Services\Gateways\Contracts;

class PaymentResult
{
    public function __construct(
        public string $token,
        public string $url
    ) {}
}
