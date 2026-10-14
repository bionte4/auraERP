<?php

return [
    'title' => 'Batal',
    'modal' => [
        'heading'     => 'Batalkan Penawaran',
        'description' => 'Apakah Anda yakin ingin membatalkan penawaran ini?',
    ],

    'footer-actions' => [
        'send-and-cancel' => [
            'title' => 'Kirim & Batal',

            'notification' => [
                'cancelled' => [
                    'title' => 'Penawaran dibatalkan',
                    'body'  => 'Penawaran berhasil dibatalkan dan email berhasil dikirim.',
                ],
            ],
        ],

        'cancel' => [
            'title' => 'Batal',

            'notification' => [
                'cancelled' => [
                    'title' => 'Penawaran dibatalkan',
                    'body'  => 'Penawaran berhasil dibatalkan.',
                ],
            ],
        ],

        'close' => [
            'title' => 'Tutup',
        ],
    ],

    'form' => [
        'fields' => [
            'partner'             => 'Mitra',
            'subject'             => 'Subjek',
            'subject-placeholder' => 'Subjek',
            'subject-default'     => 'Penawaran :name telah dibatalkan untuk Pesanan Penjualan #:id',
            'description'         => 'Deskripsi',
            'description-default' => 'Yth. <b>:partner_name</b>, <br/><br/>Kami ingin memberi tahu Anda bahwa Pesanan Penjualan <b>:name</b> Anda telah dibatalkan. Dengan demikian, tidak ada biaya lebih lanjut yang dikenakan pada pesanan ini. Jika pengembalian dana diperlukan, pengembalian tersebut akan diproses secepatnya.<br/><br/>Jika Anda memiliki pertanyaan atau memerlukan bantuan lebih lanjut, jangan ragu untuk menghubungi kami.',
        ],
    ],
];
