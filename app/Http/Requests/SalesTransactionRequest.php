<?php

namespace App\Http\Requests;

use App\Enums\PaymentType;
use App\Enums\TransactionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_code'   => [
                $this->isMethod('POST') ? 'required' : 'sometimes',
                'string',
                'max:255',
                Rule::unique('sales_transactions')->ignore($this->salesTransaction?->id),
            ],
            'transaction_date'   => [
                $this->isMethod('POST') ? 'required' : 'sometimes',
                'date',
            ],
            'discount'           => ['sometimes', 'numeric', 'min:0'],
            'total'              => ['sometimes', 'numeric', 'min:0'],
            'paid'               => ['sometimes', 'numeric', 'min:0'],
            'payment_type'       => [
                'sometimes',
                Rule::enum(PaymentType::class),
            ],
            'transaction_status' => [
                'sometimes',
                Rule::enum(TransactionStatus::class),
            ],
            'customer_id'        => [
                'sometimes',
                'nullable',
                'integer',
                'exists:customers,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_code.required'   => __('sales_transactions.validation.transaction_code_required'),
            'transaction_code.unique'     => __('sales_transactions.validation.transaction_code_unique'),
            'transaction_date.required'   => __('sales_transactions.validation.transaction_date_required'),
            'transaction_date.date'       => __('sales_transactions.validation.transaction_date_invalid'),
            'customer_id.exists'          => __('sales_transactions.validation.customer_id_not_found'),
            'payment_type.enum'           => __('sales_transactions.validation.payment_type_invalid'),
            'transaction_status.enum'     => __('sales_transactions.validation.transaction_status_invalid'),
        ];
    }
}
