<?php

return [

    'uninstall' => [
        'title'   => 'Konfirmasi Pencopotan',
        'message' => 'Apakah Anda yakin ingin mencopot pemasangan plugin :name?',
        'warning' => '⚠️ Tindakan ini tidak dapat dibatalkan dan akan menghapus data secara permanen.',
    ],

    'dependents' => [
        'title'         => 'Plugin Dependensi',
        'description'   => 'Plugin-plugin ini bergantung pada plugin ini. Setiap dependensi yang sudah terpasang harus dicopot pemasangannya terlebih dahulu.',
        'installed'     => 'Terpasang',
        'not_installed' => 'Belum Terpasang',
    ],

    'dependency_warning' => [
        'title'   => 'Tindakan Diperlukan',
        'message' => '⚠️ Harap copot pemasangan plugin dependen berikut terlebih dahulu sebelum mencopot pemasangan :name.',
    ],

    'data_impact' => [
        'title'       => 'Dampak Data',
        'description' => 'Tabel basis data berikut berisi data yang akan dihapus secara permanen.',
        'records'     => ':count data',
    ],

];
