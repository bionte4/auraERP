<?php

return [
    'title'      => 'Laporan Operasional Mingguan',
    'subtitle'   => 'Ringkasan actionable untuk Operation Manager & HRD: cuti, kontrak, tugas, dan celah penugasan.',
    'navigation' => [
        'label' => 'Laporan Ops Mingguan',
    ],
    'actions' => [
        'download' => 'Unduh Excel',
    ],
    'snapshot' => [
        'title' => 'Snapshot operasional',
    ],
    'table' => [
        'metric' => 'Metrik',
        'value'  => 'Nilai',
        'notes'  => 'Catatan',
    ],
    'load' => [
        'title'     => 'Beban tenaga kerja (minggu ini)',
        'subtitle'  => 'Karyawan ditugaskan dengan jam timesheet tertinggi minggu ini (sesuai cakupan Anda).',
        'employee'  => 'Karyawan',
        'projects'  => 'Proyek',
        'hours'     => 'Jam',
        'empty'     => 'Tidak ada karyawan ditugaskan pada cakupan Anda minggu ini.',
    ],
];
