<?php

return [
    'list'         => 'Daftar transaksi penjualan berhasil diambil.',
    'detail'       => 'Detail transaksi penjualan berhasil diambil.',
    'stored'       => 'Transaksi penjualan berhasil dibuat.',
    'updated'      => 'Transaksi penjualan berhasil diperbarui.',
    'deleted'      => 'Transaksi penjualan berhasil dihapus.',
    'validation'   => [
        'transaction_code_required'   => 'Kode transaksi wajib diisi.',
        'transaction_code_unique'     => 'Kode transaksi sudah ada.',
        'transaction_date_required'   => 'Tanggal transaksi wajib diisi.',
        'transaction_date_invalid'    => 'Tanggal transaksi harus berupa tanggal yang valid.',
        'customer_id_not_found'       => 'Pelanggan tidak ditemukan.',
        'payment_type_invalid'        => 'Jenis pembayaran harus salah satu dari: CASH, TRANSFER, QRIS.',
        'transaction_status_invalid'  => 'Status transaksi harus salah satu dari: UNPAID, PROCESS, PAID, CANCEL, PENDING.',
    ],
];
