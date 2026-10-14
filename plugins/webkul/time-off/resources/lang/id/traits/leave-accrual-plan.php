<?php

return [
    'form' => [
        'fields' => [
            'accrual-amount'              => 'Jumlah Akrual',
            'accrual-value-type'          => 'Jenis Nilai Akrual',
            'accrual-frequency'           => 'Frekuensi Akrual',
            'accrual-day'                 => 'Hari Akrual',
            'day-of-month'                => 'Hari dalam Bulan',
            'first-day-of-month'          => 'Hari Pertama Bulan',
            'second-day-of-month'         => 'Hari Kedua Bulan',
            'first-period-month'          => 'Bulan Periode Pertama',
            'first-period-day'            => 'Hari Periode Pertama',
            'second-period-month'         => 'Bulan Periode Kedua',
            'second-period-day'           => 'Hari Periode Kedua',
            'first-period-year'           => 'Tahun Periode Pertama',
            'cap-accrued-time'            => 'Batasi waktu terakrual',
            'days'                        => 'Hari',
            'start-count'                 => 'Hitungan Awal',
            'start-type'                  => 'Jenis Mulai',
            'action-with-unused-accruals' => 'Tindakan untuk akrual yang tidak digunakan',
            'milestone-cap'               => 'Batas Tonggak',
            'maximum-leave-yearly'        => 'Maksimum Cuti Tahunan',
            'accrual-validity'            => 'Masa Berlaku Akrual',
            'accrual-validity-count'      => 'Jumlah Masa Berlaku Akrual',
            'accrual-validity-type'       => 'Jenis Masa Berlaku Akrual',
            'advanced-accrual-settings'   => 'Pengaturan Akrual Lanjutan',
            'after-allocation-start'      => 'Setelah Tanggal Mulai Alokasi',
            'to-date'                     => 'Hingga Tanggal',
        ],
    ],

    'table' => [
        'columns' => [
            'accrual-amount'     => 'Jumlah Akrual',
            'accrual-value-type' => 'Jenis Nilai Akrual',
            'frequency'          => 'Frekuensi',
            'maximum-leave-days' => 'Maksimum Hari Cuti',
        ],

        'groups' => [
            'accrual-amount'       => 'Jumlah Akrual',
            'accrual-value-type'   => 'Jenis Nilai Akrual',
            'frequency'            => 'Frekuensi',
            'maximum-leave-days'   => 'Maksimum Hari Cuti',
        ],

        'filters' => [
            'accrual-frequency'           => 'Frekuensi Akrual',
            'start-type'                  => 'Jenis Mulai',
            'cap-accrued-time'            => 'Batasi Waktu Terakrual',
            'action-with-unused-accruals' => 'Tindakan untuk Akrual yang Tidak Digunakan',
            'accrual-amount'              => 'Jumlah Akrual',
            'accrual-frequency'           => 'Frekuensi Akrual',
            'start-type'                  => 'Jenis Mulai',
            'created-at'                  => 'Dibuat Pada',
            'updated-at'                  => 'Diperbarui Pada',
        ],

        'header-actions' => [
            'created' => [
                'title' => 'Rencana Akrual Cuti Baru',

                'notification' => [
                    'title' => 'Rencana akrual cuti dibuat',
                    'body'  => 'Rencana akrual cuti berhasil dibuat.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Rencana akrual cuti diperbarui',
                    'body'  => 'Rencana akrual cuti berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rencana akrual cuti dihapus',
                    'body'  => 'Rencana akrual cuti berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [

            'delete' => [
                'notification' => [
                    'title' => 'Rencana akrual cuti dihapus',
                    'body'  => 'Rencana akrual cuti berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'accrual-amount'              => 'Jumlah Akrual',
            'accrual-value-type'          => 'Jenis Nilai Akrual',
            'accrual-frequency'           => 'Frekuensi Akrual',
            'accrual-day'                 => 'Hari Akrual',
            'day-of-month'                => 'Hari dalam Bulan',
            'first-day-of-month'          => 'Hari Pertama Bulan',
            'second-day-of-month'         => 'Hari Kedua Bulan',
            'first-period-month'          => 'Bulan Periode Pertama',
            'first-period-day'            => 'Hari Periode Pertama',
            'second-period-month'         => 'Bulan Periode Kedua',
            'second-period-day'           => 'Hari Periode Kedua',
            'first-period-year'           => 'Tahun Periode Pertama',
            'cap-accrued-time'            => 'Batasi waktu terakrual',
            'days'                        => 'Hari',
            'start-count'                 => 'Hitungan Awal',
            'start-type'                  => 'Jenis Mulai',
            'action-with-unused-accruals' => 'Tindakan untuk akrual yang tidak digunakan',
            'milestone-cap'               => 'Batas Tonggak',
            'maximum-leave-yearly'        => 'Maksimum Cuti Tahunan',
            'accrual-validity'            => 'Masa Berlaku Akrual',
            'accrual-validity-count'      => 'Jumlah Masa Berlaku Akrual',
            'accrual-validity-type'       => 'Jenis Masa Berlaku Akrual',
            'advanced-accrual-settings'   => 'Pengaturan Akrual Lanjutan',
            'after-allocation-start'      => 'Setelah Tanggal Mulai Alokasi',
        ],
    ],
];
