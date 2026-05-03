<?php

return [
    'list'         => 'Purchase transaction list retrieved successfully.',
    'detail'       => 'Purchase transaction detail retrieved successfully.',
    'stored'       => 'Purchase transaction created successfully.',
    'updated'      => 'Purchase transaction updated successfully.',
    'deleted'      => 'Purchase transaction deleted successfully.',
    'validation'   => [
        'transaction_code_required'   => 'Transaction code is required.',
        'transaction_code_unique'     => 'Transaction code already exists.',
        'transaction_date_required'   => 'Transaction date is required.',
        'transaction_date_invalid'    => 'Transaction date must be a valid date.',
        'supplier_id_required'        => 'Supplier is required.',
        'supplier_id_not_found'       => 'Supplier not found.',
        'payment_type_invalid'        => 'Payment type must be one of: CASH, TRANSFER, QRIS.',
        'transaction_status_invalid'  => 'Transaction status must be one of: UNPAID, PROCESS, PAID, CANCEL, PENDING.',
    ],
];
