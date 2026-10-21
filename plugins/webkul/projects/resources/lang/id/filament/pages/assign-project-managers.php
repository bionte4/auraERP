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

    'actions' => [
        'template'           => 'Unduh Template',
        'import'             => 'Impor Excel / CSV',
        'import-heading'     => 'Penetapan Project Manager massal',
        'import-description' => 'Unduh template terlebih dahulu: isinya sudah memuat semua proyek beserta email manajer saat ini. Ubah kolom new_manager_email (kosongkan untuk melepas manajer), lalu unggah kembali berkasnya. Proyek dicocokkan via project_id, manajer via email, dan hanya Operation Manager yang diterima. Sheet kedua berisi daftar email manajer yang valid.',
        'import-file'        => 'Berkas template (.xlsx / .csv)',
    ],

    'notifications' => [
        'saved' => [
            'title'        => 'Project Manager tersimpan',
            'body'         => ':manager kini memimpin ":project".',
            'body-cleared' => 'Project Manager dikosongkan untuk ":project".',
        ],
        'invalid-manager' => [
            'title' => 'User terpilih bukan Operation Manager',
        ],
        'imported' => [
            'title' => 'Impor Project Manager selesai',
            'body'  => ':updated diperbarui, :unchanged tidak berubah, :skipped dilewati.',
        ],
    ],
];
