<?php

return [
    'list'          => 'Marketing list retrieved successfully.',
    'detail'        => 'Marketing detail retrieved successfully.',
    'stored'        => 'Marketing created successfully.',
    'updated'       => 'Marketing updated successfully.',
    'deleted'       => 'Marketing deleted successfully.',
    'has_relations' => 'Marketing cannot be deleted because it is still related to products or transactions.',
    'validation' => [
        'name_required' => 'Name is required.',
        'name_max'      => 'Name may not be greater than 255 characters.',
        'phone_required' => 'Phone number is required.',
        'phone_unique'  => 'This phone number is already in use.',
        'phone_max'     => 'Phone number may not be greater than 20 characters.',
        'email_invalid' => 'Please enter a valid email address.',
        'email_unique'  => 'This email is already in use.',
    ],
];