<?php

return [
    'navigation' => [
        'title' => 'Bidang Kustom',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name'              => 'Nama',
                    'code'              => 'kode',
                    'code-helper-text'  => 'Kode harus diawali huruf atau garis bawah, dan hanya boleh berisi huruf, angka, dan garis bawah.',
                ],
            ],

            'options' => [
                'title' => 'Opsi',

                'fields' => [
                    'add-option' => 'Tambah Opsi',
                ],
            ],

            'form-settings' => [
                'title' => 'Pengaturan Formulir',

                'field-sets' => [
                    'validations' => [
                        'title' => 'Validasi',

                        'fields' => [
                            'validation'     => 'Validasi',
                            'field'          => 'Bidang',
                            'value'          => 'Nilai',
                            'add-validation' => 'Tambah Validasi',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'Pengaturan Tambahan',

                        'fields' => [
                            'setting'     => 'Pengaturan',
                            'value'       => 'Nilai',
                            'color'       => 'Warna',
                            'add-setting' => 'Tambah Pengaturan',

                            'color-options' => [
                                'danger'    => 'Bahaya',
                                'info'      => 'Info',
                                'primary'   => 'Utama',
                                'secondary' => 'Sekunder',
                                'warning'   => 'Peringatan',
                                'success'   => 'Berhasil',
                            ],

                            'grid-options' => [
                                'row'    => 'Baris',
                                'column' => 'Kolom',
                            ],

                            'input-modes' => [
                                'text'     => 'Teks',
                                'email'    => 'Email',
                                'numeric'  => 'Numerik',
                                'integer'  => 'Bilangan Bulat',
                                'password' => 'Kata Sandi',
                                'tel'      => 'Telepon',
                                'url'      => 'URL',
                                'color'    => 'Warna',
                                'none'     => 'Tidak Ada',
                                'decimal'  => 'Desimal',
                                'search'   => 'Cari',
                                'url'      => 'URL',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt'                   => 'Lebih Besar Dari',
                        'gte'                  => 'Lebih Besar Dari atau Sama Dengan',
                        'lt'                   => 'Kurang Dari',
                        'lte'                  => 'Kurang Dari atau Sama Dengan',
                        'max-size'             => 'Ukuran Maks',
                        'min-size'             => 'Ukuran Minimum',
                        'multiple-of'          => 'Kelipatan Dari',
                        'nullable'             => 'Boleh Kosong',
                        'prohibited'           => 'Dilarang',
                        'prohibited-if'        => 'Dilarang Jika',
                        'prohibited-unless'    => 'Dilarang Kecuali',
                        'prohibits'            => 'Melarang',
                        'required'             => 'Wajib',
                        'required-if'          => 'Wajib Jika',
                        'required-if-accepted' => 'Wajib Jika Diterima',
                        'required-unless'      => 'Wajib Kecuali',
                        'required-with'        => 'Wajib Jika Terisi',
                        'required-with-all'    => 'Wajib dengan Semua',
                        'required-without'     => 'Wajib Tanpa',
                        'required-without-all' => 'Wajib Tanpa Semua',
                        'rules'                => 'Aturan Kustom',
                        'unique'               => 'Unik',
                    ],

                    'text' => [
                        'alpha-dash'        => 'Alfa Dash',
                        'alpha-num'         => 'Alfanumerik',
                        'ascii'             => 'ASCII',
                        'doesnt-end-with'   => 'Tidak Berakhir Dengan',
                        'doesnt-start-with' => 'Tidak Diawali Dengan',
                        'ends-with'         => 'Berakhir Dengan',
                        'filled'            => 'Terisi',
                        'ip'                => 'IP',
                        'ipv4'              => 'IPv4',
                        'ipv6'              => 'IPv6',
                        'length'            => 'Panjang',
                        'mac-address'       => 'Alamat MAC',
                        'max-length'        => 'Panjang Maksimum',
                        'min-length'        => 'Panjang Minimum',
                        'regex'             => 'Regex',
                        'starts-with'       => 'Dimulai Dengan',
                        'ulid'              => 'ULID',
                        'uuid'              => 'UUID',
                    ],

                    'textarea' => [
                        'filled'     => 'Terisi',
                        'max-length' => 'Panjang Maksimum',
                        'min-length' => 'Panjang Minimum',
                    ],

                    'select' => [
                        'different'  => 'Berbeda',
                        'exists'     => 'Sudah Ada',
                        'in'         => 'Di Dalam',
                        'not-in'     => 'Tidak Di Dalam',
                        'same'       => 'Sama',
                    ],

                    'radio' => [],

                    'checkbox' => [
                        'accepted' => 'Diterima',
                        'declined' => 'Ditolak',
                    ],

                    'toggle' => [
                        'accepted' => 'Diterima',
                        'declined' => 'Ditolak',
                    ],

                    'checkbox-list' => [
                        'in'        => 'Di Dalam',
                        'max-items' => 'Item Maksimum',
                        'min-items' => 'Item Minimum',
                    ],

                    'datetime' => [
                        'after'           => 'Setelah',
                        'after-or-equal'  => 'Setelah atau Sama Dengan',
                        'before'          => 'Sebelum',
                        'before-or-equal' => 'Sebelum atau Sama Dengan',
                    ],

                    'editor' => [
                        'filled'     => 'Terisi',
                        'max-length' => 'Panjang Maksimum',
                        'min-length' => 'Panjang Minimum',
                    ],

                    'markdown' => [
                        'filled'     => 'Terisi',
                        'max-length' => 'Panjang Maksimum',
                        'min-length' => 'Panjang Minimum',
                    ],

                    'color' => [
                        'hex-color' => 'Warna Hex',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize'    => 'Kapitalisasi Otomatis',
                        'autocomplete'      => 'Isi Otomatis',
                        'autofocus'         => 'Fokus Otomatis',
                        'default'           => 'Nilai Bawaan',
                        'disabled'          => 'Dinonaktifkan',
                        'helper-text'       => 'Teks Bantuan',
                        'hint'              => 'Petunjuk',
                        'hint-color'        => 'Warna Petunjuk',
                        'hint-icon'         => 'Ikon Petunjuk',
                        'id'                => 'Id',
                        'input-mode'        => 'Mode Masukan',
                        'mask'              => 'Mask',
                        'placeholder'       => 'Teks Pengganti',
                        'prefix'            => 'Awalan',
                        'prefix-icon'       => 'Ikon Awalan',
                        'prefix-icon-color' => 'Warna Ikon Awalan',
                        'read-only'         => 'Hanya Baca',
                        'step'              => 'Langkah',
                        'suffix'            => 'Akhiran',
                        'suffix-icon'       => 'Ikon Akhiran',
                        'suffix-icon-color' => 'Warna Ikon Sufiks',
                    ],

                    'textarea' => [
                        'autofocus'     => 'Fokus Otomatis',
                        'autosize'      => 'Ukuran Otomatis',
                        'cols'          => 'Kolom',
                        'default'       => 'Nilai Bawaan',
                        'disabled'      => 'Dinonaktifkan',
                        'helper-text'   => 'Teks Bantuan',
                        'hint'          => 'Petunjuk',
                        'hint-color'    => 'Warna Petunjuk',
                        'hint-icon'     => 'Ikon Petunjuk',
                        'id'            => 'Id',
                        'placeholder'   => 'Teks Pengganti',
                        'read-only'     => 'Hanya Baca',
                        'rows'          => 'Baris',
                    ],

                    'select' => [
                        'default'                   => 'Nilai Bawaan',
                        'disabled'                  => 'Dinonaktifkan',
                        'helper-text'               => 'Teks Bantuan',
                        'hint'                      => 'Petunjuk',
                        'hint-color'                => 'Warna Petunjuk',
                        'hint-icon'                 => 'Ikon Petunjuk',
                        'id'                        => 'Id',
                        'loading-message'           => 'Pesan Pemuatan',
                        'no-search-results-message' => 'Pesan Tidak Ada Hasil Pencarian',
                        'options-limit'             => 'Batas Opsi',
                        'preload'                   => 'Pramuat',
                        'searchable'                => 'Dapat Dicari',
                        'search-debounce'           => 'Jeda Pencarian',
                        'searching-message'         => 'Pesan Pencarian',
                        'search-prompt'             => 'Prompt Pencarian',
                    ],

                    'radio' => [
                        'default'     => 'Nilai Bawaan',
                        'disabled'    => 'Dinonaktifkan',
                        'helper-text' => 'Teks Bantuan',
                        'hint'        => 'Petunjuk',
                        'hint-color'  => 'Warna Petunjuk',
                        'hint-icon'   => 'Ikon Petunjuk',
                        'id'          => 'Id',
                    ],

                    'checkbox' => [
                        'default'     => 'Nilai Bawaan',
                        'disabled'    => 'Dinonaktifkan',
                        'helper-text' => 'Teks Bantuan',
                        'hint'        => 'Petunjuk',
                        'hint-color'  => 'Warna Petunjuk',
                        'hint-icon'   => 'Ikon Petunjuk',
                        'id'          => 'Id',
                        'inline'      => 'Sebaris',
                    ],

                    'toggle' => [
                        'default'     => 'Nilai Bawaan',
                        'disabled'    => 'Dinonaktifkan',
                        'helper-text' => 'Teks Bantuan',
                        'hint'        => 'Petunjuk',
                        'hint-color'  => 'Warna Petunjuk',
                        'hint-icon'   => 'Ikon Petunjuk',
                        'id'          => 'Id',
                        'off-color'   => 'Warna Nonaktif',
                        'off-icon'    => 'Ikon Nonaktif',
                        'on-color'    => 'Warna Aktif',
                        'on-icon'     => 'Pada Ikon',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable'           => 'Dapat Dialihkan Massal',
                        'columns'                   => 'Kolom',
                        'default'                   => 'Nilai Bawaan',
                        'disabled'                  => 'Dinonaktifkan',
                        'grid-direction'            => 'Arah Kisi',
                        'helper-text'               => 'Teks Bantuan',
                        'hint'                      => 'Petunjuk',
                        'hint-color'                => 'Warna Petunjuk',
                        'hint-icon'                 => 'Ikon Petunjuk',
                        'id'                        => 'Id',
                        'max-items'                 => 'Item Maksimum',
                        'min-items'                 => 'Item Minimum',
                        'no-search-results-message' => 'Pesan Tidak Ada Hasil Pencarian',
                        'searchable'                => 'Dapat Dicari',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'Tutup saat Tanggal Dipilih',
                        'default'                 => 'Nilai Bawaan',
                        'disabled'                => 'Dinonaktifkan',
                        'disabled-dates'          => 'Tanggal Dinonaktifkan',
                        'display-format'          => 'Format Tampilan',
                        'first-day-of-week'       => 'Hari Pertama Minggu',
                        'format'                  => 'Format',
                        'helper-text'             => 'Teks Bantuan',
                        'hint'                    => 'Petunjuk',
                        'hint-color'              => 'Warna Petunjuk',
                        'hint-icon'               => 'Ikon Petunjuk',
                        'hours-step'              => 'Langkah Jam',
                        'id'                      => 'Id',
                        'locale'                  => 'Lokal',
                        'minutes-step'            => 'Langkah Menit',
                        'seconds'                 => 'Detik',
                        'seconds-step'            => 'Langkah Detik',
                        'timezone'                => 'Zona Waktu',
                        'week-starts-on-monday'   => 'Minggu Dimulai pada Hari Senin',
                        'week-starts-on-sunday'   => 'Minggu Dimulai pada Hari Minggu',
                    ],

                    'editor' => [
                        'default'      => 'Nilai Bawaan',
                        'disabled'     => 'Dinonaktifkan',
                        'helper-text'  => 'Teks Bantuan',
                        'hint'         => 'Petunjuk',
                        'hint-color'   => 'Warna Petunjuk',
                        'hint-icon'    => 'Ikon Petunjuk',
                        'id'           => 'Id',
                        'placeholder'  => 'Teks Pengganti',
                        'read-only'    => 'Hanya Baca',
                    ],

                    'markdown' => [
                        'default'      => 'Nilai Bawaan',
                        'disabled'     => 'Dinonaktifkan',
                        'helper-text'  => 'Teks Bantuan',
                        'hint'         => 'Petunjuk',
                        'hint-color'   => 'Warna Petunjuk',
                        'hint-icon'    => 'Ikon Petunjuk',
                        'id'           => 'Id',
                        'placeholder'  => 'Teks Pengganti',
                        'read-only'    => 'Hanya Baca',
                    ],

                    'color' => [
                        'default'     => 'Nilai Bawaan',
                        'disabled'    => 'Dinonaktifkan',
                        'helper-text' => 'Teks Bantuan',
                        'hint'        => 'Petunjuk',
                        'hint-color'  => 'Warna Petunjuk',
                        'hint-icon'   => 'Ikon Petunjuk',
                        'hsl'         => 'HSL',
                        'id'          => 'Id',
                        'rgb'         => 'RGB',
                        'rgba'        => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types'                   => 'Jenis Berkas yang Diterima',
                        'append-files'                          => 'Tambahkan Berkas',
                        'deletable'                             => 'Dapat Dihapus',
                        'directory'                             => 'Direktori',
                        'downloadable'                          => 'Dapat Diunduh',
                        'fetch-file-information'                => 'Ambil Informasi Berkas',
                        'file-attachments-directory'            => 'Direktori Lampiran Berkas',
                        'file-attachments-visibility'           => 'Visibilitas Lampiran Berkas',
                        'image'                                 => 'Gambar',
                        'image-crop-aspect-ratio'               => 'Rasio Aspek Pemotongan Gambar',
                        'image-editor'                          => 'Editor Gambar',
                        'image-editor-aspect-ratios'            => 'Rasio Aspek Editor Gambar',
                        'image-editor-empty-fill-color'         => 'Warna Isian Kosong Editor Gambar',
                        'image-editor-mode'                     => 'Mode Editor Gambar',
                        'image-preview-height'                  => 'Tinggi Pratinjau Gambar',
                        'image-resize-mode'                     => 'Mode Ubah Ukuran Gambar',
                        'image-resize-target-height'            => 'Tinggi Target Ubah Ukuran Gambar',
                        'image-resize-target-width'             => 'Lebar Target Ubah Ukuran Gambar',
                        'loading-indicator-position'            => 'Posisi Indikator Pemuatan',
                        'move-files'                            => 'Pindahkan Berkas',
                        'openable'                              => 'Dapat Dibuka',
                        'orient-images-from-exif'               => 'Orientasikan Gambar dari EXIF',
                        'panel-aspect-ratio'                    => 'Rasio Aspek Panel',
                        'panel-layout'                          => 'Tata Letak Panel',
                        'previewable'                           => 'Dapat Dipratinjau',
                        'remove-uploaded-file-button-position'  => 'Posisi tombol hapus berkas yang diunggah',
                        'reorderable'                           => 'Dapat Diurut Ulang',
                        'store-files'                           => 'Simpan Berkas',
                        'upload-button-position'                => 'Posisi Tombol Unggah',
                        'uploading-message'                     => 'Pesan Pengunggahan',
                        'upload-progress-indicator-position'    => 'Posisi Indikator Progres Unggah',
                        'visibility'                            => 'Visibilitas',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'Pengaturan Tabel',

                'fields' => [
                    'use-in-table'  => 'Gunakan di Tabel',
                    'setting'       => 'Pengaturan',
                    'value'         => 'Nilai',
                    'color'         => 'Warna',
                    'alignment'     => 'Perataan',
                    'font-weight'   => 'Ketebalan Huruf',
                    'icon-position' => 'Posisi Ikon',
                    'size'          => 'Ukuran',
                    'add-setting'   => 'Tambah Pengaturan',

                    'color-options' => [
                        'danger'    => 'Bahaya',
                        'info'      => 'Info',
                        'primary'   => 'Utama',
                        'secondary' => 'Sekunder',
                        'warning'   => 'Peringatan',
                        'success'   => 'Berhasil',
                    ],

                    'alignment-options' => [
                        'start'   => 'Mulai',
                        'left'    => 'Kiri',
                        'center'  => 'Tengah',
                        'end'     => 'Akhir',
                        'right'   => 'Kanan',
                        'justify' => 'Rata Penuh',
                        'between' => 'Antara',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Tipis',
                        'extra-light' => 'Ekstra Tipis',
                        'light'       => 'Ringan',
                        'normal'      => 'Normal',
                        'medium'      => 'Sedang',
                        'semi-bold'   => 'Semi Tebal',
                        'bold'        => 'Tebal',
                        'extra-bold'  => 'Ekstra Tebal',
                        'black'       => 'Hitam',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Sebelum',
                        'after'   => 'Setelah',
                    ],

                    'size-options' => [
                        'extra-small' => 'Ekstra Kecil',
                        'small'       => 'Kecil',
                        'medium'      => 'Sedang',
                        'large'       => 'Besar',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Rata Akhir',
                        'alignment'              => 'Perataan',
                        'align-start'            => 'Rata Awal',
                        'badge'                  => 'Lencana',
                        'boolean'                => 'Boolean',
                        'color'                  => 'Warna',
                        'copyable'               => 'Dapat Disalin',
                        'copy-message'           => 'Salin Pesan',
                        'copy-message-duration'  => 'Durasi Pesan Salin',
                        'default'                => 'Bawaan',
                        'filterable'             => 'Dapat Difilter',
                        'groupable'              => 'Dapat Dikelompokkan',
                        'grow'                   => 'Mengembang',
                        'icon'                   => 'Ikon',
                        'icon-color'             => 'Warna Ikon',
                        'icon-position'          => 'Posisi Ikon',
                        'label'                  => 'Label',
                        'limit'                  => 'Batas',
                        'line-clamp'             => 'Batas Baris',
                        'money'                  => 'Uang',
                        'placeholder'            => 'Teks Pengganti',
                        'prefix'                 => 'Awalan',
                        'searchable'             => 'Dapat Dicari',
                        'size'                   => 'Ukuran',
                        'sortable'               => 'Dapat Diurutkan',
                        'suffix'                 => 'Akhiran',
                        'toggleable'             => 'Dapat Dialihkan',
                        'tooltip'                => 'Tooltip',
                        'vertical-alignment'     => 'Perataan Vertikal',
                        'vertically-align-start' => 'Rata Vertikal Awal',
                        'weight'                 => 'Berat',
                        'width'                  => 'Lebar',
                        'words'                  => 'Kata',
                        'wrap-header'            => 'Bungkus Tajuk',
                        'column-span'            => 'Rentang Kolom',
                        'helper-text'            => 'Teks Bantuan',
                        'hint'                   => 'Petunjuk',
                        'hint-color'             => 'Warna Petunjuk',
                        'hint-icon'              => 'Ikon Petunjuk',
                    ],

                    'datetime' => [
                        'date'              => 'Tanggal',
                        'date-time'         => 'Tanggal Waktu',
                        'date-time-tooltip' => 'Tooltip Tanggal Waktu',
                        'since'             => 'Sejak',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => 'Pengaturan Infolist',

                'fields' => [
                    'setting'       => 'Pengaturan',
                    'value'         => 'Nilai',
                    'color'         => 'Warna',
                    'font-weight'   => 'Ketebalan Huruf',
                    'icon-position' => 'Posisi Ikon',
                    'size'          => 'Ukuran',
                    'add-setting'   => 'Tambah Pengaturan',

                    'color-options' => [
                        'danger'    => 'Bahaya',
                        'info'      => 'Info',
                        'primary'   => 'Utama',
                        'secondary' => 'Sekunder',
                        'warning'   => 'Peringatan',
                        'success'   => 'Berhasil',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Tipis',
                        'extra-light' => 'Ekstra Tipis',
                        'light'       => 'Ringan',
                        'normal'      => 'Normal',
                        'medium'      => 'Sedang',
                        'semi-bold'   => 'Semi Tebal',
                        'bold'        => 'Tebal',
                        'extra-bold'  => 'Ekstra Tebal',
                        'black'       => 'Hitam',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Sebelum',
                        'after'   => 'Setelah',
                    ],

                    'size-options' => [
                        'extra-small' => 'Ekstra Kecil',
                        'small'       => 'Kecil',
                        'medium'      => 'Sedang',
                        'large'       => 'Besar',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Rata Akhir',
                        'alignment'              => 'Perataan',
                        'align-start'            => 'Rata Awal',
                        'badge'                  => 'Lencana',
                        'boolean'                => 'Boolean',
                        'color'                  => 'Warna',
                        'copyable'               => 'Dapat Disalin',
                        'copy-message'           => 'Salin Pesan',
                        'copy-message-duration'  => 'Durasi Pesan Salin',
                        'default'                => 'Bawaan',
                        'filterable'             => 'Dapat Difilter',
                        'groupable'              => 'Dapat Dikelompokkan',
                        'grow'                   => 'Mengembang',
                        'icon'                   => 'Ikon',
                        'icon-color'             => 'Warna Ikon',
                        'icon-position'          => 'Posisi Ikon',
                        'label'                  => 'Label',
                        'limit'                  => 'Batas',
                        'line-clamp'             => 'Batas Baris',
                        'money'                  => 'Uang',
                        'placeholder'            => 'Teks Pengganti',
                        'prefix'                 => 'Awalan',
                        'searchable'             => 'Dapat Dicari',
                        'size'                   => 'Ukuran',
                        'sortable'               => 'Dapat Diurutkan',
                        'suffix'                 => 'Akhiran',
                        'toggleable'             => 'Dapat Dialihkan',
                        'tooltip'                => 'Tooltip',
                        'vertical-alignment'     => 'Perataan Vertikal',
                        'vertically-align-start' => 'Rata Vertikal Awal',
                        'weight'                 => 'Berat',
                        'width'                  => 'Lebar',
                        'words'                  => 'Kata',
                        'wrap-header'            => 'Bungkus Tajuk',
                        'column-span'            => 'Rentang Kolom',
                        'helper-text'            => 'Teks Bantuan',
                        'hint'                   => 'Petunjuk',
                        'hint-color'             => 'Warna Petunjuk',
                        'hint-icon'              => 'Ikon Petunjuk',
                    ],

                    'datetime' => [
                        'date'              => 'Tanggal',
                        'date-time'         => 'Tanggal Waktu',
                        'date-time-tooltip' => 'Tooltip Tanggal Waktu',
                        'since'             => 'Sejak',
                    ],

                    'checkbox-list' => [
                        'separator'                => 'Pemisah',
                        'list-with-line-breaks'    => 'Daftar dengan Pemisah Baris',
                        'bulleted'                 => 'Berpoin',
                        'limit-list'               => 'Batasi Daftar',
                        'expandable-limited-list'  => 'Daftar Terbatas yang Dapat Diperluas',
                    ],

                    'select' => [
                        'separator'                => 'Pemisah',
                        'list-with-line-breaks'    => 'Daftar dengan Pemisah Baris',
                        'bulleted'                 => 'Berpoin',
                        'limit-list'               => 'Batasi Daftar',
                        'expandable-limited-list'  => 'Daftar Terbatas yang Dapat Diperluas',
                    ],

                    'checkbox' => [
                        'boolean'     => 'Boolean',
                        'false-icon'  => 'Ikon Salah',
                        'true-icon'   => 'Ikon Benar',
                        'true-color'  => 'Warna Benar',
                        'false-color' => 'Warna Salah',
                    ],

                    'toggle' => [
                        'boolean'     => 'Boolean',
                        'false-icon'  => 'Ikon Salah',
                        'true-icon'   => 'Ikon Benar',
                        'true-color'  => 'Warna Benar',
                        'false-color' => 'Warna Salah',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Pengaturan',

                'fields' => [
                    'type'           => 'Tipe',
                    'input-type'     => 'Jenis Masukan',
                    'is-multiselect' => 'Pilihan Banyak',
                    'sort-order'     => 'Urutan Sortir',

                    'type-options' => [
                        'text'          => 'Masukan Teks',
                        'textarea'      => 'Area Teks',
                        'select'        => 'Pilih',
                        'checkbox'      => 'Kotak Centang',
                        'radio'         => 'Radio',
                        'toggle'        => 'Alihkan',
                        'checkbox-list' => 'Daftar Kotak Centang',
                        'datetime'      => 'Pemilih Tanggal Waktu',
                        'editor'        => 'Editor Teks Kaya',
                        'markdown'      => 'Editor Markdown',
                        'color'         => 'Pemilih Warna',
                    ],

                    'input-type-options' => [
                        'text'     => 'Teks',
                        'email'    => 'Email',
                        'numeric'  => 'Numerik',
                        'integer'  => 'Bilangan Bulat',
                        'password' => 'Kata Sandi',
                        'tel'      => 'Telepon',
                        'url'      => 'URL',
                        'color'    => 'Warna',
                    ],
                ],
            ],

            'resource' => [
                'title' => 'Sumber Daya',

                'fields' => [
                    'plugin'   => 'Plugin',
                    'resource' => 'Sumber Daya',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Kode',
            'name'       => 'Nama',
            'type'       => 'Tipe',
            'resource'   => 'Sumber Daya',
            'created-at' => 'Dibuat Pada',
        ],

        'groups' => [
        ],

        'filters' => [
            'type'     => 'Tipe',
            'resource' => 'Sumber Daya',

            'type-options' => [
                'text'          => 'Masukan Teks',
                'textarea'      => 'Area Teks',
                'select'        => 'Pilih',
                'checkbox'      => 'Kotak Centang',
                'radio'         => 'Radio',
                'toggle'        => 'Alihkan',
                'checkbox-list' => 'Daftar Kotak Centang',
                'datetime'      => 'Pemilih Tanggal Waktu',
                'editor'        => 'Editor Teks Kaya',
                'markdown'      => 'Editor Markdown',
                'color'         => 'Pemilih Warna',
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bidang dipulihkan',
                    'body'  => 'Bidang berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bidang dihapus',
                    'body'  => 'Bidang berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Bidang dihapus paksa',
                    'body'  => 'Bidang berhasil dihapus secara paksa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bidang dipulihkan',
                    'body'  => 'Bidang berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bidang dihapus',
                    'body'  => 'Bidang berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Bidang dihapus paksa',
                    'body'  => 'Bidang berhasil dihapus secara paksa.',
                ],
            ],
        ],
    ],
];
