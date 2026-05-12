<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required'              => __('auth.validation.password_required'),
            'password.min'                   => __('auth.validation.password_min'),
            'password.confirmed'             => __('auth.validation.password_confirmed'),
            'password_confirmation.required' => __('auth.validation.password_confirmation_required'),
        ];
    }
}