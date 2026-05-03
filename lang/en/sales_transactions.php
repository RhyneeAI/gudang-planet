<?php

return [
    'list'         => 'Sales transaction list retrieved successfully.',
    'detail'       => 'Sales transaction detail retrieved successfully.',
    'stored'       => 'Sales transaction created successfully.',
    'updated'      => 'Sales transaction updated successfully.',
    'deleted'      => 'Sales transaction deleted successfully.',
    'validation'   => [
        'transaction_code_required'   => 'Transaction code is required.',
        'transaction_code_unique'     => 'Transaction code already exists.',
        'transaction_date_required'   => 'Transaction date is required.',
        'transaction_date_invalid'    => 'Transaction date must be a valid date.',
        'customer_id_not_found'       => 'Customer not found.',
        'payment_type_invalid'        => 'Payment type must be one of: CASH, TRANSFER, QRIS.',
        'transaction_status_invalid'  => 'Transaction status must be one of: UNPAID, PROCESS, PAID, CANCEL, PENDING.',
    ],
];
