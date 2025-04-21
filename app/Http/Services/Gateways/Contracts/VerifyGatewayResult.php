<?php

namespace App\Http\Services\Gateways\Contracts;

class VerifyGatewayResult
{
    public function __construct(
        public string $trackingCode,
        public $error = null
    ) {
        $this->error = is_array($error) || is_object($error) ? json_encode($error,320) : $error;
    }

    public function isSuccess(): bool
    {
        return is_null($this->error);
    }
}
