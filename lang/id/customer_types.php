<?php

return [
    'list'          => 'Daftar tipe customer berhasil diambil.',
    'detail'        => 'Detail tipe customer berhasil diambil.',
    'stored'        => 'Tipe customer berhasil dibuat.',
    'updated'       => 'Tipe customer berhasil diperbarui.',
    'deleted'       => 'Tipe customer berhasil dihapus.',
    'has_customers' => 'Tipe customer tidak dapat dihapus karena masih digunakan oleh customer.',
    'validation'    => [
        'type_required'    => 'Nama tipe customer wajib diisi.',
        'type_unique'      => 'Nama tipe customer sudah digunakan.',
        'type_max'         => 'Nama tipe customer maksimal 255 karakter.',
        'discount_numeric' => 'Diskon harus berupa angka.',
        'discount_min'     => 'Diskon tidak boleh kurang dari 0.',
        'discount_max'     => 'Diskon tidak boleh lebih dari 100.',
    ],
];