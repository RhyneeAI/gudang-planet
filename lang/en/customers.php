<?php

return [
    'list'             => 'Customer list retrieved successfully.',
    'detail'           => 'Customer detail retrieved successfully.',
    'stored'           => 'Customer created successfully.',
    'updated'          => 'Customer updated successfully.',
    'deleted'          => 'Customer deleted successfully.',
    'has_transactions' => 'Customer cannot be deleted because it still has transactions.',
    'validation'       => [
        'name_required'              => 'Customer name is required.',
        'name_unique'                => 'Customer name already exists.',
        'phone_max'                  => 'Phone number must not exceed 20 characters.',
        'customer_type_uuid_invalid' => 'Invalid customer type UUID format.',
        'customer_type_not_found'    => 'Customer type not found.',
    ],
];