<?php

return [
    'widget' => [
        'button'      => 'auraAI',
        'title'       => 'auraAI Assistant (Chatter & ERP)',
        'reset'       => 'Reset',
        'placeholder' => 'Tanya data absensi, kontrak, kinerja, proyek...',
        'send'        => 'Kirim',
        'greeting'    => "Halo! Saya auraAI Assistant yang terhubung dengan Chatter & data ERP Anda.\nPilih topik cepat di atas atau ketik pertanyaan Anda.",
        'presets'     => [
            'attendance'  => 'Absensi hari ini',
            'contracts'   => 'Kontrak akan berakhir',
            'performance' => 'Ringkasan kinerja & evaluasi',
            'employees'   => 'Ringkasan karyawan',
        ],
    ],

    'chatter' => [
        'button'             => 'Tanya AI',
        'modal-heading'      => 'auraAI Assistant — Analisis Dokumen & Chatter',
        'modal-description'  => 'AI akan merangkum data dokumen ini beserta riwayat diskusi Chatter, atau menjawab pertanyaan Anda langsung ke dalam thread Chatter.',
        'prompt-label'       => 'Instruksi atau pertanyaan (opsional)',
        'prompt-placeholder' => 'Kosongkan untuk membuat ringkasan otomatis dokumen ini...',
        'submit'             => 'Buat Jawaban AI di Chatter',
        'reply-subject'      => 'Ringkasan & Analisis auraAI',
        'notification-title' => 'Analisis AI ditambahkan',
        'notification-body'  => 'auraAI telah memposting jawaban di panel Chatter dokumen ini.',
    ],

    'summary' => [
        'header'         => 'Ringkasan :type: :title',
        'recent-chatter' => 'Diskusi/Catatan Chatter Terakhir:',
    ],

    'employee' => [
        'job'              => 'Jabatan: :job',
        'department'       => 'Departemen: :department',
        'type'             => 'Tipe Karyawan: :type',
        'join-date'        => 'Tanggal Masuk: :date',
        'contract-end'     => 'Akhir Kontrak: :date (:days hari lagi)',
        'attendance-month' => 'Absensi bulan ini: :present hadir (:late terlambat)',
        'performance-plan' => 'Rencana Kinerja terbaru: :title (Progres: :score)',
        'evaluation'       => 'Evaluasi Kinerja terbaru: :title (Skor: :score)',
    ],

    'insights' => [
        'no-employee-module'        => 'Modul Karyawan belum aktif.',
        'no-attendance-module'      => 'Modul Absensi belum aktif.',
        'no-project-module'         => 'Modul Proyek belum aktif.',
        'contract-heading'          => 'Status Kontrak Karyawan (≤ 30 Hari):',
        'no-expiring-contracts'     => 'Tidak ada kontrak karyawan aktif yang berakhir dalam 30 hari ke depan.',
        'expired'                   => 'sudah berakhir',
        'days-left'                 => 'hari lagi',
        'type-breakdown'            => 'Komposisi Tipe Karyawan Aktif:',
        'unassigned'                => 'Belum ditentukan',
        'attendance-today'          => 'Absensi Hari Ini (:date) — Total: :total, Terlambat: :late',
        'active-sites'              => 'Lokasi Kerja (Geofence) Aktif: :sites',
        'performance-heading'       => 'Ringkasan Kinerja & Evaluasi:',
        'plans-count'               => 'Total Rencana Kinerja: :count',
        'evaluations-count'         => 'Total Evaluasi Kinerja: :count',
        'projects-summary'          => 'Ringkasan Proyek: :projects proyek aktif dengan :tasks tugas.',
        'employee-summary'          => 'Total Karyawan Aktif: :employees orang di :departments departemen.',
        'personal-heading'          => 'Data Profil & Kinerja Anda (:name):',
        'personal-today-attendance' => 'Absensi Anda hari ini: :in → :out:site',
        'personal-unlinked'         => 'Akun Anda hanya memiliki akses data pribadi, namun belum terhubung ke profil Karyawan. Hubungi HRD/Admin untuk menautkan akun pengguna Anda.',
    ],

    'help' => "Anda dapat bertanya tentang:\n• Absensi hari ini & lokasi kerja\n• Kontrak karyawan yang akan berakhir (≤ 30 hari) & tipe karyawan\n• Rencana kinerja & evaluasi kinerja\n• Ringkasan proyek\n• Atau ketik @ai di dalam pesan Chatter dokumen apa pun.",
];
