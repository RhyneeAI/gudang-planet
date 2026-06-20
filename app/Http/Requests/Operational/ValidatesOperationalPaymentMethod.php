<?php

namespace App\Http\Requests\Operational;

use App\Enums\OpsPaymentMethod;
use Illuminate\Validation\Rule;

trait ValidatesOperationalPaymentMethod
{
    protected function paymentMethodRules(): array
    {
        return [
            'payment_method' => ['required', Rule::in(OpsPaymentMethod::values())],
        ];
    }

    protected function paymentMethodMessages(): array
    {
        return [
            'payment_method.required' => __('operational.validation.payment_method_required'),
            'payment_method.in' => __('operational.validation.payment_method_invalid'),
        ];
    }
}
