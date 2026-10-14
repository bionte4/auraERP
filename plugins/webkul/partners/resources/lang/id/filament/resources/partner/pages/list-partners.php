<?php

return [
    'title' => 'Kontak',

    'header-actions' => [
        'template' => [
            'label' => 'Unduh Template Excel',
        ],

        'import' => [
            'label'             => 'Import Excel / CSV',
            'modal-heading'     => 'Bulk Import Customer & Vendor (Kontak)',
            'modal-description' => 'Unggah file .xlsx atau .csv sesuai format template (kolom: name, role [customer/vendor/both], account_type [company/individual], email, phone, mobile, tax_id, website, street1, city, zip, reference).',
            'file-label'        => 'File Excel (.xlsx) atau CSV (.csv)',
            'notification'      => [
                'title' => 'Import kontak selesai',
                'body'  => 'Berhasil menambahkan :created kontak baru, memperbarui :updated kontak, dan melewati :skipped baris kosong.',
            ],
        ],

        'create' => [
            'label' => 'Buat Kontak',
        ],
    ],

    'tabs' => [
        'individuals' => 'Individu',
        'companies'   => 'Perusahaan',
        'employees'   => 'Karyawan',
        'customers'   => 'Pelanggan',
        'vendors'     => 'Pemasok',
        'archived'    => 'Diarsipkan',
    ],
];
