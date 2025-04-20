<?php

namespace App\Rules;

use App\Models\Transaction;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTransactionToken implements ValidationRule
{
    protected string $gateway;

    public function __construct(string $gateway)
    {
        $this->gateway = $gateway;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = Transaction::query()
            ->where('gateway', $this->gateway)
            ->where('token', $value)
            //->where('status', 'pending')
            ->exists();

        if (! $exists) {
            $fail(" تراکنشی با این $attribute و وضعیت pending برای درگاه {$this->gateway} یافت نشد. ");
        }
    }
}
