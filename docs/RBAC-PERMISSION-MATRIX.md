# Matriks Permission & Role (RBAC) — auraERP

Dokumen ini menjelaskan arti angka **Permissions** pada daftar **Pengaturan → Roles**, aturan pemberian hak akses untuk setiap role, dan tata kelola perubahannya. Sumber kebenaran aturan ada di kode: `plugins/webkul/security/src/Services/RolePermissionSynchronizer.php`; dokumen ini adalah penjelasan terbacanya.

| Field | Nilai |
| :--- | :--- |
| **Versi dokumen** | 1.3 |
| **Tanggal** | 2026-10-20 |
| **Status** | Live (sesuai hasil sync di production) |

---

## 1. Apa arti angka “Permissions” di daftar Roles?

Angka tersebut adalah **jumlah permission yang saat ini dimiliki role** (baris aktif di tabel `role_has_permissions`). Cara membacanya:

- Total “pool” permission di sistem ditentukan oleh plugin yang ter-install. Di production saat ini pool-nya **1.366 permission** — itulah sebabnya **Admin = 1.366** (Admin selalu menerima seluruh pool).
- Angka role lain adalah hasil irisan pool dengan aturan role-nya (lihat §3). Angka **bukan skor/nilai tetap**: ia naik/turun mengikuti jumlah plugin & resource yang ter-install, jadi wajar bila di server lokal (plugin sebagian) angkanya lebih kecil dengan pola yang sama.
- Rincian persisnya terlihat di **Roles → Edit**: checkbox tercentang pada section **Resources / Pages / Widgets**. Total centang = angka di daftar.
- Daftar nama permission sebuah role juga bisa dilihat via server:

```bash
cd /var/www/auraerp && sudo -u www-data env HOME=/tmp php artisan tinker --execute='print_r(\Spatie\Permission\Models\Role::where("name", "Operation Manager")->where("guard_name", "web")->first()->permissions->pluck("name")->sort()->values()->toArray());'
```

## 2. Konvensi nama permission

Permission resource mengikuti pola `{aksi}_{grup}_{resource}`, halaman diawali `page_`, widget diawali `widget_`. Nama resource bersarang memakai `::` (mis. `time_off_my::time::off`).

| Aksi | Arti |
| :--- | :--- |
| `view_any` | Melihat **daftar/list** resource |
| `view` | Melihat **detail** satu record |
| `create` | Membuat record baru |
| `update` | Mengubah record |
| `delete` | Menghapus record (**soft-delete**, masih bisa dipulihkan) |
| `delete_any` | Menghapus **massal** (bulk) terpilih |
| `restore` / `restore_any` | Memulihkan record terhapus (per record / massal) |
| `force_delete` / `force_delete_any` | Menghapus **permanen** (per record / massal) |
| `reorder` | Mengubah urutan record (drag sort) |
| `page_*` | Mengakses sebuah halaman khusus (dashboard, terminal POS, dsb.) |
| `widget_*` | Melihat sebuah widget dashboard |

Contoh: `update_employee_employee` = boleh mengubah data pada resource **Employee** di grup **Employee**; `widget_employee_people_stats_widget` = boleh melihat widget statistik di People Dashboard.

## 3. Aturan per role (baseline)

Baseline disusun dari dua mekanisme di `RolePermissionSynchronizer`:

- **`contains`** — role menerima semua permission yang namanya mengandung kata kunci tertentu (menyapu lintas resource/plugin).
- **`exact`** — daftar nama permission spesifik yang diberikan satu per satu.

Hasil snapshot production (2026-10-20, pool 1.366):

| Role | Jml. | Mekanisme utama | Profil akses |
| :--- | ---: | :--- | :--- |
| **Admin** | 1.366 | Seluruh pool | Akses penuh termasuk fitur admin-only (Audit Trail, Database Backups, Blocked IPs, Sync RBAC). |
| **Accounting** | 544 | `contains`: invoice, account, payment, bill, partner, contact, product | Keuangan & mitra. Angkanya besar karena kata kuncinya menyapu banyak resource lintas plugin (akuntansi, penjualan, pembelian, produk). Dapat melihat proyek (view saja) + dashboard proyek. **Tidak** mengelola karyawan/proyek. |
| **HRD** | 343 | `contains`: employee, attendance, performance, evaluation, time_off, recruitment | Domain SDM penuh: karyawan, absensi, cuti, evaluasi, rekrutmen + People Dashboard. Tanpa widget OM dan tanpa laporan proyek klien. |
| **Operation Manager** | 198 | `contains`: project_project, project_task, project_timesheet, timesheet, partner_partner, performance, evaluation, time_off, attendance + daftar `exact` | Delivery proyek & approval tim: proyek/task/timesheet, dashboard proyek & OM, laporan mingguan (internal + klien), evaluasi, People Dashboard (terscope). Karyawan: lihat + `create/update/delete` per record (lihat pembatasan scope di §4). Teams: `view/create/update` (tanpa hapus) agar dapat mengelola tim proyek & membuka Team Board. Tanpa modul Sales/Purchase/Inventory penuh, tanpa akuntansi. |
| **Kasir** | 32 | `contains`: `_pos_` + `exact` | Terminal POS, clock absensi, My Day; lihat produk & mitra (+ buat mitra). |
| **Team Leader** | ±25 | `exact` saja | Self-service seperti Staff + lihat karyawan & departemen + Project Dashboard. Leader **satu proyek aktif**: proyek/task/timesheet yang terlihat dibatasi ke proyek yang ia pimpin (`projects_projects.team_leader_employee_id`) lewat lapisan scope §4 — bukan sekadar permission. |
| **Staff** | 22 | `exact` saja | Self-service: clock absensi, ajukan/kelola cuti sendiri (My Time Off & My Allocation), lihat evaluasi diri, lihat proyek & task, isi/update timesheet, update task. Tanpa dashboard evaluasi perusahaan dan tanpa data HR orang lain. |
| **Karyawan** *(custom)* | 12 | Diatur manual via UI | Role buatan di luar baseline — **tidak diubah oleh sync**. Isinya tanggung jawab Admin; pertimbangkan konsolidasi ke **Staff** agar tidak ada dua role sefungsi. |

Setelah baseline diterapkan, permission **admin-only** (Audit Trail, Backup DB, Blocked IPs, dsb.) secara tegas **dicabut** dari semua role non-Admin.

## 4. Permission saja tidak cukup: lapisan scope

Memiliki permission `view/update` tidak otomatis berarti bisa mengakses semua record. Lapisan tambahan yang berlaku:

| Lapisan | Cara kerja |
| :--- | :--- |
| **Resource Permission (Global / Group / Individual)** | Pada resource ber-policy: Global = semua record; Group = record milik tim/subordinat; Individual = record sendiri. Diterapkan antara lain lewat `EmployeePolicy` + trait `HasScopedPermissions`. |
| **OperationalScope (OM)** | Untuk Operation Manager: daftar & profil karyawan dibatasi ke karyawan pada proyek yang ia ikuti + diri sendiri + bawahan langsung + karyawan yang ia buat. **Edit/hapus lebih sempit lagi**: hanya karyawan pada proyek di mana ia **Project Manager** (`projects_projects.user_id`) + karyawan buatannya. Di luar scope: record tidak terlihat (URL langsung = 404/403). |
| **Pembatasan hapus OM** | OM hanya `delete` per record (soft-delete). `delete_any`, `force_delete*`, `restore*` untuk karyawan **tidak diberikan** — pemulihan & penghapusan permanen tetap wewenang Admin/HRD. |
| **Team Leader scope** | User ber-role Team Leader (tanpa role OM/Admin/HRD) hanya melihat **satu proyek yang ia pimpin**: daftar proyek, task, timesheet, widget operasional, dan data karyawan terscope ke proyek tersebut (`OperationalScope::teamLeaderProjectIds`). Satu karyawan hanya boleh memimpin **satu proyek aktif** — ditegakkan validasi model + partial unique index Postgres. Team Leader otomatis menjadi anggota proyeknya. Struktur pimpinan proyek terlihat di tab **Struktur Organisasi** pada detail proyek (PM → Team Leader → Anggota). |
| **Penghapusan role berpelindung** | Role baseline (Admin, HRD, Operation Manager, Accounting, Kasir, Staff, Team Leader guard `web`) **tidak bisa dihapus** dari UI; role yang masih dipakai user juga ditolak untuk dihapus. Modal konfirmasi menampilkan guard, jumlah permission, dan jumlah user terkait. |

## 5. Tata kelola perubahan

1. **Ubah baseline lewat kode, bukan UI.** Edit `RolePermissionSynchronizer`, commit, deploy, lalu jalankan sync. Mengedit role bawaan lewat UI akan **tertulis ulang** pada sync berikutnya.
2. **Sinkronisasi:** `sudo -u www-data php artisan security:sync-rbac --shield` (flag `--shield` men-generate permission Shield lebih dulu). Sync memakai `syncPermissions` untuk role non-Admin — grant basi/non-baseline hilang.
3. **Role custom** (dibuat via UI, contoh: Karyawan) tidak disentuh synchronizer; isinya dikelola manual dan menjadi tanggung jawab Admin.
4. **Default user baru = Staff.** Nilai `general.default_role_id` diarahkan ke Staff (migrasi settings `2026_10_20_120000_set_default_user_role_to_staff`); kenaikan role dilakukan manual via **Pengaturan → Users** setelah penempatan jelas.
5. **Guard `sanctum` wajib terdaftar** di `config/auth.php` (provider `users`). Role/permission berguard sanctum yang tidak terdaftar model user-nya akan gagal dihapus (error Spatie) — gejala di UI: toast “Error while loading page”.
6. **Setelah sync, user logout/login ulang** agar cache permission Spatie segar.
7. Perubahan role tercatat di **Audit Trail** (observer role/user) untuk kebutuhan penelusuran.

## 6. Cara verifikasi cepat

| Kebutuhan | Cara |
| :--- | :--- |
| Jumlah permission per role | Daftar **Pengaturan → Roles**, kolom **Permissions** |
| Ringkasan lintas role tanpa scroll panjang | **Pengaturan → Ringkasan Role** (`/admin/role-summary`, khusus Admin): kartu per role (badge Baseline/Custom, progress bar %, breakdown Resources/Pages/Widgets/Lainnya yang selalu klop ke total, daftar user pemilik role, link Edit), klik beberapa kartu untuk **membandingkan role** berdampingan, filter **Hanya tampilkan perbedaan**, grup plugin bisa collapse, klik item untuk nama permission, pencarian permission |
| Rincian centang per resource/halaman/widget | **Roles → Edit**, section Resources / Pages / Widgets |
| Nama permission persis (teks) | Perintah tinker di §1 |
| Role terpakai oleh siapa | Modal Delete menampilkan jumlah user; atau cek via **Users** |
| Baseline sesuai kode atau belum | Jalankan sync (§5.2), lalu bandingkan angka & centang |
