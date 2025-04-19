<?php

namespace App\Http\Services\Gateways\Contracts;

interface Gateway
{
    public function payment($amount);
}
