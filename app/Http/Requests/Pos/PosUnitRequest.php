<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PosUnitRequest extends FormRequest
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
                Rule::unique('units')
                ->where('company_id', $this->user()->company_id)
                ->ignore($this->unit?->id)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('units.validation.name_required'),
            'name.string'   => __('units.validation.name_string'),
            'name.max'      => __('units.validation.name_max', ['max' => 255]),
            'name.unique'   => __('units.validation.name_unique'),
        ];
    }
}
