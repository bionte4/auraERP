<?php

return [
    'title'      => 'Laporan Mingguan Proyek Pelanggan',
    'subtitle'   => 'Laporan progres proyek siap dikirim ke pelanggan. Judul tugas dan tekanan jadwal disamarkan; jam timesheet tidak ditampilkan.',
    'navigation' => [
        'label' => 'Laporan Proyek Pelanggan',
    ],
    'form' => [
        'section' => 'Pilih proyek',
    ],
    'fields' => [
        'project'       => 'Proyek',
        'notes'         => 'Catatan untuk pelanggan',
        'notes-helper'  => 'Opsional; ikut di sheet Cover pada file Excel.',
    ],
    'actions' => [
        'preview'  => 'Pratinjau laporan',
        'download' => 'Unduh Excel',
    ],
    'notifications' => [
        'missing-project'  => 'Pilih proyek terlebih dahulu',
        'forbidden'        => 'Anda tidak dapat membuat laporan untuk proyek ini',
        'preview-ready'    => 'Pratinjau siap — periksa di bawah, lalu unduh Excel',
        'preview-required' => 'Pratinjau laporan dulu, baru unduh Excel',
    ],
    'table' => [
        'metric' => 'Metrik',
        'value'  => 'Nilai',
        'notes'  => 'Catatan',
        'item'   => 'Item',
        'status' => 'Status',
        'date'   => 'Tanggal',
    ],
    'preview' => [
        'cover'          => 'Pratinjau laporan',
        'notes'          => 'Catatan untuk pelanggan',
        'completed'      => 'Selesai minggu ini (disamarkan)',
        'open'           => 'Sedang berjalan (disamarkan)',
        'empty'          => 'Tidak ada item pada bagian ini.',
        'masked-hint'    => 'Nama tugas asli dan kata “terlambat” disembunyikan agar aman dikirim ke pelanggan.',
        'select-project' => 'Pilih proyek, klik Pratinjau laporan, periksa isinya, lalu Unduh Excel.',
    ],
];
