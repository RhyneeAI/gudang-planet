<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
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
            'name' => [
                $this->isMethod('POST') ? 'required' : 'sometimes',
                'string',
                'max:255',
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('company_id', $this->user()->company_id);
                })->ignore($this->category?->id), 
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('categories.validation.name_required'),
            'name.string'   => __('categories.validation.name_string'),
            'name.max'      => __('categories.validation.name_max', ['max' => 255]),
            'name.unique'   => __('categories.validation.name_unique'), 
        ];
    }
}