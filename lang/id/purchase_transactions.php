<?php

return [
    'list'         => 'Daftar transaksi pembelian berhasil diambil.',
    'detail'       => 'Detail transaksi pembelian berhasil diambil.',
    'stored'       => 'Transaksi pembelian berhasil dibuat.',
    'updated'      => 'Transaksi pembelian berhasil diperbarui.',
    'deleted'      => 'Transaksi pembelian berhasil dihapus.',
    'validation'   => [
        'transaction_code_required'   => 'Kode transaksi wajib diisi.',
        'transaction_code_unique'     => 'Kode transaksi sudah ada.',
        'transaction_date_required'   => 'Tanggal transaksi wajib diisi.',
        'transaction_date_invalid'    => 'Tanggal transaksi harus berupa tanggal yang valid.',
        'supplier_id_required'        => 'Supplier wajib dipilih.',
        'supplier_id_not_found'       => 'Supplier tidak ditemukan.',
        'payment_type_invalid'        => 'Jenis pembayaran harus salah satu dari: CASH, TRANSFER, QRIS.',
        'transaction_status_invalid'  => 'Status transaksi harus salah satu dari: UNPAID, PROCESS, PAID, CANCEL, PENDING.',
    ],
];
