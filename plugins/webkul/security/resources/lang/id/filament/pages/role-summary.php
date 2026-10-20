<?php

return [
    'title' => 'Ringkasan Role & Hak Akses',

    'navigation' => [
        'label' => 'Ringkasan Role',
    ],

    'intro' => [
        'heading'     => 'Setiap role bisa apa saja?',
        'description' => 'Ringkasan hanya-baca dari :total permission yang tersedia. Klik kartu role untuk membandingkannya berdampingan di matriks; klik nama item untuk melihat nama permission persisnya. Pengubahan tetap dilakukan di Roles.',
    ],

    'search' => [
        'placeholder' => 'Cari permission atau item, mis. delete_employee…',
    ],

    'card' => [
        'permissions' => 'permission',
        'users'       => 'pengguna',
        'resources'   => 'Resource',
        'pages'       => 'Halaman',
        'widgets'     => 'Widget',
        'other'       => 'Lainnya',
        'other-hint'  => 'Permission yang tidak terpetakan ke resource, halaman, atau widget (mis. permission API atau legacy).',
        'baseline'    => 'Baseline',
        'custom'      => 'Custom',
        'show-users'  => 'Lihat pengguna',
        'edit'        => 'Edit di Roles',
    ],

    'compare' => [
        'selected'         => ':count role dipilih untuk dibandingkan',
        'clear'            => 'Bersihkan pilihan',
        'hint'             => 'Tips: klik dua kartu role atau lebih untuk membandingkannya.',
        'only-differences' => 'Hanya tampilkan perbedaan',
    ],

    'categories' => [
        'resources' => 'Resource',
        'pages'     => 'Halaman',
        'widgets'   => 'Widget',
    ],

    'table' => [
        'item' => 'Item',
    ],

    'empty' => 'Tidak ada item yang cocok dengan pencarian atau filter.',
];
