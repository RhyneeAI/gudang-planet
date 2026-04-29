<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units')->ignore($this->unit?->id) 
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('unit.validation.name_required'),
            'name.string'   => __('unit.validation.name_string'),
            'name.max'      => __('unit.validation.name_max', ['max' => 255]),
            'name.unique'   => __('unit.validation.name_unique'),
        ];
    }
}