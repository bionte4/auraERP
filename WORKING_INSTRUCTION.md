# WORKING INSTRUCTION (WI) — AURA ERP

Dokumen ini berisi panduan operasional lengkap untuk seluruh modul kustom, sistem hak akses berjenjang (**RBAC & Struktur Organisasi**), **Evaluasi Kinerja PKWT & Outtask/Outsource**, **Dashboard Evaluasi**, **Point of Sale (POS)**, **Manajemen Proyek & Penugasan Karyawan**, **Integrasi (SMTP / auraAI / Jira)**, **Manajemen Aset**, serta **auraAI** di **auraERP** (`https://opsora.my.id` / `https://auraerp.my.id`).

---

## 1. Akun Login, Peran Standar (RBAC) & Level Akses Struktur Organisasi

Halaman login Admin Panel: **`/admin/login`**

### A. 5 Role Standar Sistem (`php artisan security:sync-rbac`)

| Role | Hak Akses Utama |
| :--- | :--- |
| **Admin** | Akses penuh ke seluruh modul, pengaturan perusahaan, manajemen User, Role & Permission, **Audit Trail**, **Backup Database**, **IP Diblokir** (khusus Admin), dan instalasi plugin. |
| **HRD** | Mengelola Karyawan, Kontrak, Departemen, Lokasi Kerja (Geofence), Rekap Absensi, Cuti/Izin (`Time Off`), Rencana Kinerja (`Performance Plans`), Evaluasi Kinerja (`Evaluations`), Dashboard Evaluasi + Export Excel/PDF, Rekrutmen, dan Aset Karyawan. |
| **Operation Manager** | Mengelola Proyek (`Projects`), **Tugaskan Karyawan ke Proyek**, Tugas (`Tasks`), `Timesheets`, Gudang & Stok (`Inventory` & `Products`), Perawatan & Aset (`Maintenance` & `Equipment`), Pembelian (`Purchases`), Penjualan (`Sales`), Mitra (`Partners`), serta melakukan **Penilaian Kinerja (L1/L2)** dan **Approval Cuti/Absensi** untuk tim/bawahannya. |
| **Kasir** | Mengoperasikan **POS Terminal** (`/admin/pos-terminal`), membuka/menutup sesi shift kasir (`POS Sessions`), membuat transaksi penjualan kasir (`POS Orders`), melihat katalog produk & pelanggan, serta melakukan absensi (`/admin/absen`). |
| **Staff (Karyawan)** | Self-service karyawan: melakukan Absensi (`/admin/absen`), mengajukan & memantau Cuti/Izin pribadi (`My Time Off` & `My Allocations`), mengisi `Timesheet` & tugas proyek, melihat `Performance Plan` & mengonfirmasi hasil `Evaluation` miliknya sendiri, serta menggunakan `auraAI` untuk data pribadinya. |

### B. 3 Tingkatan Scope Data (`Resource Permission` pada Menu Settings → Users)
Saat membuat atau mengedit User di **Settings → Users**, terdapat pengaturan **Resource Permission**:
1. **`Global`**: User dapat melihat dan mengelola seluruh data di perusahaan sesuai izin modulnya (cocok untuk Direksi, HRD Pusat, Admin).
2. **`Group` (Tim & Hirarki Struktur Organisasi)**: User otomatis dapat mengakses data milik:
   - Dirinya sendiri,
   - Rekan satu tim (`Teams`), **DAN**
   - **Seluruh bawahan langsung (Level 1) maupun bawahan tidak langsung (Level 2 dst.)** berdasarkan pohon struktur organisasi karyawan (`Manager / parent_id` serta `Department Manager`).
3. **`Individual`**: User hanya dapat melihat dan mengelola data miliknya sendiri (*Self-Service*).

### C. Mengelola Anggota Tim (`Settings → Teams`) & Papan Manage Teams
1. Buka **Settings → Teams** (`/admin/teams`) → **New team** / **Edit**.
2. Isi **Nama** (disarankan **sama dengan nama Proyek**), lalu simpan. Anggota bisa dipilih manual, atau terisi otomatis (lihat poin 4).
3. Alternatif manual: **Settings → Users** → edit user → bagian Teams (Resource Permission **Group**).
4. **Sinkron otomatis anggota Tim** (nama Tim = nama Proyek, case-insensitive):
   - **Manajer Proyek** + karyawan yang ditugaskan ke proyek (yang punya Related User) otomatis masuk anggota Tim.
   - Dipicu saat simpan proyek, Assign Employees, atau simpan Tim.
   - Backfill sekali setelah deploy: `sudo -u www-data php artisan projects:sync-team-memberships`.
5. Papan **Dashboard → Team Board / Manage Teams** menampilkan:
   - **Anggota**: Manajer Proyek + karyawan yang di-assign ke proyek (nama Tim = nama Proyek).
   - **Total N anggota** dapat diklik → membuka daftar karyawan terlibat (peran + nama proyek).
   - **Daftar proyek yang dapat diklik** menuju detail masing-masing proyek.

### D. Audit Trail (khusus Admin — Security Access)
1. Menu **Settings → Audit Trail** (`/admin/audit-trail`) **hanya tampil dan dapat dibuka oleh role Admin** (hard-check role + policy; HRD/OM/Kasir/Staff ditolak).
2. Mencatat peristiwa akses keamanan: login berhasil/gagal, logout, perubahan user (email/status/password), perubahan peran user, serta CRUD role.
3. Setiap entri menampilkan waktu, pelaku, objek, IP, guard, dan detail tambahan. **CRUD dinonaktifkan** (hanya lihat; create/update/delete/force-delete = false).
4. Setelah deploy: pastikan migrasi jalan (`php artisan migrate`) lalu `sudo -u www-data php artisan security:sync-rbac` (sync juga **mencabut** permission audit/backup dari role non-Admin).

### E. Backup Database Berkala & Restore (khusus Admin)
1. Menu **Settings → Backup Database** (`/admin/database-backups`) **hanya untuk role Admin** (termasuk unduh file via route auth).
2. Backup otomatis harian (default pukul **02:00** server) via scheduler; pembersihan backup lama default pukul **03:15**.
3. Dari UI Admin dapat: **buat backup sekarang**, **unduh**, **restore** (konfirmasi ketik `RESTORE`), **hapus**, dan **bersihkan backup lama**.
4. File disimpan di `storage/app/private/database-backups` (bisa diubah lewat `SECURITY_BACKUP_PATH`).
5. Perintah CLI:
   - `sudo -u www-data php artisan security:database-backup`
   - `sudo -u www-data php artisan security:database-backup-clean`
6. Pastikan cron scheduler aktif:
   `* * * * * cd /var/www/auraerp && sudo -u www-data php artisan schedule:run >> /dev/null 2>&1`
7. Env berguna: `SECURITY_BACKUP_ENABLED`, `SECURITY_BACKUP_SCHEDULE_AT`, `SECURITY_BACKUP_RETENTION_DAYS`, `SECURITY_BACKUP_RETENTION_COUNT`.
8. **Peringatan:** Restore menimpa database aktif. Di **production**, restore dari UI/CLI **default dinonaktifkan** (unduh saja, restore di staging). Override: `SECURITY_BACKUP_ALLOW_RESTORE=true`.
9. Cek kesehatan backup: `sudo -u www-data php artisan security:health-check`.

### F. Rate Limit Login & Blokir IP
1. Login Admin Panel (`/admin/login`) dan API login dilindungi:
   - **Rate limit:** default **5** percobaan gagal per kombinasi email+IP dalam **1** menit.
   - **Blokir IP:** setelah **10** kegagalan dari IP yang sama dalam **15** menit, IP diblokir **30** menit.
2. Menu **Settings → IP Diblokir** (`/admin/blocked-ips`) **khusus Admin** — lihat daftar IP aktif + **Buka blokir**.
3. Whitelist default: `127.0.0.1`, `::1` (tidak pernah diblokir). Sesuaikan via env.
4. Env berguna:
   - `SECURITY_LOGIN_PROTECTION_ENABLED` (default `true`)
   - `SECURITY_LOGIN_MAX_ATTEMPTS` (default `5`)
   - `SECURITY_LOGIN_DECAY_MINUTES` (default `1`)
   - `SECURITY_LOGIN_BLOCK_AFTER_FAILURES` (default `10`)
   - `SECURITY_LOGIN_FAILURE_WINDOW_MINUTES` (default `15`)
   - `SECURITY_LOGIN_BLOCK_MINUTES` (default `30`)
   - `SECURITY_LOGIN_IP_WHITELIST` (comma-separated, default `127.0.0.1,::1`)
5. Setelah deploy: `php artisan migrate` (tabel `security_blocked_ips`) lalu `security:sync-rbac`.

### G. Deploy dari Fork (jika `origin` 403)
VPS sering menarik dari fork `bionte4/auraERP` karena akses write ke `aureuserp/aureuserp` terbatas:
```bash
cd /var/www/auraerp
sudo -u www-data git remote add fork https://github.com/bionte4/auraERP.git 2>/dev/null || true
sudo -u www-data git fetch fork && sudo -u www-data git merge fork/master
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan security:sync-rbac
sudo -u www-data php artisan optimize:clear
```

### H. Akun Demo
- Demo: `hrd@opsora.my.id` / `Demo-hrd-2026`, `karyawan@opsora.my.id` / `Demo-karyawan-2026`.
- Untuk go-live tanpa demo: set `SECURITY_DEMO_ACCOUNTS_ALLOWED=false` di `.env` (login demo ditolak).
- Sync RBAC kapan saja: `sudo -u www-data php artisan security:sync-rbac`.

*(Untuk menyinkronkan ulang seluruh Role & Permission kapan saja: `sudo -u www-data php artisan security:sync-rbac`)*

---

## 2. Pengelolaan Karyawan, Struktur Organisasi, Alert Kontrak (≤ 30 Hari) & Bulk Import

### A. Mengatur Departemen & Hirarki Atasan Berjenjang
1. Buka menu **Employees → Configurations → Departments** (`/admin/employees/departments`) untuk mengatur *Manager* Kepala Departemen dan *Parent Department*.
2. Buka menu **Employees → Employees** (`/admin/employees/employees`), pilih karyawan, lalu pada bagian **Work Information**:
   - **Manager (`Atasan Langsung / Level 1`)**: Pilih atasan langsung karyawan tersebut.
   - Sistem secara otomatis mengenali **Atasan dari Manager** (atau Kepala Departemen) sebagai **Approver Level 2** untuk keperluan persetujuan cuti berjenjang dan evaluasi kinerja.

### B. Mengisi Tipe Karyawan & Masa Kontrak
1. Masuk ke tab **Work Information (Informasi Kerja)** → bagian **Organization Details (Detail Organisasi)**:
   - **Employment Type (Tipe Karyawan):** Pilih salah satu (`Permanent`, `Contract` / PKWT, `Konsultan`, `Freelance`, atau `Outsource / Outtask`).
   - **Start Join Date (Tanggal Mulai Bergabung):** Pilih tanggal mulai bekerja.
   - **End Contract Date (Tanggal Berakhir Kontrak):** Isi tanggal berakhir kontrak (kosongkan untuk *Permanent*).
2. Klik **Save Changes**.

### C. Bulk Import Karyawan (Excel `.xlsx` / CSV)
1. Buka menu **Employees → Employees** (`/admin/employees/employees`).
2. Klik tombol **`Unduh Template` (`Download Template`)** di pojok kanan atas untuk mengunduh file `template-import-employees.xlsx`.
3. Isi kolom: `name`, `work_email`, `work_phone`, `mobile_phone`, `job_title`, `department`, `employment_type`, `join_date`, `contract_end_date`, `identification_id`.
4. Klik tombol **`Impor Excel / CSV` (`Import Excel / CSV`)**, unggah file yang sudah diisi, lalu klik **Submit**.

### D. Menugaskan Proyek dari Profil Karyawan (`Assigned Projects`)
1. Buka **Employees → Employees** → pilih karyawan → pastikan **Pengguna Terkait (`Related User`)** sudah diisi bila karyawan perlu login/akses proyek.
2. Buka tab **Proyek Ditugaskan (`Assigned Projects`)** (`/admin/employees/employees/{id}/assigned-projects`).
3. Pilih satu atau beberapa **Proyek**, lalu klik **Simpan Penugasan**.
4. Penugasan ini tersinkron dengan layar **Project → Tugaskan Karyawan** (data & followers yang sama). Lihat juga **§7**.

### E. Memantau Kontrak yang Akan Habis (Alert 30 Hari) & Approval Cuti Berjenjang (SLA 24 Jam)
- **Tab Filter Cepat Kontrak:** Klik tab **`Kontrak Segera Berakhir` (`Expiring Contracts`)** di daftar **Employees** untuk melihat karyawan dengan sisa kontrak `≤ 30 hari`.
- **Approval Cuti/Izin Berjenjang & Eskalasi Otomatis 24 Jam (`Time Off`):**
  - Saat karyawan mengajukan izin/cuti di **Time Off → My Time Off**, sistem otomatis menetapkan **Approver Level 1 (Atasan Langsung)** dan **Approver Level 2 (Atasan dari Atasan / Kepala Dept)**.
  - Apabila dalam **24 jam** Approver Level 1 belum memberikan persetujuan, scheduler (`php artisan time-off:escalate-pending-approvals`) otomatis mengeskalasi pengajuan tersebut ke **Approver Level 2 & HRD**.

---

## 3. Absensi Geofence Multi-Lokasi & Kamera (HP vs Laptop)

### A. Penempatan Karyawan (Peta Welcome) vs Lokasi Kerja Absensi
Dua master data terpisah:
1. **Work Location / Penempatan** (`Employees → Configurations → Work Locations`, `/admin/employees/configurations/work-locations`):
   - Isi **Nama**, **Kota**, **Latitude & Longitude** (opsional tapi wajib agar muncul di peta Welcome).
   - Di form karyawan (**Work Information → Work Location**), pilih penempatan tersebut.
   - Peta **Welcome → Penempatan Karyawan** menghitung jumlah karyawan aktif per Work Location (bukan clock-in absensi).
2. **Lokasi kerja absensi / Work sites** (`Employees → Attendance ▾ → Lokasi kerja`, `/admin/employees/attendance-sites`):
   - Khusus geofence **Clock In / Clock Out** (lat/lng + radius). Tidak dipakai sebagai sumber peta Welcome.

### B. Mendaftarkan Lokasi Kerja Absensi (Oleh HRD / Admin)
1. Buka menu **Employees → Attendance ▾ → Lokasi kerja (`Work sites`)** (`/admin/employees/attendance-sites`).
2. Klik **Lokasi kerja baru (`New work site`)**, isi **Nama lokasi**, **Latitude & Longitude** (bisa klik **Gunakan posisi GPS saat ini**), dan **Radius diizinkan (meter)** (`20` s/d `5000` meter).
3. Pastikan toggle **Aktif** menyala, lalu klik **Save**.

### C. Cara Karyawan Melakukan Clock In / Clock Out
1. Buka menu **Employees → Attendance ▾ → Absen (`Clock`)** (`/admin/absen`).
2. Izinkan akses **Lokasi (GPS)** pada browser. Sistem otomatis menghitung jarak ke lokasi kerja aktif terdekat.
3. **Aturan Kamera Otomatis Berdasarkan Perangkat:**
   - **HP / Smartphone / Tablet:** Kamera depan otomatis menyala untuk verifikasi wajah (*Face Matching*) dan foto selfie sebelum tombol **Clock In / Clock Out** aktif.
   - **Laptop / PC / Netbook:** Kamera **tidak dibuka**; karyawan cukup berada di dalam radius GPS lokasi kerja untuk langsung menekan tombol **Clock In / Clock Out**.

---

## 4. Goal Setting (KPI), Evaluasi Kinerja Triwulan PKWT & Outsource/Outtask, serta Dashboard Evaluasi

### A. Rencana Kinerja / Goal Setting (`Performance Plans`)
1. Buka menu **Employees → Performance Plans** (`/admin/performance/plans`).
2. Buat program Goal Setting karyawan, tentukan periode mulai & selesai, lalu tambahkan butir-butir **Goals / Sasaran** beserta **Weight (Bobot %)** dan **Progress (%)**.
3. Sistem menghitung **Skor Tertimbang (%)** secara otomatis yang langsung terhubung ke form Evaluasi Kinerja.

### B. Formulir Evaluasi Kinerja Karyawan (`Evaluations`)
Buka menu **Employees → Performance Plans → Evaluations** (`/admin/evaluations/evaluations`) lalu klik **New Evaluation**:

1. **Bagian A & B — Identitas Evaluasi & Data Karyawan (Layout 2 Kolom Simetris & Status Otomatis):**
   - **Baris 1:** `Judul Evaluasi` (Kiri) & `Jenis / Periode Evaluasi` (Kanan: `Triwulan I PKWT`, `Triwulan II PKWT`, `Triwulan III PKWT`, `Triwulan IV PKWT`, `Penilaian Akhir PKWT`, `Evaluasi Karyawan Outtask / Outsource`, atau `Evaluasi Tahunan / Reguler`).
   - **Baris 2:** `Periode Mulai (Period Start)` (Kiri) & `Periode Selesai (Period End)` (Kanan) — berdampingan sejajar.
   - **Baris 3:** `Karyawan yang Dinilai (Employee)` (Kiri) & `NIK Karyawan` (Kanan).
   - **Baris 4:** `Approver Level 1: Penilai (Atasan Langsung / Pembimbing)` (Kiri) & `Approver Level 2: Atasan Penilai / Kepala Departemen (Berjenjang)` (Kanan).
   - Saat Anda memilih **Karyawan**, sistem **otomatis mengisi** `NIK`, `Tanggal Masuk`, `Jabatan`, `Departemen`, `Lokasi Kerja`, serta **`Approver Level 1`** dan **`Approver Level 2`** langsung dari pohon struktur organisasi.
   - **Status Otomatis `In Progress`:** Penilai tidak perlu memilih status `Draft`. Begitu evaluasi dibuat (**Create**) atau disimpan (**Save**), status otomatis menjadi **`In Progress`** dan akan berubah menjadi **`Completed`** secara otomatis setelah seluruh tanda tangan berjenjang lengkap.

2. **Bagian C — Pencapaian Goal Karyawan (Khusus Non-Outsource / PKWT):**
   - Pilih **Link Program Goal Setting**, maka **Rata-rata Pencapaian Goal (%)** terisi otomatis (Standar kelulusan pengangkatan tetap: **minimal 95%**).
   - **Aturan Otomatis Karyawan Outsource / Outtask:** Apabila karyawan yang dinilai berstatus **Outsource / Outtask** (atau jenis evaluasi/rekomendasi dipilih untuk *Outtask / Outsource*), **Bagian C (Pencapaian Goal Karyawan) otomatis disembunyikan/dihilangkan** dari form, halaman detail, dan cetakan PDF.

3. **Bagian D — Manajemen Pekerjaan (Poin 1 – 10) & Bar Total/Rata-rata Live:**
   - Form langsung menampilkan **10 Aspek Penilaian Manajemen Pekerjaan standar** (Kualitas Hasil Kerja, Kuantitas Hasil Kerja, Pengetahuan Pekerjaan, Keandalan, Inisiatif, Kerjasama, Kehadiran, Kepatuhan Aturan/K3, Komunikasi, dan Tanggung Jawab).
   - Pilih **Rating (Skala 1 – 5)** dan isi **Komentar / Justifikasi Penilai** pada setiap poin.
   - Di bagian paling bawah kotak Poin D terdapat bar otomatis **`Total Nilai (Σ Rating) / Rata-rata Nilai (Σ Rating / 10)`** yang menghitung **Total / Rata-rata** secara *real-time* (contoh: `36 / 3.60`) beserta badge **`Memenuhi Standar (≥ 3.00)`**.

4. **Bagian E, F & G — Penilaian Kompetensi, Catatan Penilai & Usulan Pengembangan:**
   - Isi **Jumlah Kompetensi yang Memenuhi Requirement** dan **Total Kompetensi yang Dipersyaratkan** (Standar kelulusan: **minimal 90%**).
   - Isi **F.1 Aspek Kekuatan**, **F.2 Aspek yang Perlu Dikembangkan**, **G. Usulan Pengembangan (Training/Coaching/Penugasan)**, serta **Tanggapan Karyawan**.

5. **Bagian H, I & J — 6 Opsi Rekomendasi Keputusan & Approval Berjenjang 3 Tahap:**
   - Sistem mengevaluasi otomatis apakah karyawan PKWT memenuhi minimal **2 dari 3 kriteria** (`Goal ≥ 95%`, `Manajemen Pekerjaan ≥ 3.00`, `Kompetensi ≥ 90%`).
   - Pilih salah satu dari **6 Kesimpulan Rekomendasi (Poin I)**:
     1. **`Diangkat Sebagai Karyawan Tetap`** (muncul input *Tanggal Efektif Pengangkatan*)
     2. **`Diperpanjang Masa PKWT`** (muncul input *Durasi Perpanjangan Bulan*)
     3. **`Dialihkan ke Outtask / Outsource`** (muncul input *Durasi Kontrak Outtask/Outsource*)
     4. **`Diperpanjang Masa Outtask/Outsource`** (muncul input *Durasi Perpanjangan Outtask/Outsource*)
     5. **`Tidak Diperpanjang Outtask/Outsource`** (muncul input *Alasan / Pertimbangan*)
     6. **`Tidak Direkomendasikan / Terminasi PKWT`** (muncul input *Alasan / Pertimbangan*)
   - **Alur Tanda Tangan / Approval Berjenjang (`View Evaluation → Approve / Tanda Tangani Berjenjang`)**:
     - **Tahap 1:** Penilai / Atasan Langsung (`Level 1`)
     - **Tahap 2:** Konfirmasi Karyawan yang Dinilai (`Employee`)
     - **Tahap 3:** Atasan Penilai / Kepala Departemen (`Level 2`)
   - **Cetak Form Evaluasi (PDF):** Klik tombol **`Cetak Form Evaluasi (PDF)`** untuk mengunduh dokumen resmi evaluasi siap cetak.

### C. Dashboard Evaluasi Kinerja & Download Excel (`.xlsx`)
1. Buka menu **Dashboard → Dashboard Evaluasi Kinerja** (`/admin/performance-evaluation-dashboard`) atau klik tombol **`Buka Dashboard Evaluasi`** dari halaman daftar **Evaluations**.
2. Fitur pada Dashboard Evaluasi:
   - **Filter Real-Time:** Filter berdasarkan *Jenis / Periode Evaluasi*, *Status Evaluasi*, *Departemen*, dan *Karyawan*.
   - **4 Kartu KPI:** *Total Evaluasi (PKWT vs Outsource)*, *Status Evaluasi Berjalan (`In Progress` vs `Completed`)*, *Rata-rata Poin D (Manajemen Kerja)*, dan *Kelayakan Pengangkatan Tetap*.
   - **2 Grafik Visual:** Grafik Batang *Distribusi Jenis & Periode Evaluasi* dan Grafik Doughnut *Komposisi Rekomendasi Keputusan (Poin I)*.
   - **Tabel Monitoring Approval Berjenjang (`L1 -> Karyawan -> L2`)** dilengkapi tombol hijau **`Download Excel (.xlsx)`** untuk mengunduh rekapitulasi lengkap 22 kolom dalam format Microsoft Excel.

---

## 5. Modul Retail Point of Sale / Kasir (`POS`)

1. **Membuka Terminal Kasir:**
   - Buka menu **Point of Sale → POS Terminal** (`/admin/pos-terminal`).
   - Pilih **Register Kasir** dan masukkan **Modal Awal Kas (`Opening Cash`)** untuk memulai shift baru.
2. **Transaksi Penjualan & Struk Thermal:**
   - Cari produk melalui kolom pencarian atau scan barcode, atur jumlah/diskon, pilih pelanggan (opsional), lalu pilih metode pembayaran (`Cash / Tunai`, `QRIS`, `Debit / Kartu`, `Transfer`).
   - Saat pembayaran selesai, stok produk otomatis berkurang dan layar menampilkan **Struk Thermal (`58mm` / `80mm`)** siap cetak.
3. **Menutup Shift Kasir (`Close Session`):**
   - Klik **Tutup Shift**, masukkan **Kas Aktual Dihitung (`Closing Cash`)**, dan sistem otomatis menghitung selisih kas (*difference*) beserta rekap penjualan shift tersebut.

---

## 6. Pengelolaan Customer & Vendor (Beserta Bulk Import)

1. **Lokasi Menu:**
   - **Semua Kontak (Customer & Vendor):** Menu **Contact → Contacts** (`/admin/contact/contacts`).
   - **Khusus Customer:** Menu **Sales → Orders → Customers** (`/admin/sales/orders/customers`).
2. **Bulk Import Customer & Vendor (Excel `.xlsx` / CSV):**
   - Klik **`Unduh Template`** (`template-import-customers-vendors.xlsx`), isi kolom `name`, `role` (`customer`/`vendor`/`both`), `account_type`, `email`, `phone`, `mobile`, `tax_id`, `website`, `street1`, `city`, `zip`, `reference`, lalu klik **`Impor Excel / CSV`**.

---

## 7. Manajemen Proyek Multi-Customer, Penugasan Karyawan, Nilai Proyek, Reminder 3-2-1 Bulan & Bulk Import

1. **Satu Customer Memiliki Banyak Proyek (`1 Customer → N Projects`):**
   - Buka menu **Project → Projects** (`/admin/project/projects`).
   - Pilih **Customer**, **Start Date & End Date**, serta **Project Value (`Nilai Proyek Rp`)**.
   - Daftar seluruh proyek dan total nilai kontrak per Customer dapat dilihat langsung pada tab **Proyek** di halaman detail Customer.
2. **Penugasan Karyawan ke Proyek (dua arah — Admin & Operation Manager):**
   - **Dari Proyek:** menu **Project → Tugaskan Karyawan (`Assign Employees`)** (`/admin/project/assign-employees`) → pilih **Proyek**, lalu pilih satu atau beberapa **Karyawan**, klik **Simpan Penugasan**.
   - **Dari Karyawan:** buka **Employees → Employees** → pilih karyawan → tab **Proyek Ditugaskan (`Assigned Projects`)** → pilih proyek → **Simpan Penugasan**.
   - Kedua cara memakai data penugasan yang sama, dan otomatis menambahkan karyawan yang punya partner/user terkait sebagai **pengikut (followers)** agar mendapat akses proyek.
   - **Prasyarat akses login:** Pastikan karyawan punya **Pengguna Terkait (`Related User`)**. Tanpa ini, penugasan tetap tersimpan, tetapi karyawan belum bisa login/mengakses proyek.
   - Layar Project Assign: permission `page_project_assign_project_employees`. Tab di Employee mengikuti izin update Employee (plugin Projects harus terpasang).
3. **Penugasan lewat Manajer Proyek / Task (cara lama, tetap tersedia):**
   - **Manajer Proyek (satu orang):** Pada form proyek, isi **Manajer Proyek (`Project Manager`)**.
   - **Penugasan kerja harian:** **Project → Tasks** → isi **Penerima Tugas (`Assignees`)** (daftar **User**).
   - Hak akses juga dipengaruhi **Visibilitas (`Visibility`)** proyek.
4. **Dashboard beban kerja Operation Manager (`Project → Dashboard`):**
   - Buka **Project → Dashboard** (`/admin/project`).
   - Widget **Item aksi OM** (scope OM/Admin/HRD): cuti pending, kontrak ≤ 30 hari, tugas terlambat, karyawan ditugaskan tanpa Related User.
   - Widget **Kehadiran hari ini**: hadir / terlambat / belum absen (scope).
   - Widget **Operation Manager — Proyek sebagai Manajer Proyek** menampilkan OM + jumlah proyek aktif sebagai **Manajer Proyek**.
   - **Dashboard → Hari Saya** (`/admin/my-day`) — kartu fokus harian per peran (Admin/HRD/OM/Staff/Kasir).
   - **Laporan Ops Mingguan** (`/admin/weekly-operations-report`) — tabel metrik + beban jam/proyek per karyawan + unduh Excel.
   - Daftar **Projects**: kolom **Kesehatan** (progress task, anggota, overdue, sisa hari).
   - **Tugaskan Karyawan**: opsi karyawan menampilkan kapasitas (`N proyek · X jam/minggu`); tombol **Buat Related User yang hilang**.
   - **Employee → Ringkasan 360** (`…/overview`) — proyek, kapasitas, cuti, aset, evaluasi, buat Related User 1-klik.
   - **Team Board**: mode **Kanban Proyek** (seret karyawan antar proyek) untuk Admin/OM.
   - Permission baru: `page_support_my_day_dashboard`, `widget_project_attendance_presence_widget` (setelah `security:sync-rbac`).
5. **Link Tim ↔ Proyek eksplisit:** di **Settings → Teams** pilih **Proyek terkait**. Sinkron anggota & Team Board mengutamakan link ini; jika kosong, fallback cocok nama. Melepas karyawan dari proyek juga melepaskan mereka dari task terbuka proyek tersebut.
5. **Reminder Berjenjang 3, 2, dan 1 Bulan Sebelum `End Date` Proyek:**
   - Sistem memberi badge peringatan dan notifikasi otomatis pada sisa waktu `61–90 hari` (**3 Bulan**), `31–60 hari` (**2 Bulan**), `0–30 hari` (**1 Bulan**), dan saat **Berakhir** (`php artisan projects:notify-expiring-projects`).
6. **Bulk Import Proyek:**
   - Klik **`Unduh Template`** (`template-import-projects.xlsx`) di halaman **Projects**, lalu unggah melalui tombol **`Impor Excel / CSV`**.

---

## 8. Manajemen Aset (Karyawan, Proyek, Customer), Pergerakan Aset & Bulk Import

1. **Mendaftarkan & Mengimpor Aset (`Maintenance → Equipment`):**
   - Buka `/admin/maintenance/equipments` untuk menambah aset beserta **Vendor**, **Assigned Employee**, **Project**, **Customer**, dan **Lokasi**.
   - Tersedia tombol **`Unduh Template`** (`template-import-equipment-assets.xlsx`) dan **`Impor Excel / CSV`**.
2. **Mencatat Pergerakan / Perpindahan Aset (`Assignment`, `Relocation`, `Replacement`, `Return`, `Repair`, `Scrap`):**
   - Buka detail aset → bagian **Riwayat Pergerakan / Perpindahan Aset** → klik **Catat Pergerakan / Pindah Aset**.
   - Untuk **Penggantian Unit (`Replacement`)**, sistem otomatis memindahkan karyawan pemegang, proyek, customer, dan lokasi dari unit lama ke unit pengganti baru.

---

## 9. Integrasi SMTP, auraAI & Jira (`Settings → Integrations`)

1. Buka **Settings → Integrations** (`/admin/integrations/manage-integrations`) — role **Admin** (permission `page_integrations_manage_integrations`).
2. Isi konfigurasi per konektor:
   - **SMTP:** host, port, enkripsi, username/password, from address — lalu **Test Connection**.
   - **AI (auraAI):** provider/base URL, API key, model — lalu **Test Connection**.
   - **Jira:** base URL, email/username, API token — lalu **Test Connection**.
3. Rahasia (password/token) disimpan terenkripsi di settings; biarkan kosong saat edit jika tidak ingin mengubah nilai yang sudah tersimpan.
4. Setelah plugin baru dipasang di server, jalankan `php artisan integrations:install` (lihat **§12**).

---

## 10. Penggunaan `auraAI Assistant` & Pengaturan Tema Modern (`Branding`)

1. **Floating Chatbot `✨ auraAI` & Tombol `✨ Tanya AI` di Chatter:**
   - Klik tombol **`✨ auraAI`** di pojok kanan bawah atau gunakan **`✨ Tanya AI`** / mention **`@ai`** di panel Chatter dokumen apa pun. Data yang disajikan otomatis mengikuti hak akses & scope user yang sedang login.
   - Konfigurasi koneksi AI dapat diatur di **Settings → Integrations** (§9).
2. **Preset Tema Warna 1-Klik (`Settings → Manage Branding`):**
   - Buka **Settings → General → Manage Branding** untuk memilih tema warna instan (`Aura Indigo`, `Emerald Fresh`, `Ocean Blue`, `Royal Violet`, `Sunset Amber`, `Rose Modern`, atau `Slate Minimal`).

---

## 11. Ringkasan Menu yang Mendukung Export / Bulk Import Excel (`.xlsx`)

| Modul | Halaman / URL | Fitur Excel (`.xlsx`) |
| :--- | :--- | :--- |
| **Evaluasi Kinerja (Monitoring & Dashboard)** | `/admin/performance-evaluation-dashboard` & `/admin/evaluations/evaluations` | **Export Excel (`Download Excel .xlsx`)** — 22 kolom nilai & status approval berjenjang |
| **Employees (Karyawan)** | `/admin/employees/employees` | **Template & Bulk Import** (`template-import-employees.xlsx`) |
| **Customers & Vendors** | `/admin/contact/contacts` | **Template & Bulk Import** (`template-import-customers-vendors.xlsx`) |
| **Projects (Proyek)** | `/admin/project/projects` | **Template & Bulk Import** (`template-import-projects.xlsx`) |
| **Equipment (Aset)** | `/admin/maintenance/equipments` | **Template & Bulk Import** (`template-import-equipment-assets.xlsx`) |

---

## 12. Panduan Update / Deploy di Server VPS

Setiap kali ada pembaruan kode dari GitHub (`bionte4/auraERP` branch `main`), jalankan perintah berikut di VPS (`ubuntu@VM-18-102-ubuntu`).

**Langkah standar (setelah ada plugin/composer baru, gunakan blok lengkap di bawah):**

```bash
cd /var/www/auraerp && \
sudo chown -R ubuntu:www-data /var/www/auraerp && \
git fetch origin main && \
git reset --hard origin/main && \
composer dump-autoload -o && \
sudo -u www-data php artisan migrate --force --no-interaction && \
sudo -u www-data php artisan integrations:install --no-interaction && \
sudo -u www-data php artisan projects:sync-team-memberships && \
sudo -u www-data php artisan security:sync-rbac && \
sudo chown -R www-data:www-data storage bootstrap/cache public && \
sudo chmod -R ug+rwx storage bootstrap/cache && \
sudo -u www-data php artisan optimize:clear
```

**Catatan deploy:**
1. `sudo chown ... ubuntu:www-data` diperlukan sebelum `composer dump-autoload` agar `filament:upgrade` (post-autoload-dump) bisa menulis ke `public/js` & `public/css`.
2. `composer dump-autoload -o` wajib jika ada **plugin baru** (mis. `integrations`) agar namespace Artisan & autoload PSR-4 terdaftar.
3. `integrations:install` aman dijalankan ulang (idempotent) bila plugin sudah terpasang.
4. `security:sync-rbac` menyinkronkan permission baru (termasuk **Tugaskan Karyawan** & **Integrations**).
5. Setelah deploy, uji: login Admin → **Project → Tugaskan Karyawan**, **Employees → tab Proyek Ditugaskan**, **Settings → Integrations**.
