<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordVerifyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'exists:users,username'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => __('auth.validation.username_required'),
            'username.exists'   => __('auth.validation.username_not_found'),
        ];
    }
}