<?php

namespace App\Http\Requests\Absence;

use Illuminate\Foundation\Http\FormRequest;

class AbsAttendanceReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mode' => ['sometimes', 'nullable', 'in:export,data'],
            'date_from' => ['sometimes', 'nullable', 'date'],
            'date_to' => ['sometimes', 'nullable', 'date', 'after_or_equal:date_from'],
            'sub_company_uuid' => ['sometimes', 'nullable', 'uuid'],
            'employee_uuid' => ['sometimes', 'nullable', 'uuid'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:200'],
        ];
    }
}
