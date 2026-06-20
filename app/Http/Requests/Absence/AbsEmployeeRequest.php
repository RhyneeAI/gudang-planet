<?php

namespace App\Http\Requests\Absence;

use App\Models\AbsBranch;
use App\Models\AbsShift;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AbsEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('absEmployee');
        $userId = $user instanceof \App\Models\User ? $user->id : null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($userId),
            ],
            'password' => [$this->isMethod('post') ? 'required' : 'nullable', 'string', 'min:6'],
            'branch_uuid' => ['required', 'uuid', function ($attribute, $value, $fail) {
                if (!AbsBranch::where('uuid', $value)->where('company_id', $this->user()->company_id)->exists()) {
                    $fail(__('absence.validation.branch_uuid_not_found'));
                }
            }],
            'shift_uuid' => ['required', 'uuid', function ($attribute, $value, $fail) {
                if (!AbsShift::where('uuid', $value)->where('company_id', $this->user()->company_id)->exists()) {
                    $fail(__('absence.validation.shift_uuid_not_found'));
                }
            }],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
