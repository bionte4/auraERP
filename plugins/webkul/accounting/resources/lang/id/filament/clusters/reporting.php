<?php

return [
    'navigation' => [
        'title' => 'Pelaporan',
    ],
    'common' => [
        'from-to'                  => ':report - Dari :from hingga :to',
        'expand-all'               => 'Perluas Semua',
        'collapse-all'             => 'Ciutkan Semua',
        'account'                  => 'Akun',
        'date'                     => 'Tanggal',
        'communication'            => 'Komunikasi',
        'partner'                  => 'Mitra',
        'journal'                  => 'Jurnal',
        'invoice-date'             => 'Tanggal Faktur',
        'due-date'                 => 'Tanggal Jatuh Tempo',
        'debit'                    => 'Debit',
        'credit'                   => 'Kredit',
        'balance'                  => 'Saldo',
        'total'                    => 'Total',
        'opening-balance'          => 'Saldo Pembukaan',
        'initial-balance'          => 'Saldo Awal',
        'end-balance'              => 'Saldo Akhir',
        'not-due'                  => 'Belum jatuh tempo',
        'no-data'                  => 'Tidak ada data yang tersedia',
        'no-accounts-transactions' => 'Tidak ada akun dengan transaksi pada periode ini',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Neraca',
                'group' => 'Laporan Pernyataan',
            ],
            'actions' => [
                'export-excel' => 'Ekspor ke Excel',
                'export-pdf'   => 'Ekspor ke PDF',
            ],
            'filters' => [
                'date-range' => 'Rentang Tanggal',
                'journals'   => 'Jurnal',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title'       => 'ASET',
                        'total-label' => 'Total ASET',
                        'subsections' => [
                            'current-assets' => [
                                'title'       => 'Aset Lancar',
                                'total-label' => 'Total Aset Lancar',
                            ],
                            'fixed-assets' => [
                                'title'       => 'Aset Tetap',
                                'total-label' => 'Total Aset Tetap',
                            ],
                            'non-current-assets' => [
                                'title'       => 'Aset Tidak Lancar',
                                'total-label' => 'Total Aset Tidak Lancar',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title'       => 'KEWAJIBAN',
                        'total-label' => 'Total KEWAJIBAN',
                        'subsections' => [
                            'current-liabilities' => [
                                'title'       => 'Kewajiban Lancar',
                                'total-label' => 'Total Liabilitas Lancar',
                            ],
                            'non-current-liabilities' => [
                                'title'       => 'Liabilitas Tidak Lancar',
                                'total-label' => 'Total Liabilitas Tidak Lancar',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title'       => 'EKUITAS',
                        'total-label' => 'Total EKUITAS',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title'          => 'Laba Belum Dialokasikan',
                                'current-year'   => 'Laba Belum Dialokasikan Tahun Berjalan',
                                'previous-years' => 'Laba Belum Dialokasikan Tahun-Tahun Sebelumnya',
                                'total-label'    => 'Total Laba Belum Dialokasikan',
                            ],
                            'retained-earnings' => [
                                'title'       => 'Laba Ditahan',
                                'total-label' => 'Total Laba Ditahan',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'KEWAJIBAN + EKUITAS',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Laba & Rugi',
                'group' => 'Laporan Pernyataan',
            ],
            'actions' => [
                'export-excel' => 'Ekspor ke Excel',
                'export-pdf'   => 'Ekspor ke PDF',
            ],
            'filters' => [
                'date-range' => 'Rentang Tanggal',
                'journals'   => 'Jurnal',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title'         => 'PENDAPATAN',
                        'total-label'   => 'Total Pendapatan',
                        'empty-message' => 'Tidak ada akun pendapatan yang memiliki transaksi pada periode ini',
                    ],
                    'expenses' => [
                        'title'         => 'BEBAN',
                        'total-label'   => 'Total Pengeluaran',
                        'empty-message' => 'Tidak ada akun beban yang memiliki transaksi pada periode ini',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'Buku Besar',
                'group' => 'Laporan Audit',
            ],
            'actions' => [
                'export-excel' => 'Ekspor ke Excel',
                'export-pdf'   => 'Ekspor ke PDF',
            ],
            'filters' => [
                'date-range' => 'Rentang Tanggal',
                'journals'   => 'Jurnal',
            ],
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Neraca Saldo',
                'group' => 'Laporan Audit',
            ],
            'actions' => [
                'export-excel' => 'Ekspor ke Excel',
                'export-pdf'   => 'Ekspor ke PDF',
            ],
            'filters' => [
                'date-range' => 'Rentang Tanggal',
                'journals'   => 'Jurnal',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Buku Besar Mitra',
                'group' => 'Laporan Mitra',
            ],
            'actions' => [
                'export-excel' => 'Ekspor Excel',
                'export-pdf'   => 'Ekspor PDF',
            ],
            'filters' => [
                'date-range' => 'Rentang Tanggal',
                'partners'   => 'Mitra',
                'journals'   => 'Jurnal',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Umur Piutang',
                'group' => 'Laporan Mitra',
            ],
            'actions' => [
                'export-excel' => 'Ekspor Excel',
                'export-pdf'   => 'Ekspor PDF',
            ],
            'filters' => [
                'as-of'         => 'Per Tanggal',
                'based-on'      => 'Berdasarkan',
                'period-length' => 'Lama Periode (hari)',
                'journals'      => 'Jurnal',
                'partners'      => 'Mitra',
                'entries'       => 'Entri',
                'options'       => [
                    'due-date'       => 'Tanggal Jatuh Tempo',
                    'invoice-date'   => 'Tanggal Faktur',
                    'days-30'        => '30 Hari',
                    'days-60'        => '60 Hari',
                    'days-90'        => '90 Hari',
                    'posted-entries' => 'Entri yang Diposting',
                    'all-entries'    => 'Semua Entri',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Utang Berdasarkan Umur',
                'group' => 'Laporan Mitra',
            ],
            'actions' => [
                'export-excel' => 'Ekspor Excel',
                'export-pdf'   => 'Ekspor PDF',
            ],
            'filters' => [
                'as-of'         => 'Per Tanggal',
                'based-on'      => 'Berdasarkan',
                'period-length' => 'Lama Periode (hari)',
                'journals'      => 'Jurnal',
                'partners'      => 'Mitra',
                'entries'       => 'Entri',
                'options'       => [
                    'due-date'       => 'Tanggal Jatuh Tempo',
                    'invoice-date'   => 'Tanggal Faktur',
                    'days-30'        => '30 Hari',
                    'days-60'        => '60 Hari',
                    'days-90'        => '90 Hari',
                    'posted-entries' => 'Entri yang Diposting',
                    'all-entries'    => 'Semua Entri',
                ],
            ],
        ],
    ],
];
