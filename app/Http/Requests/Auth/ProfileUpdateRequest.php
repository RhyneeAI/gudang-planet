<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name'    => ['sometimes', 'string', 'max:255'],
            'email'   => [
                'sometimes',
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'address' => ['sometimes', 'nullable', 'string'],
            'phone'   => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($userId), // ← tambahkan ini
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max'      => __('auth.validation.name_max'),
            'email.email'   => __('auth.validation.email_invalid'),
            'email.unique'  => __('auth.validation.email_unique'),
            'phone.max'     => __('auth.validation.phone_max'),
            'phone.unique'  => __('auth.validation.phone_unique'),
        ];
    }
}