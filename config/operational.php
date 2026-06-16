<?php

return [
    'expense_edit_window_days' => (int) env('OPS_EXPENSE_EDIT_WINDOW_DAYS', 3),
    'expense_max_edit_count'   => (int) env('OPS_EXPENSE_MAX_EDIT_COUNT', 1),
    'max_sub_companies_per_mandor' => (int) env('OPS_MAX_SUB_COMPANIES_PER_MANDOR', 10),
    'proof_disk'               => 'public',
    'proof_directory'          => 'operational/proofs',
];
