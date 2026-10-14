<?php

return [
    'notification' => [
        'title'              => 'Cuti diperbarui',
        'body'               => 'Cuti berhasil diperbarui.',
        'action_not_allowed' => [
            'title' => 'Tindakan Tidak Diizinkan',
            'body'  => 'Anda tidak dapat mengubah permintaan cuti ini karena berada dalam status terkunci.',
        ],
        'overlap' => [
            'title' => 'Pengajuan Cuti yang Tumpang Tindih',
            'body'  => 'Tanggal cuti yang dipilih tumpang tindih dengan permintaan yang sudah ada. Silakan pilih tanggal yang berbeda.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Cuti dihapus',
                'body'  => 'Cuti berhasil dihapus.',
            ],
        ],
    ],
];
