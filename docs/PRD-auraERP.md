# PRD — auraERP (Opsora)

| Field | Nilai |
| :--- | :--- |
| **Produk** | auraERP — customisasi operasional Opsora |
| **Versi dokumen** | 1.5 |
| **Tanggal** | 2026-10-21 |
| **Revisi** | UI Roles berbasis section permission (SEC-07), OM kelola karyawan terscope PM (EMP-07), sidebar expanded + badge navigasi, pemisahan Accounting/Accounting Manager + Kontrol Anggaran Proyek (SEC-08, PRJ-14/15), kerahasiaan nilai kontrak (PRJ-16), hapus tim beranggotakan dengan konfirmasi (SEC-09), Team Board multi-layout + pencarian + sortir (PRJ-17), dashboard tanggung jawab Proyek & Team Leader (PRJ-18), kelengkapan persona/checklist akunting, status & rencana OTP login (SEC-10, implementasi ditahan) |
| **Status** | Baseline produk (fitur utama sudah live / di-deploy) |
| **Pemilik produk** | Opsora / auraERP ops |
| **Referensi operasional** | [WORKING_INSTRUCTION.md](../WORKING_INSTRUCTION.md) |
| **Stack** | Laravel 11+, Filament 4/5, Livewire 3, PostgreSQL, PHP 8.3+ (VPS: PHP 8.4) |
| **URL produksi** | `https://auraerp.my.id`, `https://opsora.my.id` |

---

## 1. Ringkasan eksekutif

auraERP Opsora adalah ERP modular berbasis AureusERP/Filament yang dikustomisasi untuk operasi harian perusahaan jasa/proyek: **HR & absensi**, **evaluasi kinerja PKWT/outtask**, **manajemen proyek multi-customer**, **laporan mingguan internal & klien**, **POS retail**, **akuntansi ringan**, serta **keamanan akses berjenjang (RBAC + scope organisasi)**.

Dokumen ini menetapkan *apa yang harus benar* di produk. Cara menjalankan menu dan deploy ada di Working Instruction (WI).

---

## 2. Masalah yang diselesaikan

| Masalah bisnis | Solusi produk |
| :--- | :--- |
| Hak akses menyebar / role “bengkak” setelah grant manual | Baseline RBAC ketat; `security:sync-rbac` **menyetel ulang** permission non-Admin |
| OM terpapar modul ERP yang bukan domainnya (Sales/Inventory penuh) | Role Operation Manager fokus delivery proyek + approval tim |
| HRD melihat laporan klien / widget operasional yang bukan miliknya | Laporan klien & widget OM hanya Admin/OM |
| Staff melihat dashboard evaluasi agregat perusahaan | Dashboard evaluasi hanya Admin / HRD / OM |
| Klien butuh status proyek tanpa jam kerja & detail task mentah | Laporan Mingguan Proyek untuk Klien (preview + Excel, masking) |
| Penugasan karyawan ke proyek tidak konsisten / sulit | Assign dua arah (Proyek ↔ Karyawan) + sinkron Tim |
| Absensi tanpa konteks lokasi | Geofence multi-lokasi + kamera di perangkat mobile |
| Evaluasi PKWT/outtask tidak terstandar | Form evaluasi berjenjang + dashboard monitoring + export Excel |

---

## 3. Tujuan & non-tujuan

### 3.1 Tujuan (Goals)

1. Satu panel Admin untuk operasional harian (HR, proyek, POS, akuntansi ringkas).
2. Isolasi data lewat **role** + **Resource Permission** (`Global` / `Group` / `Individual`) + hirarki organisasi.
3. Baseline permission yang **dapat diulang** di setiap deploy (`syncPermissions`).
4. Laporan operasional internal terpisah dari laporan siap klien.
5. Alur evaluasi kinerja PKWT/outtask yang dapat diaudit (tanda tangan L1 → karyawan → L2).

### 3.2 Non-tujuan (Non-goals)

- Mengganti ERP akuntansi penuh (modul Accounting tetap ringkas; bukan pengganti sistem pajak eksternal).
- Mobile native app terpisah (absensi via browser + geofence).
- Custom grant permission per-user sebagai sumber kebenaran (baseline role menang setelah sync).
- Publikasi laporan klien otomatis ke email/portal eksternal (unduh manual oleh OM/Admin).

---

## 4. Persona & role

| Persona | Role sistem | Kebutuhan utama |
| :--- | :--- | :--- |
| Direksi / IT Admin | **Admin** | Akses penuh, audit, backup, IP block, integrasi, instalasi plugin |
| HRD | **HRD** | Karyawan, absensi sites, cuti, performance/evaluation, dashboard evaluasi, laporan ops mingguan |
| Manajer Operasional | **Operation Manager** | Proyek, assign karyawan, timesheet/task, laporan ops + **laporan klien**, evaluasi L1/L2, approval cuti/absensi tim |
| Finance (staff) | **Accounting** | Invoice/bill/payment (buat/ubah, **tanpa hapus**), partners/products terkait, My Day, Team Board, lihat Project Dashboard / proyek (view), Kontrol Anggaran Proyek sebatas timnya |
| Finance (manajer) | **Accounting Manager** | Seluruh hak Accounting staff **ditambah hapus/restore/force-delete dokumen keuangan**, Kontrol Anggaran Proyek se-perusahaan, atur anggaran operasional & nilai kontrak dari halaman Budget Control |
| Kasir toko | **Kasir** | POS terminal, sesi, order; absensi; My Day; lihat/create partner & lihat produk; tanpa user management |
| Karyawan lapangan / staf | **Staff** | Absensi, cuti sendiri, task/timesheet, lihat evaluasi sendiri — tanpa dashboard agregat |

**Scope data (orthogonal ke role):**

| Scope | Perilaku |
| :--- | :--- |
| `Global` | Seluruh data perusahaan sesuai permission modul |
| `Group` | Diri + tim + bawahan hirarki (L1/L2…) |
| `Individual` | Hanya data milik sendiri |

---

## 5. Requirement fungsional

ID memakai prefiks modul. Prioritas: **P0** harus ada / sudah ada; **P1** penting; **P2** nice-to-have.

### 5.1 Keamanan & RBAC — `SEC`

| ID | Requirement | Prioritas | Status |
| :--- | :--- | :--- | :--- |
| SEC-01 | Sistem menyediakan 8 role standar: Admin, HRD, Operation Manager, Accounting, Accounting Manager, Kasir, Staff, Team Leader | P0 | Done |
| SEC-08 | **Pemisahan tugas akunting:** Accounting (staff) boleh buat/ubah dokumen keuangan tetapi **tidak** memegang permission hapus finansial (invoice/bill/payment/account move); hak delete/restore/force-delete keuangan hanya **Accounting Manager** (+ Admin). User Accounting lama tidak otomatis naik role — penetapan Accounting Manager manual oleh Admin | P0 | Done |
| SEC-02 | `php artisan security:sync-rbac` menyinkronkan baseline; non-Admin memakai **sync** (stale grant hilang); Admin mendapat semua permission | P0 | Done |
| SEC-03 | Fitur admin-only (Audit Trail, Backup DB, IP Diblokir) tidak dapat diakses non-Admin (role + policy) | P0 | Done |
| SEC-04 | Rate limit login + auto-blokir IP setelah ambang kegagalan; Admin dapat unblock | P0 | Done |
| SEC-05 | Resource Permission Global/Group/Individual diterapkan pada resource ber-policy | P0 | Done |
| SEC-06 | Setelah sync RBAC, user harus logout/login agar cache Spatie segar | P0 | Operasional |
| SEC-07 | UI pengelolaan Role (`/admin/shield/roles`) menampilkan section permission Resources/Pages/Widgets dengan checkbox per entitas; baseline role bawaan bersumber dari kode (`RolePermissionSynchronizer`) dan ter-reset saat sync, role custom tidak disentuh sync | P0 | Done |
| SEC-09 | **Hapus tim yang masih beranggotakan:** tombol Delete di `Settings → Teams` selalu tampil bagi pemegang `delete_security_team` (praktisnya Admin; OM tetap tanpa hak hapus). Menghapus tim beranggotakan wajib lewat modal konfirmasi yang menyebutkan jumlah anggota; anggota dilepas dari tim lalu tim dihapus dalam satu alur — tidak perlu mengosongkan tim manual lebih dulu. Team Board sendiri tidak menyediakan aksi hapus | P0 | Done |
| SEC-10 | **OTP/MFA saat login (belum diaktifkan — keputusan: implementasi ditahan):** aktifkan MFA bawaan Filament — **Email Authentication (OTP 6 digit via email)** dan/atau **App Authentication (TOTP + recovery codes)** — sebagai faktor kedua setelah password. Fondasi sudah terpasang (User mengimplementasikan kedua kontrak MFA, kolom `app_authentication_secret`/`app_authentication_recovery_codes`/`has_email_authentication` sudah termigrasi, halaman Login & Profile sudah menangani challenge); aktivasi = registrasi provider di panel + rollout wajib bertahap (Admin & Accounting Manager → Operation Manager → semua user). Alternatif *Cloudflare Access One-time PIN* sudah dievaluasi: bukan pilihan utama (tier gratis ≤50 user, login ganda tanpa integrasi JWT, origin wajib dikunci dari bypass); *Cloudflare Turnstile* hanya anti-bot (bukan OTP) dan boleh menjadi pelengkap. Prasyarat: SMTP produksi andal + email user aktif | P1 | Planned |

### 5.2 Karyawan & organisasi — `EMP`

| ID | Requirement | Prioritas | Status |
| :--- | :--- | :--- | :--- |
| EMP-01 | CRUD karyawan, departemen, hirarki Manager / Approver L1–L2 | P0 | Done |
| EMP-02 | Tipe karyawan & masa kontrak; filter kontrak ≤ 30 hari | P0 | Done |
| EMP-03 | Bulk import karyawan via Excel template | P0 | Done |
| EMP-04 | Penugasan proyek dari profil karyawan sinkron dengan Assign Employees | P0 | Done |
| EMP-05 | Related User wajib agar karyawan bisa login & akses proyek | P0 | Done |
| EMP-06 | **People Dashboard** (`/admin/employees/dashboard`): KPI klik, tabel kontrak mendekati/lewat tenggat, tabel perlu perhatian; Admin/HRD penuh, OM scope tim | P0 | Done |
| EMP-08 | **Riwayat Kontrak karyawan** (`employees_employee_contracts`): setiap kontrak tercatat berurutan (Kontrak ke-1, ke-2, …) dengan tipe (PKWT/PKWTT/Outsource/Outtask), nomor, periode, status (Aktif/Selesai/Diperpanjang/Dialihkan PKWTT/Terminasi) & sumber; kontrak aktif terbaru menyinkronkan `contract_end_date` + tipe berjalan; kontrak lama otomatis berstatus Diperpanjang saat kontrak baru aktif; akumulasi bulan PKWT terhitung (dasar kepatuhan batas 60 bulan PP 35/2021); tab **Riwayat Kontrak** di profil karyawan + badge *Kontrak ke-N* di daftar; backfill *Kontrak ke-1* via `employees:backfill-contracts` | P0 | Done |
| EMP-07 | **OM kelola karyawan proyeknya**: Operation Manager dapat tambah/edit/hapus (soft-delete per record saja, tanpa bulk/force-delete) karyawan, tetapi record karyawan terscope: lihat = proyek yang ia tangani (+ diri sendiri, bawahan langsung, karyawan buatannya), edit/hapus hanya proyek di mana ia Project Manager (+ karyawan buatannya); Admin/HRD tanpa scope | P0 | Done |

### 5.3 Absensi — `ATT`

| ID | Requirement | Prioritas | Status |
| :--- | :--- | :--- | :--- |
| ATT-01 | Master Work sites (lat/lng + radius) terpisah dari Work Location peta Welcome | P0 | Done |
| ATT-02 | Clock in/out dalam radius GPS | P0 | Done |
| ATT-03 | Perangkat mobile: kamera/face verification; laptop: GPS saja | P0 | Done |

### 5.4 Time Off — `TO`

| ID | Requirement | Prioritas | Status |
| :--- | :--- | :--- | :--- |
| TO-01 | Pengajuan cuti/izin dengan Approver L1 & L2 otomatis dari hirarki | P0 | Done |
| TO-02 | Eskalasi otomatis jika L1 tidak merespons dalam 24 jam | P0 | Done |

### 5.5 Performance & evaluasi — `EVAL`

| ID | Requirement | Prioritas | Status |
| :--- | :--- | :--- | :--- |
| EVAL-01 | Performance Plans (goals, bobot, progress, skor tertimbang) | P0 | Done |
| EVAL-02 | Form evaluasi PKWT/outtask/reguler dengan bagian A–J & rekomendasi 6 opsi | P0 | Done |
| EVAL-03 | Approval tanda tangan berjenjang L1 → karyawan → L2; status In Progress → Completed otomatis | P0 | Done |
| EVAL-04 | Bagian Goal (C) disembunyikan untuk Outsource/Outtask | P0 | Done |
| EVAL-05 | Cetak PDF form evaluasi | P0 | Done |
| EVAL-06 | Dashboard evaluasi (KPI + chart + tabel + export Excel 22 kolom) hanya **Admin / HRD / OM** | P0 | Done |
| EVAL-07 | Staff tidak punya `page_evaluation_performance_evaluation_dashboard` maupun `view_any_evaluation` | P0 | Done |

### 5.6 Proyek & penugasan — `PRJ`

| ID | Requirement | Prioritas | Status |
| :--- | :--- | :--- | :--- |
| PRJ-01 | 1 Customer → N Projects; nilai proyek, start/end date | P0 | Done |
| PRJ-02 | Assign Employees (checkbox multi-karyawan) + sinkron Livewire saat ganti proyek | P0 | Done |
| PRJ-03 | Assign dua arah Proyek ↔ Karyawan; followers otomatis bila Related User ada | P0 | Done |
| PRJ-03a | Unassign karyawan: hilangkan centang di Assign Employees / Assigned Projects → simpan; lepaskan juga dari task terbuka | P0 | Done |
| PRJ-04 | Sinkron Tim ↔ Proyek (nama/link); backfill `projects:sync-team-memberships` | P0 | Done |
| PRJ-05 | Reminder proyek 3/2/1 bulan sebelum end date | P0 | Done |
| PRJ-06 | Dashboard OM: widget beban, aksi, kehadiran (scope) | P0 | Done |
| PRJ-07 | Bulk import proyek Excel | P1 | Done |
| PRJ-08 | **Manajer Proyek** (`projects.user_id`): assign/ganti/kosongkan di form proyek (1 user); beda dari Assign Employees. Penetapan massal lihat PRJ-12 | P0 | Done |
| PRJ-10 | **Team Leader proyek** (`projects_projects.team_leader_employee_id`, berbasis karyawan): satu karyawan memimpin maksimal **satu proyek aktif** (validasi model + partial unique index); leader otomatis anggota proyek; role baseline **Team Leader** hanya melihat proyek/task/timesheet proyek pimpinannya (`OperationalScope` + scoping resource) dan tidak dapat mengelola karyawan | P0 | Done |
| PRJ-11 | Tab **Struktur Organisasi** pada detail proyek: tampil hierarki Project Manager → Team Leader → Anggota Tim (read-only) | P1 | Done |
| PRJ-12 | **Atur Project Manager massal** (khusus Admin, `page_project_assign_project_managers`): grid semua proyek + dropdown PM per baris (hanya user Operation Manager), chip beban proyek per OM, filter & badge proyek tanpa PM; **bulk via template Excel terisi** (semua proyek + email PM saat ini; sheet referensi email OM) dengan laporan hasil per baris; PM tersinkron ke tim proyek. Panduan operasional: `docs/OM-USER-EMPLOYEE-LINKING.md` | P0 | Done |
| PRJ-13 | **Scope daftar Projects/Tasks & kartu Team Board untuk Operation Manager** dibatasi ke proyek penugasan (sebagai PM atau via karyawan tertaut yang di-assign); Team Leader proyek pimpinannya, Staff penugasannya; Accounting, Accounting Manager & Admin/HRD tetap penuh (`OperationalScope::projectListScopeIds` + `hasFinanceRole`; query resource melepas `OwnershipScope` untuk role full-list agar Resource Permission Group/Individual tidak menyembunyikan daftar) | P0 | Done |
| PRJ-09 | Widget dashboard **Proyek Mendekati / Melewati Tenggat**: proyek aktif expired/≤3 bulan, badge sisa hari, progress + task overdue, baris bisa diklik ke View/Edit | P0 | Done |
| PRJ-14 | **Kontrol Anggaran Proyek** (`/admin/project/budget-control`, `page_project_project_budget_control`): per proyek nilai kontrak vs terinvoice/belum terbayar (invoice posted), anggaran operasional vs biaya vendor posted (serapan % terhitung otomatis dan tidak dapat diedit manual, sisa anggaran, status Aman <80% / Waspada ≥80% / Over budget ≥100% / Belum ada anggaran), pencarian + filter status + ringkasan total + ekspor Excel. Scope: Admin & Accounting Manager se-perusahaan, Accounting staff proyek timnya (`ProjectBudgetControlData`). Admin & Accounting Manager dapat mengatur `operational_cost` per proyek langsung dari tabel (tanpa `update_project_project`); staff read-only | P0 | Done |
| PRJ-15 | **Pengaitan dokumen keuangan ke proyek:** kolom `accounts_account_moves.project_id` (nullable, FK null-on-delete) + field **Proyek** pada form Invoice pelanggan & Bill vendor (invoice memfilter proyek sesuai customer terpilih; non-editable saat posted/cancel) sebagai dasar realisasi anggaran per proyek (PRJ-14) | P0 | Done |
| PRJ-16 | **Kerahasiaan nilai kontrak proyek:** `project_value` hanya terlihat oleh Admin & tim budget-control (Accounting/Accounting Manager) via satu gerbang `OperationalScope::canViewProjectValue()` — disembunyikan dari daftar/View/Edit proyek (field tidak dirender untuk non-finance), detail Customer, kartu Team Board, Ringkasan 360 karyawan, notifikasi pengingat proyek (isi tanpa nilai), konteks auraAI, dan API proyek. Accounting Manager dapat mengedit nilai kontrak dari halaman Budget Control (tercatat di activity log). OM/Team Leader memantau opex proyeknya lewat widget **Anggaran Operasional & Realisasi Opex** (`widget_project_operational_budget_widget`: anggaran, biaya vendor aktual, sisa, serapan — tanpa nilai kontrak/invoice) | P0 | Done |
| PRJ-17 | **Team Board untuk skala banyak tim/proyek:** pilihan tampilan **Cards** (default) / **Compact** (kartu ringkas tanpa blok About) / **List** (satu baris per tim: anggota, proyek, task, nilai, status, diperbarui, Details) — Project kanban tetap khusus Admin/OM; **pencarian live** menurut nama tim atau nama proyek; **sortir** Nama A–Z/Z–A, anggota/proyek/task terbanyak, progress tertinggi/terendah, terakhir diperbarui, dan nilai terbesar (opsi & kolom nilai tunduk pada gerbang PRJ-16). Pilihan tampilan & sortir divalidasi server, tersimpan per sesi, dan scope kartu tetap mengikuti penugasan viewer (PRJ-13) | P1 | Done |
| PRJ-18 | **Dashboard tanggung jawab Proyek & Team Leader:** widget `widget_project_project_responsibility_widget` di Project Dashboard, satu baris per proyek aktif (Proyek, Customer, Project Manager, Team Leader, jumlah anggota, progress tugas, tugas terlambat, sisa waktu tenggat; baris klik ke detail proyek; mengikuti filter atas dashboard). Scope `OperationalScope`: Operation Manager proyek kelolaan/ikutannya, Team Leader hanya proyek pimpinannya, Admin/HRD penuh. Tanpa nilai kontrak/data penagihan (tunduk PRJ-16). Permission di-grant baseline ke OM & Team Leader oleh `RolePermissionSynchronizer` | P0 | Done |

### 5.7 Laporan proyek — `RPT`

| ID | Requirement | Prioritas | Status |
| :--- | :--- | :--- | :--- |
| RPT-01 | **Laporan Ops Mingguan** (internal): metrik + beban jam; Admin/HRD/OM; data mengikuti OperationalScope | P0 | Done |
| RPT-02 | **Laporan Mingguan Proyek untuk Klien**: 1 proyek per unduhan, rentang tanggal kustom | P0 | Done |
| RPT-03 | Wajib preview on-screen sebelum download Excel | P0 | Done |
| RPT-04 | Masking judul task / overdue; **jam kerja disembunyikan** di output klien | P0 | Done |
| RPT-05 | Permission klien-report hanya Admin/OM; **bukan** HRD | P0 | Done |

### 5.8 POS — `POS`

| ID | Requirement | Prioritas | Status |
| :--- | :--- | :--- | :--- |
| POS-01 | Terminal kasir: buka/tutup sesi, order, metode bayar, struk thermal | P0 | Done |
| POS-02 | Role Kasir dapat POS + absensi; tidak dapat manajemen user keamanan | P0 | Done |

### 5.9 Integrasi & AI — `INT`

| ID | Requirement | Prioritas | Status |
| :--- | :--- | :--- | :--- |
| INT-01 | Settings → Integrations: SMTP, auraAI, Jira (test connection); rahasia terenkripsi | P0 | Done |
| INT-02 | auraAI assistant menghormati hak akses & scope user login | P0 | Done |
| INT-03 | Akses halaman Integrations khusus Admin | P0 | Done |

### 5.10 Aset — `AST`

| ID | Requirement | Prioritas | Status |
| :--- | :--- | :--- | :--- |
| AST-01 | Equipment + pergerakan aset (assign/relocate/replace/…) + bulk import | P1 | Done |
| AST-02 | Baseline HRD **tidak** otomatis mendapat modul Maintenance penuh | P0 | Done |

---

## 6. Matriks akses (ringkas)

| Fitur / permission kunci | Admin | HRD | OM | Accounting | Kasir | Staff |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| Audit / Backup / Blocked IP | ✓ | — | — | — | — | — |
| Dashboard Evaluasi | ✓ | ✓ | ✓ | — | — | — |
| Laporan Ops Mingguan | ✓ | ✓ | ✓ | — | — | — |
| Laporan Proyek Klien | ✓ | — | ✓ | — | — | — |
| Assign Project Employees | ✓ | — | ✓ | — | — | — |
| Widget aksi OM | ✓ | — | ✓ | — | — | — |
| Project Dashboard (view) | ✓ | — | ✓ | ✓ | — | — |
| Kontrol Anggaran Proyek | ✓ | — | — | ✓** | — | — |
| Team Board | ✓ | — | ✓ | ✓ | — | ✓ |
| My Day | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| POS Terminal | ✓ | — | — | — | ✓ | — |
| Sales / Purchase / Inventory penuh | ✓ | — | — | sebagian* | — | — |
| Absensi clock | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |

\*Accounting: invoice/bill/payment/partner/product terkait + view proyek; bukan domain OM Sales/Inventory.

\**Kontrol Anggaran Proyek: Accounting staff sebatas proyek timnya (read-only); **Accounting Manager** (kolom Accounting, cakupan manajer) se-perusahaan plus hak hapus dokumen keuangan dan edit anggaran operasional/nilai kontrak dari halaman tersebut (SEC-08, PRJ-14).

---

## 7. Requirement non-fungsional

| ID | Area | Requirement |
| :--- | :--- | :--- |
| NFR-01 | Keamanan | Artisan & web process menulis ke `storage` / `bootstrap/cache` sebagai `www-data`; tidak meninggalkan file log milik root/ubuntu |
| NFR-02 | Deploy | Kode produksi dari fork `bionte4/auraERP` `master`; setelah pull wajib `security:sync-rbac` bila ada perubahan RBAC |
| NFR-03 | Idempotensi | `security:sync-rbac`, `integrations:install`, `projects:sync-team-memberships` aman dijalankan ulang |
| NFR-04 | Observabilitas | Audit trail untuk peristiwa keamanan penting (login, perubahan user/role) |
| NFR-05 | Backup | Backup DB terjadwal; restore production default off |
| NFR-06 | UX bahasa | UI operasional memakai bahasa Indonesia formal (**Anda**) |
| NFR-07 | Uji | Fitur kritis dilindungi Pest feature tests (RBAC sync, assign employees, customer weekly report, admin-only) |

---

## 8. Alur utama (ringkas)

### 8.1 Penugasan proyek

**A. Manajer Proyek (1 orang / proyek)** — field form proyek `Project Manager` (`user_id`):

```
OM/Admin → Project → Projects → Edit proyek
        → pilih User di Manajer Proyek → Save   (= assign / ganti)
        → kosongkan Manajer Proyek → Save       (= unassign)
        → widget Dashboard OM menampilkan hitungan proyek sebagai manajer
```

**B. Karyawan di proyek (N orang)** — Assign Employees / Assigned Projects:

```
OM/Admin → pilih Proyek → pilih Karyawan (checkbox)
       → simpan → pivot assign + followers (jika Related User)
       → Tim proyek tersinkron (nama/link)
Unassign: hilangkan centang → simpan (juga lepas dari task terbuka)
```

### 8.2 Laporan klien mingguan

```
OM/Admin → pilih 1 proyek + start/end date
        → Preview di layar
        → Download Excel (masked, tanpa jam)
```

### 8.3 Evaluasi PKWT

```
Buat Evaluation → status In Progress
→ L1 tanda tangan → Karyawan konfirmasi → L2 tanda tangan
→ Completed → muncul di Dashboard Evaluasi / Excel
```

### 8.4 Deploy + RBAC

```
git fetch/merge fork/master → composer install
→ migrate → security:sync-rbac → optimize
→ user logout/login
```

---

## 9. Kriteria penerimaan (Acceptance)

### 9.1 RBAC (wajib setelah setiap sync)

- [ ] Staff/Kasir/Accounting **tidak** dapat membuka `/admin/performance-evaluation-dashboard`
- [ ] HRD **tidak** punya Customer Project Weekly Report / widget aksi OM
- [ ] OM **tidak** punya menu penuh Sales / Purchase / Inventory
- [ ] Grant manual di luar baseline hilang setelah `security:sync-rbac`
- [ ] Admin tetap akses penuh termasuk audit & backup

### 9.2 Laporan klien

- [ ] Hanya satu proyek per unduhan
- [ ] Preview wajib sebelum download
- [ ] Rentang tanggal kustom diterapkan ke deliverable yang ditampilkan
- [ ] Output tidak menampilkan jam kerja; task sensitif ter-mask

### 9.3 Assign karyawan & Manajer Proyek

- [ ] Checkbox mencerminkan anggota proyek saat ganti proyek
- [ ] Simpan memperbarui anggota & tidak error 500 pada daftar besar
- [ ] Unassign karyawan (hilangkan centang → simpan) melepas dari pivot & task terbuka
- [ ] Assign/ganti/kosongkan **Manajer Proyek** di form proyek hanya mengubah `user_id` (1 orang); tidak memakai layar Assign Employees

### 9.4 Deploy VPS

- [ ] Tidak ada `Permission denied` pada `storage/logs` / `bootstrap/cache` saat Artisan sebagai `www-data`
- [ ] `security:sync-rbac` melaporkan 8 roles: Admin, HRD, Operation Manager, Accounting, Accounting Manager, Kasir, Staff, Team Leader
- [ ] Accounting staff **tidak** dapat menghapus invoice/bill/payment; Accounting Manager dapat (SEC-08), dan keduanya membuka **Kontrol Anggaran Proyek** (manager se-perusahaan, staff sebatas timnya)

---

## 10. Asumsi & dependensi

1. Plugin inti (employees, attendances, projects, evaluations, performance, pos, security, integrations, …) terpasang di lingkungan produksi.
2. Karyawan yang perlu login punya **Related User**.
3. Scheduler cron aktif untuk backup, eskalasi cuti, reminder proyek.
4. Write ke upstream `aureuserp/aureuserp` mungkin 403; distribusi lewat fork `bionte4/auraERP`.

---

## 11. Risiko & mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| Admin menambah permission manual lalu sync menghapusnya | Bingung “permission hilang” | Dokumentasikan di WI; ubah baseline di `RolePermissionSynchronizer` bila grant sah |
| Ownership file storage salah user | Artisan/Composer gagal | Selalu `sudo -u www-data`; chown storage/cache |
| Cache permission Spatie basi | Menu lama masih tampil | Logout/login; flush cache di sync |
| Laporan klien bocor jam/detail task | Risiko kontraktual | Masking + hide hours; preview sebelum unduh |

---

## 12. Roadmap / backlog (usulan)

| Item | Prioritas | Catatan |
| :--- | :--- | :--- |
| Portal unduh laporan klien ber-token (tanpa login panel) | P2 | Di luar non-goal saat ini |
| Notifikasi email otomatis laporan mingguan ke PIC klien | P2 | Butuh SMTP Integrations stabil |
| Soft-lock: warning UI sebelum sync mencabut grant custom | P1 | Kurangi surprise operasional |
| Perluas Accounting ke laporan AR aging di My Day | P1 | Sudah ada kartu; bisa diperdalam |
| Hardening Select native di seluruh filter app (audit lanjutan) | P1 | Sebagian sudah diperbaiki |
| Aktifkan OTP/MFA login (SEC-10) | P1 | Fondasi MFA bawaan Filament sudah terpasang; aktivasi ditahan menunggu kesiapan SMTP & keputusan rollout |

---

## 13. Jejak keputusan produk (terkait)

| Keputusan | Pilihan |
| :--- | :--- |
| Sync RBAC | `syncPermissions` untuk non-Admin (reset baseline) |
| Dashboard evaluasi | Hard-lock Admin / HRD / OM |
| Laporan klien | 1 proyek / unduhan; preview wajib; mask overdue/titles; hide hours; Excel |
| Domain OM | Proyek + HR approval tim; **bukan** Sales/Purchase/Inventory penuh |
| Domain HRD | People ops + eval + weekly ops internal; **bukan** customer weekly report |
| Manajer Proyek | 1 user via `projects.user_id` di form proyek; **terpisah** dari pivot Assign Employees |
| OTP login | Belum diaktifkan; rekomendasi MFA bawaan Filament (Email OTP/TOTP) di atas Cloudflare Access OTP; implementasi ditahan (SEC-10) |

---

## 14. Lampiran

- Working Instruction: [WORKING_INSTRUCTION.md](../WORKING_INSTRUCTION.md)
- ERD: [ERD-auraERP.md](./ERD-auraERP.md)
- Database schema: [DATABASE-SCHEMA.md](./DATABASE-SCHEMA.md)
- Perintah kunci: `security:sync-rbac`, `projects:sync-team-memberships`, `integrations:install`
- Kelas inti: `Webkul\Security\Services\RolePermissionSynchronizer`
- Halaman kunci:
  - `/admin/performance-evaluation-dashboard`
  - `/admin/weekly-operations-report`
  - `/admin/customer-project-weekly-report`
  - `/admin/project/assign-employees` (anggota kerja)
  - `/admin/project/projects/{id}/edit` (field **Manajer Proyek**)
  - `/admin/pos-terminal`
