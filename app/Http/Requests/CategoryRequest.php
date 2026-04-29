<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('categories.validation.name_required'),
            'name.string'   => __('categories.validation.name_string'),
            'name.max'      => __('categories.validation.name_max', ['max' => 255]),
        ];
    }
}