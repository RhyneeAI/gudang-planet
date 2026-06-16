<?php

namespace App\Http\Requests\Operational;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OpsMandorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->user()->company_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->where('company_id', $companyId),
            ],
            'email' => [
                'sometimes',
                'nullable',
                'email',
                Rule::unique('users', 'email')->where('company_id', $companyId),
            ],
            'address' => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('operational.validation.name_required'),
            'phone.required' => __('operational.validation.phone_required'),
            'phone.unique' => __('operational.validation.phone_unique'),
            'email.email' => __('operational.validation.email_invalid'),
            'email.unique' => __('operational.validation.email_unique'),
        ];
    }
}
