<?php

namespace App\Http\Requests\Operational;

trait ValidatesOperationalProofFiles
{
    protected function proofFileRules(bool $isStore): array
    {
        $arrayRule = $isStore ? 'required' : 'nullable';

        return [
            'proof_files' => [$arrayRule, 'array', 'min:1', 'max:3'],
            'proof_files.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    protected function proofFileMessages(): array
    {
        return [
            'proof_files.required' => __('operational.validation.proof_file_required'),
            'proof_files.array' => __('operational.validation.proof_files_array'),
            'proof_files.min' => __('operational.validation.proof_files_min'),
            'proof_files.max' => __('operational.validation.proof_files_max'),
            'proof_files.*.file' => __('operational.validation.proof_file_file'),
            'proof_files.*.image' => __('operational.validation.proof_file_invalid'),
            'proof_files.*.mimes' => __('operational.validation.proof_file_invalid'),
            'proof_files.*.max' => __('operational.validation.proof_file_max'),
        ];
    }
}
