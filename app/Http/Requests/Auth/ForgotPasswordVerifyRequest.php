<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordVerifyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => 'required|string|exists:users,phone', 
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => __('auth.phone_required'),
            'phone.exists'   => __('auth.phone_not_found'),
        ];
    }
}