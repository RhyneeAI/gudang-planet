<?php

return [
    'login'                   => 'Login successfully',
    'logout'                  => 'Logout successfully',
    'profile_retrieved'        => 'Profile retrieved successfully.',
    'profile_updated'          => 'Profile updated successfully.',
    'password_reset_success'   => 'Password changed successfully. Please login again.',
    'forgot_password_verified' => 'Username found. Please reset your password.',
    'invalid_reset_token'      => 'Invalid or expired reset token.',
    'validation'               => [
        'username_required'              => 'Username is required.',
        'username_not_found'             => 'Username not found.',
        'password_required'              => 'Password is required.',
        'password_min'                   => 'Password must be at least 8 characters.',
        'password_confirmed'             => 'Password confirmation does not match.',
        'password_confirmation_required' => 'Password confirmation is required.',
        'name_max'                       => 'Name must not exceed 255 characters.',
        'email_invalid'                  => 'Invalid email format.',
        'email_unique'                   => 'Email already exists.',
        'phone_max'                      => 'Phone number must not exceed 20 characters.',
    ],
];