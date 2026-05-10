<?php

return [
    'list'             => 'Daftar customer berhasil diambil.',
    'detail'           => 'Detail customer berhasil diambil.',
    'stored'           => 'Customer berhasil dibuat.',
    'updated'          => 'Customer berhasil diperbarui.',
    'deleted'          => 'Customer berhasil dihapus.',
    'has_transactions' => 'Customer tidak dapat dihapus karena masih memiliki transaksi.',
    'validation'       => [
        'name_required'              => 'Nama customer wajib diisi.',
        'name_unique'                => 'Nama customer sudah digunakan.',
        'phone_max'                  => 'Nomor telepon maksimal 20 karakter.',
        'customer_type_uuid_invalid' => 'Format UUID tipe customer tidak valid.',
        'customer_type_not_found'    => 'Tipe customer tidak ditemukan.',
    ],
];