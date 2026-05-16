<?php

namespace App\Http\Requests;

use App\Enums\PaymentType;
use App\Models\Supplier;
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
            'supplier_uuid'        => [
                'required',
                'string',
                'uuid',
                function ($attribute, $value, $fail) {
                    // Pastikan format UUID valid sebelum query
                    if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
                        return; // Biarkan uuid rule handle error message
                    }
                    
                    $supplierExists = Supplier::where('uuid', $value)
                        ->where('company_id', $this->user()->company_id)
                        ->exists();
                    
                    if (!$supplierExists) {
                        $fail(__('purchase_transactions.validation.supplier_uuid_not_found'));
                    }
                }
            ],
            'transaction_date'     => ['required', 'date'],
            'discount'             => ['nullable', 'numeric', 'min:0'],
            'total'                => ['required', 'numeric', 'min:0'],
            'paid'                 => ['required', 'numeric', 'min:0'],
            'payment_type'         => ['required', Rule::enum(PaymentType::class)],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_uuid' => ['required', 'string', 'uuid', 'exists:products,uuid'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],
            'items.*.buy_price'    => ['required', 'numeric', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                if ($this->payment_type === PaymentType::CICIL->value) {
                    return;
                }
                
                $total    = $this->total ?? 0;
                $discount = $this->discount ?? 0;
                $paid     = $this->paid ?? 0;

                if ($discount > $total) {
                    $validator->errors()->add(
                        'discount',
                        __('purchase_transactions.validation.discount_greater_than_total')
                    );
                }

                if ($paid < $total) {
                    $validator->errors()->add(
                        'paid',
                        __('purchase_transactions.validation.paid_lower_than_total')
                    );
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_uuid.required'          => __('purchase_transactions.validation.supplier_uuid_required'),
            'supplier_uuid.uuid'              => __('purchase_transactions.validation.supplier_uuid_not_valid'),
            'supplier_uuid.exists'            => __('purchase_transactions.validation.supplier_uuid_not_found'),
            'transaction_date.required'       => __('purchase_transactions.validation.transaction_date_required'),
            'transaction_date.date'           => __('purchase_transactions.validation.transaction_date_invalid'),
            'payment_type.required'           => __('purchase_transactions.validation.payment_type_required'),
            'payment_type.enum'               => __('purchase_transactions.validation.payment_type_invalid'),
            'items.required'                  => __('purchase_transactions.validation.items_required'),
            'items.min'                       => __('purchase_transactions.validation.items_min'),
            'items.*.product_uuid.uuid'       => __('purchase_transactions.validation.item_product_uuid'),
            'items.*.product_uuid.required'   => __('purchase_transactions.validation.item_product_required'),
            'items.*.product_uuid.exists'     => __('purchase_transactions.validation.item_product_not_found'),
            'items.*.quantity.required'       => __('purchase_transactions.validation.item_quantity_required'),
            'items.*.quantity.min'            => __('purchase_transactions.validation.item_quantity_min'),
            'items.*.buy_price.required'      => __('purchase_transactions.validation.item_buy_price_required'),
            'items.*.buy_price.min'           => __('purchase_transactions.validation.item_buy_price_min'),
        ];
    }

    public function getSupplierId(): ?int
    {
        if (!$this->supplier_uuid) return null;
        return Supplier::where('uuid', $this->supplier_uuid)
            ->where('company_id', $this->user()->company_id)
            ->value('id');
    }
}