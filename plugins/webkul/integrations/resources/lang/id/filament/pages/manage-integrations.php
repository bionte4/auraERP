<?php

return [
    'group'      => 'Umum',
    'breadcrumb' => 'Integrasi',
    'title'      => 'Integrasi',
    'navigation' => [
        'label' => 'Integrasi',
    ],

    'actions' => [
        'test' => 'Uji koneksi',
    ],

    'notifications' => [
        'success' => 'Koneksi berhasil',
        'failed'  => 'Koneksi gagal',
    ],

    'sections' => [
        'smtp' => [
            'title'       => 'SMTP Perusahaan',
            'description' => 'Kirim surat persetujuan dan sistem lewat kotak surat perusahaan. Penerima boleh memakai alamat korporat maupun Gmail.',
        ],
        'ai' => [
            'title'       => 'auraAI',
            'description' => 'Kunci API dan model untuk asisten auraAI. Matikan bila jawaban cukup dari konteks ERP bawaan.',
        ],
        'jira' => [
            'title'       => 'Jira',
            'description' => 'Autentikasi ke Jira Cloud atau Data Center. Pembuatan tiket dapat menyusul setelah koneksi ini hijau.',
        ],
    ],

    'fields' => [
        'enabled'                => 'Aktif',
        'smtp_host'              => 'Host SMTP',
        'smtp_port'              => 'Port',
        'smtp_encryption'        => 'Enkripsi',
        'smtp_encryption_none'   => 'Tidak ada',
        'smtp_username'          => 'Nama pengguna',
        'smtp_password'          => 'Kata sandi',
        'smtp_from_address'      => 'Alamat pengirim',
        'smtp_from_name'         => 'Nama pengirim',
        'ai_api_key'             => 'Kunci API',
        'ai_endpoint'            => 'Endpoint chat completions',
        'ai_model'               => 'Model',
        'jira_base_url'          => 'URL dasar Jira',
        'jira_email'             => 'Email akun',
        'jira_api_token'         => 'Token API',
        'secret_helper'          => 'Kosongkan untuk mempertahankan rahasia yang sudah tersimpan.',
        'last_status'            => 'Status uji terakhir',
        'last_tested_at'         => 'Diuji terakhir pada',
        'last_message'           => 'Pesan uji terakhir',
    ],

    'testers' => [
        'smtp' => [
            'disabled'     => 'Aktifkan SMTP sebelum menguji.',
            'incomplete'   => 'Host, alamat pengirim, dan kata sandi wajib diisi.',
            'no-recipient' => 'Masuk dengan pengguna yang memiliki alamat email untuk menerima surat uji.',
            'subject'      => 'Uji koneksi SMTP auraERP',
            'body'         => 'Ini adalah surat uji dari Integrasi auraERP. Koneksi SMTP berhasil.',
            'success'      => 'Surat uji diterima untuk dikirim ke :email.',
            'failed'       => 'SMTP menolak koneksi: :error',
        ],
        'ai' => [
            'disabled'   => 'Aktifkan auraAI sebelum menguji.',
            'incomplete' => 'Kunci API dan endpoint wajib diisi.',
            'empty'      => 'Penyedia menjawab tanpa isi pesan.',
            'success'    => 'auraAI menjawab berhasil dengan model :model.',
            'failed'     => 'Koneksi auraAI gagal: :error',
        ],
        'jira' => [
            'disabled'   => 'Aktifkan Jira sebelum menguji.',
            'incomplete' => 'URL dasar, email, dan token API wajib diisi.',
            'success'    => 'Terautentikasi ke Jira sebagai :account.',
            'failed'     => 'Koneksi Jira gagal: :error',
        ],
    ],
];
