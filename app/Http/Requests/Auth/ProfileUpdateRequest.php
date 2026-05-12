<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'    => ['sometimes', 'string', 'max:255'],
            'email'   => [
                'sometimes',
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'address' => ['sometimes', 'nullable', 'string'],
            'phone'   => ['sometimes', 'nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max'      => __('auth.validation.name_max'),
            'email.email'   => __('auth.validation.email_invalid'),
            'email.unique'  => __('auth.validation.email_unique'),
            'phone.max'     => __('auth.validation.phone_max'),
        ];
    }
}