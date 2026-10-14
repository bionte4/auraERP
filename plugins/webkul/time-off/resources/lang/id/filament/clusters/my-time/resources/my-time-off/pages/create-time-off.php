<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Cuti dibuat',
            'body'  => 'Cuti berhasil dibuat.',
        ],

        'overlap' => [
            'title' => 'Pengajuan Cuti yang Tumpang Tindih',
            'body'  => 'Tanggal cuti yang dipilih tumpang tindih dengan permintaan yang sudah ada. Silakan pilih tanggal yang berbeda.',
        ],

        'warning' => [
            'title' => 'Anda tidak memiliki akun karyawan',
            'body'  => 'Anda tidak memiliki akun karyawan. Silakan hubungi administrator Anda.',
        ],

        'invalid_half_day_leave' => [
            'title' => 'Permintaan Cuti Tidak Valid',
            'body'  => 'Cuti setengah hari hanya dapat diajukan untuk satu hari.',
        ],

        'leave_request_denied_no_allocation' => [
            'title' => 'Permintaan Cuti Ditolak',
            'body'  => 'Anda tidak memiliki jatah cuti untuk :leaveType.',
        ],

        'leave_request_denied_insufficient_balance' => [
            'title' => 'Permintaan Cuti Ditolak',
            'body'  => 'Saldo cuti tidak mencukupi. Anda memiliki :available_balance hari yang tersedia. Diminta: :requested_days hari.',
        ],
    ],
];
