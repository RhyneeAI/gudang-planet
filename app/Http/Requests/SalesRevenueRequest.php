<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesRevenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_from' => ['required', 'date'],
            'date_to'   => ['required', 'date', 'after_or_equal:date_from'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_from.required'     => __('reports.validation.salesRevenue.date_from_required'),
            'date_to.required'       => __('reports.validation.salesRevenue.date_to_required'),
            'date_to.after_or_equal' => __('reports.validation.salesRevenue.date_to_after'),
        ];
    }
}