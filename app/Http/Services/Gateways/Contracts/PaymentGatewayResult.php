<?php

namespace App\Http\Services\Gateways\Contracts;

class PaymentGatewayResult
{
    public function __construct(
        public string $token,
        public string $url
    ) {}
}
