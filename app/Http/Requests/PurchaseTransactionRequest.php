<?php

namespace App\Http\Requests;

use App\Enums\PaymentType;
use App\Enums\TransactionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseTransactionRequest extends FormRequest
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
                Rule::unique('purchase_transactions')->ignore($this->purchaseTransaction?->id),
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
            'supplier_id'        => [
                $this->isMethod('POST') ? 'required' : 'sometimes',
                'integer',
                'exists:suppliers,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_code.required'   => __('purchase_transactions.validation.transaction_code_required'),
            'transaction_code.unique'     => __('purchase_transactions.validation.transaction_code_unique'),
            'transaction_date.required'   => __('purchase_transactions.validation.transaction_date_required'),
            'transaction_date.date'       => __('purchase_transactions.validation.transaction_date_invalid'),
            'supplier_id.required'        => __('purchase_transactions.validation.supplier_id_required'),
            'supplier_id.exists'          => __('purchase_transactions.validation.supplier_id_not_found'),
            'payment_type.enum'           => __('purchase_transactions.validation.payment_type_invalid'),
            'transaction_status.enum'     => __('purchase_transactions.validation.transaction_status_invalid'),
        ];
    }
}
