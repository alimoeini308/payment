<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ValidPaymentToken implements ValidationRule
{
    protected string $gateway;

    public function __construct(string $gateway)
    {
        $this->gateway = $gateway;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = DB::table('payments')
            ->where('gateway', $this->gateway)
            ->where('token', $value)
            ->where('status', 'pending')
            ->exists();

        if (! $exists) {
            $fail("تراکنشی با این $attribute و وضعیت pending برای درگاه {$this->gateway} یافت نشد.");
        }
    }
}
