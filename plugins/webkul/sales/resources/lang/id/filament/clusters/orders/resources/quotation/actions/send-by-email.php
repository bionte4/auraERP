<?php

return [
    'title'        => 'Kirim melalui Email',
    'resend-title' => 'Kirim Ulang melalui Email',
    'quotation'    => 'penawaran',
    'quotations'   => 'penawaran',

    'modal' => [
        'heading' => 'Kirim Penawaran melalui Email',
    ],

    'form' => [
        'fields' => [
            'partners'    => 'Mitra',
            'subject'     => 'Subjek',
            'description' => 'Deskripsi',
            'attachment'  => 'Lampiran',
        ],
    ],

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'Tidak Ada Penerima yang Dipilih',
                    'body'  => 'Silakan pilih setidaknya satu mitra untuk dikirimi penawaran.',
                ],

                'all_success' => [
                    'title' => 'Penawaran Terkirim!',
                    'body'  => ':plural Anda berhasil dikirim ke: :recipients',
                ],

                'all_failed' => [
                    'title' => 'Tidak Dapat Mengirim Penawaran',
                    'body'  => 'Kami mengalami masalah saat mengirim penawaran Anda: :failures',
                ],

                'partial_success' => [
                    'title'       => 'Beberapa Penawaran Terkirim',
                    'sent_part'   => 'Berhasil dikirim ke: :recipients',
                    'failed_part' => 'Tidak dapat mengirim ke: :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],

];
