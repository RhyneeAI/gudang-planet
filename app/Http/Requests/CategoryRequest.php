<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $categoryId = $this->category?->id;
        
        return [
            'name' => [
                $this->isMethod('POST') ? 'required' : 'sometimes',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($categoryId) {
                    // Case-insensitive duplicate check within same company
                    $exists = Category::where('company_id', $this->user()->company_id)
                        ->whereRaw('LOWER(name) = LOWER(?)', [$value])
                        ->when($categoryId, function ($query) use ($categoryId) {
                            return $query->where('id', '!=', $categoryId);
                        })
                        ->exists();
                    
                    if ($exists) {
                        $fail(__('categories.validation.name_unique'));
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('categories.validation.name_required'),
            'name.string'   => __('categories.validation.name_string'),
            'name.max'      => __('categories.validation.name_max', ['max' => 255]),
        ];
    }
}