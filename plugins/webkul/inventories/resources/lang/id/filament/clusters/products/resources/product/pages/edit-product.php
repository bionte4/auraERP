<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Kesalahan saat memperbarui pelacakan',
                    'body'  => 'Anda tidak dapat mengubah pelacakan persediaan produk yang sudah digunakan.',
                ],

                'reordering-rules' => [
                    'title' => 'Kesalahan saat memperbarui produk',
                    'body'  => 'Anda masih memiliki aturan pemesanan ulang yang aktif pada produk ini. Silakan arsipkan atau hapus aturan tersebut terlebih dahulu.',
                ],

                'reserved' => [
                    'title' => 'Kesalahan saat memperbarui pelacakan',
                    'body'  => 'Anda tidak dapat mengubah pelacakan persediaan produk yang saat ini direservasi pada pergerakan stok. Jika Anda perlu mengubah pelacakan persediaan, batalkan reservasi pergerakan stok terlebih dahulu.',
                ],

                'qty-not-zero' => [
                    'title' => 'Kesalahan saat memperbarui pelacakan',
                    'body'  => 'Kuantitas tersedia harus diatur ke nol sebelum mengubah pelacakan persediaan.',
                ],

                'available-quantity' => [
                    'title' => 'Kesalahan saat memperbarui produk',
                    'body'  => 'Kuantitas tersedia harus diatur ke nol sebelum Anda dapat menandai produk ini sebagai tidak dapat disimpan.',
                ],

                'track-by-update' => [
                    'title' => 'Kesalahan saat memperbarui pelacakan',
                    'body'  => 'Anda memiliki produk dalam stok yang tidak memiliki nomor lot/seri. Anda dapat menetapkan nomor lot/seri dengan melakukan penyesuaian persediaan.',
                ],
            ],
        ],
    ],
];
