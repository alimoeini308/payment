<?php
namespace App\Http\Services\Gateways\Contracts;

use App\Http\Services\Gateways\Shepa;
use App\Http\Services\Gateways\Zarinpal;
use App\Http\Services\Gateways\Zibal;
use InvalidArgumentException;

class GatewayManager
{
    public function resolve(string $name): Gateway
    {
        return match (strtolower($name)) {
            'zarinpal' => app(Zarinpal::class),
            'shepa' => app(Shepa::class),
            'zibal' => app(Zibal::class),
            default => throw new InvalidArgumentException("Unsupported gateway [$name]"),
        };
    }
}
