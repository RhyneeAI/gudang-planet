<?php
return [
    // ========== RESPONSE MESSAGES (untuk controller) ==========
    'stored'   => 'Category created successfully.',
    'updated'  => 'Category updated successfully.',
    'deleted'  => 'Category deleted successfully.',
    'list'     => 'Category list retrieved successfully.',
    'detail'   => 'Category detail retrieved successfully.',
    'not_found'=> 'Category not found.',
    'has_products' => 'Category has products.',
    'unauthorized' => 'You are not authorized to access this category.',

    // ========== VALIDATION MESSAGES (untuk FormRequest) ==========
    'validation' => [
        'name_required' => 'The category name is required.',
        'name_string'   => 'The category name must be a string.',
        'name_max'      => 'The category name may not be greater than 255 characters.',
    ],
];