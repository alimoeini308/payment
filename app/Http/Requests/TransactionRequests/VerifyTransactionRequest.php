<?php

namespace App\Http\Requests\TransactionRequests;

use App\Rules\ValidTransactionToken;
use Illuminate\Foundation\Http\FormRequest;

class VerifyTransactionRequest extends FormRequest
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
            'Authority' => ['required_if:gateway,zarinpal',new ValidTransactionToken('zarinpal')],
            'Status' => 'required_if:gateway,zarinpal',

            // Shepa
            'token' => ['required_if:gateway,shepa',new ValidTransactionToken('shepa')],
            'status' => 'required_if:gateway,shepa',

            // Zibal
            'trackId' => ['required_if:gateway,zibal',new ValidTransactionToken('zibal')],
            'success' => 'required_if:gateway,zibal',
        ];
    }
}
