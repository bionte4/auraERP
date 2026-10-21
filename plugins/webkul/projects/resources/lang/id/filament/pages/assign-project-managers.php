<?php

return [
    'navigation' => [
        'title'         => 'Atur Project Manager',
        'badge-tooltip' => 'Proyek tanpa Project Manager',
    ],

    'title'       => 'Atur Project Manager',
    'description' => 'Tetapkan Project Manager (Operation Manager) untuk semua proyek dalam satu tabel. Perubahan langsung tersimpan dan manajer baru otomatis masuk ke tim proyek. Hanya user berperan Operation Manager yang dapat dipilih.',

    'summary' => [
        'all'        => 'Semua proyek',
        'unassigned' => 'Belum ada manajer',
    ],

    'search-placeholder' => 'Cari proyek…',

    'table' => [
        'project'    => 'Proyek',
        'customer'   => 'Customer',
        'members'    => 'Anggota',
        'end-date'   => 'Tanggal Berakhir',
        'manager'    => 'Project Manager',
        'no-manager' => 'Belum ada manajer',
        'empty'      => 'Tidak ada proyek yang cocok dengan filter saat ini.',
    ],

    'note' => 'Mengosongkan atau mengganti manajer tidak menghapus manajer lama dari anggota tim — hapus manual di Teams bila perlu.',

    'notifications' => [
        'saved' => [
            'title'        => 'Project Manager tersimpan',
            'body'         => ':manager kini memimpin ":project".',
            'body-cleared' => 'Project Manager dikosongkan untuk ":project".',
        ],
        'invalid-manager' => [
            'title' => 'User terpilih bukan Operation Manager',
        ],
    ],
];
