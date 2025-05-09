<?php

namespace App\Http\Requests\PaymentRequests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "username"  => "required|string",
            "phone" => "required|numeric",
            "description" => "string",
            "amount" => "numeric|min:1000",
            "gateway" => "in:zarinpal,shepa,zibal"
        ];
    }
}
