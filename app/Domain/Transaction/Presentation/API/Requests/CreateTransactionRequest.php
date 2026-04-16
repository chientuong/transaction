<?php

namespace App\Domain\Transaction\Presentation\API\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prefix_id' => ['required', 'integer', 'exists:payment_prefixes,id'],
            'bank_account_id' => ['nullable', 'integer', 'exists:bank_accounts,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'user_id' => ['nullable', 'string'],
            'sync_status' => ['nullable', 'string', 'in:PENDING,RECEIVED_SIGNAL,FAILED,SUCCESS'],
        ];
    }
}
