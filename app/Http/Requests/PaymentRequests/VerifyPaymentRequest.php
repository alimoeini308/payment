<?php

namespace App\Http\Requests\PaymentRequests;

use App\Rules\ValidPaymentToken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyPaymentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gateway' => 'required|in:zarinpal,shepa,zibal',

            // Zarinpal
            'Authority' => ['required_if:gateway,zarinpal',new ValidPaymentToken('zarinpal')],
            'Status' => 'required_if:gateway,zarinpal',

            // Shepa
            'token' => ['required_if:gateway,shepa',new ValidPaymentToken('shepa')],
            'status' => 'required_if:gateway,shepa',

            // Zibal
            'trackId' => ['required_if:gateway,zibal',new ValidPaymentToken('zibal')],
            'success' => 'required_if:gateway,zibal',
        ];
    }
}
