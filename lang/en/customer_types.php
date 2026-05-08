<?php

return [
    'list'          => 'Customer type list retrieved successfully.',
    'detail'        => 'Customer type detail retrieved successfully.',
    'stored'        => 'Customer type created successfully.',
    'updated'       => 'Customer type updated successfully.',
    'deleted'       => 'Customer type deleted successfully.',
    'has_customers' => 'Customer type cannot be deleted because it is still used by customers.',
    'validation'    => [
        'type_required'    => 'Customer type name is required.',
        'type_unique'      => 'Customer type name already exists.',
        'type_max'         => 'Customer type name must not exceed 255 characters.',
        'discount_numeric' => 'Discount must be a number.',
        'discount_min'     => 'Discount cannot be less than 0.',
        'discount_max'     => 'Discount cannot be more than 100.',
    ],
];