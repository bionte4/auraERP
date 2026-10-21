# WORKING INSTRUCTION (WI) — AURA ERP

Dokumen ini berisi panduan operasional lengkap untuk seluruh modul kustom, sistem hak akses berjenjang (**RBAC & Struktur Organisasi**), **Evaluasi Kinerja PKWT & Outtask/Outsource**, **Dashboard Evaluasi**, **Point of Sale (POS)**, **Manajemen Proyek & Penugasan Karyawan**, **Integrasi (SMTP / auraAI / Jira)**, **Manajemen Aset**, serta **auraAI** di **auraERP** (`https://opsora.my.id` / `https://auraerp.my.id`).

**Dokumentasi produk:** [docs/README.md](docs/README.md) · PRD [docs/PRD-auraERP.md](docs/PRD-auraERP.md) · ERD [docs/ERD-auraERP.md](docs/ERD-auraERP.md) · Schema [docs/DATABASE-SCHEMA.md](docs/DATABASE-SCHEMA.md)

---

## 1. Akun Login, Peran Standar (RBAC) & Level Akses Struktur Organisasi

Halaman login Admin Panel: **`/admin/login`**

### A. 8 Role Standar Sistem (`php artisan security:sync-rbac`)

Perintah `security:sync-rbac` **menyetel ulang** permission role non-Admin dengan `syncPermissions` (bukan hanya menambah). Grant manual di luar baseline **akan hilang** setelah sync. Role **Admin** tetap mendapat seluruh permission.

| Role | Hak Akses Utama |
| :--- | :--- |
| **Admin** | Akses penuh ke seluruh modul, pengaturan perusahaan, manajemen User, Role & Permission, **Audit Trail**, **Backup Database**, **IP Diblokir** (khusus Admin), dan instalasi plugin. |
| **HRD** | Karyawan, Kontrak, Departemen, Lokasi Kerja (Geofence), Absensi, Cuti/Izin (`Time Off`), Rencana Kinerja, Evaluasi, **Dashboard Evaluasi**, Rekrutmen, **Laporan Ops Mingguan** (internal), widget kehadiran. **Tidak** mendapat: Customer Project Weekly Report, widget aksi OM, Sales/Purchase/Inventory, Maintenance/Aset. |
| **Operation Manager** | Proyek (`Projects`), **Teams** (lihat/buat/ubah tim proyek; tanpa hak hapus tim), **Tugaskan Karyawan**, Tasks, Timesheets, Mitra (`Partners`), **Laporan Ops Mingguan**, **Laporan Mingguan Proyek untuk Klien** (Excel), Dashboard Proyek + widget OM, Absensi/Cuti untuk approval tim, **Penilaian Kinerja (L1/L2)** + **Dashboard Evaluasi**. **Karyawan di proyek yang ia tangani** (tambah/edit/hapus per record — hapus hanya soft-delete, tanpa hapus massal/permanen — tetapi daftar & akses record karyawan otomatis terscope: **lihat** = anggota proyeknya + dirinya + bawahan langsung + karyawan buatannya, **edit/hapus hanya** di proyek di mana ia **Project Manager** (+ karyawan buatannya); di proyek di mana ia hanya anggota, karyawan hanya bisa dilihat). **Daftar Projects & Tasks juga hanya menampilkan proyek yang ditugaskan kepadanya** (sebagai PM atau anggota via karyawan tertaut) — OM tanpa penugasan melihat daftar kosong; kartu Team Board mengikuti scope yang sama. Accounting & Admin/HRD tetap melihat seluruh daftar proyek. **Tidak** mendapat modul penuh Sales, Purchase, atau Inventory (itu domain Admin/Accounting sesuai kebutuhan). |
| **Accounting Manager** | Seluruh hak Accounting **termasuk menghapus** invoice pelanggan, bill vendor, payment & jurnal akuntansi (delete/restore/force-delete), serta **Kontrol Anggaran Proyek se-perusahaan** (tanpa scope tim) dan **Dashboard → Hari Saya** bundle accounting penuh. Role ini untuk manajer akunting — berikan lewat **Settings → Users** setelah deploy; user Accounting lama **tidak** otomatis naik ke role ini. |
| **Accounting** *(staff)* | Invoice pelanggan, bill vendor, payment, partners/products terkait — **buat & ubah saja, tidak bisa menghapus** dokumen keuangan (hapus/restore/force-delete khusus Accounting Manager). **Dashboard → Hari Saya** dengan kontrol anggaran tim (invoice terbuka, overdue AR/AP, payment pending, budget proyek tim) dan halaman **Project → Kontrol Anggaran Proyek** terbatas pada proyek timnya. |
| **Kasir** | Mengoperasikan **POS Terminal** (`/admin/pos-terminal`), membuka/menutup sesi shift kasir (`POS Sessions`), membuat transaksi penjualan kasir (`POS Orders`), melihat katalog produk & pelanggan, serta melakukan absensi (`/admin/absen`). |
| **Team Leader** | Self-service seperti Staff + lihat karyawan & departemen + Project Dashboard. **Hanya melihat satu proyek yang ia pimpin** (`Team Leader` di form proyek, berbasis karyawan): daftar proyek, task, timesheet, dan widget operasional terscope ke proyek tersebut. Satu karyawan hanya boleh memimpin **satu proyek aktif**; leader otomatis menjadi anggota proyek. Tidak dapat membuat/mengubah proyek atau mengelola karyawan (bukan approver). Struktur pimpinan terlihat di tab **Struktur Organisasi** pada detail proyek (PM → Team Leader → Anggota). |
| **Staff (Karyawan)** | Daftar **Proyek & Task hanya menampilkan proyek tempat ia ditugaskan** (anggota/PM). Self-service: Absensi, Cuti/Izin pribadi, Timesheet & update tugas proyek, melihat Performance Plan & Evaluation miliknya sendiri, Hari Saya / Team Board. **Tidak** akses **Dashboard Evaluasi** agregat maupun `view_any_evaluation`. |

### B. 3 Tingkatan Scope Data (`Resource Permission` pada Menu Settings → Users)
Saat membuat atau mengedit User di **Settings → Users**, terdapat pengaturan **Resource Permission**:
1. **`Global`**: User dapat melihat dan mengelola seluruh data di perusahaan sesuai izin modulnya (cocok untuk Direksi, HRD Pusat, Admin).
2. **`Group` (Tim & Hirarki Struktur Organisasi)**: User otomatis dapat mengakses data milik:
   - Dirinya sendiri,
   - Rekan satu tim (`Teams`), **DAN**
   - **Seluruh bawahan langsung (Level 1) maupun bawahan tidak langsung (Level 2 dst.)** berdasarkan pohon struktur organisasi karyawan (`Manager / parent_id` serta `Department Manager`).
3. **`Individual`**: User hanya dapat melihat dan mengelola data miliknya sendiri (*Self-Service*).

> **Default user baru = `Individual`** (hak terkecil). Naikkan ke `Group`/`Global` secara sadar lewat **Settings → Users** sesuai peran (mis. OM lazimnya `Group`, HRD/Admin `Global`). **Audit user lama:** user yang terlanjur `Global` padahal bukan Admin/HRD tetap melihat absensi/evaluasi semua orang — tinjau dengan perintah: `php artisan tinker --execute='print_r(\Webkul\Security\Models\User::where("resource_permission", "global")->pluck("email")->toArray());'` lalu turunkan yang tidak perlu dari halaman Users.

### C. Mengelola Anggota Tim (`Settings → Teams`) & Papan Manage Teams
> **Scope Operation Manager:** daftar **Teams** untuk OM hanya menampilkan tim dari proyek yang di-assign kepadanya (proyek yang ia kelola sebagai PM + proyek yang ia ikuti sebagai anggota + tim buatannya + tim tempat ia anggota). **Mengubah anggota tim** lebih sempit lagi: hanya tim proyek di mana ia **Project Manager** atau tim buatannya sendiri — tim proyek orang lain tidak tampil dan tidak bisa diubah (URL langsung ditolak). Admin & HRD melihat semua tim. Tim lama yang belum tertaut (`project_id` kosong) dicocokkan berdasarkan **nama yang sama persis dengan nama proyek** — jadi PM tetap bisa mengedit tim proyeknya; tim bernama kembar sebaiknya digabung Admin menjadi satu agar tidak ambigu.
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
   - **Pilihan tampilan (layout)** di header papan: **Cards** (default, kartu penuh), **Compact** (kartu ringkas — muat lebih banyak tim per layar, tanpa blok *About team*), dan **List** (satu baris per tim dalam tabel: Tim, Anggota, Proyek, Task, Nilai (hanya untuk yang berhak melihat nilai kontrak), Status, Diperbarui, Details). Pilihan tersimpan otomatis per sesi login. **Project kanban** (Admin/OM) tetap tersedia dan tidak berubah.
   - **Pencarian** di header memfilter kartu menurut **nama tim atau nama proyek** (ketik langsung, hasil terfilter live) — berguna saat jumlah tim/proyek sudah banyak.
   - **Sortir (Urutkan)** di header mengurutkan kartu/baris di semua mode tampilan: **Nama A–Z / Z–A** (default A–Z), **Anggota terbanyak**, **Proyek terbanyak**, **Task terbanyak**, **Progress tertinggi / terendah**, **Terakhir diperbarui**, dan **Nilai terbesar** (opsi nilai hanya muncul bagi yang berhak melihat nilai kontrak). Pilihan sortir juga tersimpan per sesi, dan berlaku bersamaan dengan pencarian.
6. **Menghapus tim:** Team Board tidak punya aksi hapus (hanya *Details* ke Settings → Teams). Di **Settings → Teams**, tombol **Delete** selalu tampil bagi yang berhak (`delete_security_team`, praktisnya Admin); bila tim masih punya anggota, modal konfirmasi menyebutkan jumlah anggotanya — anggota dilepas dari tim dulu, lalu tim dihapus, dalam satu alur. Tim janggal (mis. bernama orang dan tidak tertaut proyek — tim resmi sistem selalu bernama sama dengan proyeknya) bersihkan dengan cara ini; tidak perlu mengosongkan anggota manual lebih dulu.

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
VPS menarik dari fork `bionte4/auraERP` (branch `master`) karena akses write ke `aureuserp/aureuserp` terbatas. Lihat juga **§12** untuk blok lengkap.
```bash
cd /var/www/auraerp
git remote add fork https://github.com/bionte4/auraERP.git 2>/dev/null || true
git fetch fork && git merge fork/master
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan security:sync-rbac
sudo -u www-data php artisan optimize:clear
```
**Penting:** jalankan Artisan sebagai `www-data`. Jika muncul `Permission denied` pada `storage/logs` atau `bootstrap/cache`, perbaiki ownership lalu ulangi (lihat §12).

### H. Akun Demo
- Demo:
  - HRD: `hrd@opsora.my.id` / `Demo-hrd-2026`
  - Karyawan: `karyawan@opsora.my.id` / `Demo-karyawan-2026`
  - Accounting: `accounting@opsora.my.id` / `Demo-accounting-2026`
- Seed/ulang akun demo: `sudo -u www-data php artisan db:seed --class="Webkul\\Security\\Database\\Seeders\\DemoUserSeeder" --force --no-interaction`
- Untuk go-live tanpa demo: set `SECURITY_DEMO_ACCOUNTS_ALLOWED=false` di `.env` (login demo ditolak).
- Sync RBAC kapan saja: `sudo -u www-data php artisan security:sync-rbac`.

### I. Mengelola Role & Permission lewat UI (Roles)
> Penjelasan arti angka **Permissions** dan matriks aturan tiap role: [docs/RBAC-PERMISSION-MATRIX.md](docs/RBAC-PERMISSION-MATRIX.md).
- Buka **Pengaturan → Roles** (`/admin/shield/roles`, hanya Admin). Form Create/Edit Role menampilkan 3 section permission (bukan tab): **Resources** (terbuka; grup per plugin bisa di-expand, berisi checkbox per resource), **Pages**, dan **Widgets** (default ter-collapse). Toggle **Select All** mencentang semua permission yang tampil. Klik **Save**.
- Menetapkan role ke user: **Pengaturan → Users** (`/admin/users`) → edit user → pilih role.
- **Melihat ringkasan tanpa scroll panjang:** **Pengaturan → Ringkasan Role** (`/admin/role-summary`, khusus Admin) — kartu total per role + matriks `tercentang/total` per grup plugin + detail nama permission (klik item) + pencarian permission. Halaman ini hanya-baca; pengubahan tetap di **Roles**.
- Aplikasi dilayani dua domain (`auraerp.my.id` & `opsora.my.id`): asset Filament (JS/CSS/font) **harus same-origin dengan domain yang dibuka**. `asset()` memprioritaskan `config('app.asset_url')` (fallback `APP_URL`) di atas `forceRootUrl`, jadi `AppServiceProvider` mematok keduanya (`forceRootUrl` + `useAssetOrigin`) ke host request. Bila asset termuat lintas domain, browser memblokirnya via CORS → checkbox list/tab Filament tidak terinisialisasi (centang tidak tampil, tab kosong, toast *"Error while loading page"*) walau data di database benar.
- Guard `sanctum` **wajib terdaftar** di `config/auth.php` (provider `users`): tanpa itu, menghapus role/permission berguard sanctum akan error (*"Class name must be a valid object or a string"*) karena listener Spatie gagal memetakan model user — gejala di UI berupa toast *"Error while loading page"* saat menghapus role.
- **Penghapusan role berpelindung:** role baseline (Admin/HRD/Operation Manager/Accounting/Accounting Manager/Kasir/Staff/Team Leader guard `web`) tidak bisa dihapus (hanya bisa diedit permission-nya); role yang masih dipakai user juga ditolak untuk dihapus — pindahkan dulu user-nya. Modal konfirmasi hapus menampilkan guard, jumlah permission, dan jumlah user terkait agar tidak salah hapus (mis. duplikat Staff guard `sanctum` yang aman dihapus).
- **Default Role user baru = Staff** (prinsip hak paling kecil): diatur di **Pengaturan → Settings → Manage Users** (`/admin/settings/manage-users`); nilai existing yang lebih tinggi (mis. Operation Manager) otomatis dikoreksi ke Staff oleh migrasi settings `2026_10_20_120000_set_default_user_role_to_staff` saat deploy. Naikkan role user secara manual lewat **Users** setelah penempatannya jelas.
- **Aturan main penting:** baseline 8 role standar didefinisikan di kode (`RolePermissionSynchronizer`). Menjalankan `security:sync-rbac` akan **menyetel ulang** permission role bawaan ke baseline — perubahan manual lewat UI pada role bawaan akan hilang. Untuk mengubah aturan role bawaan, ubah kodenya lalu deploy/sync; role **baru/custom** buatan UI tidak disentuh synchronizer.
- Bila suatu saat daftar permission di form Role kosong: generate dulu permission Shield lalu sync: `sudo -u www-data php artisan shield:generate --all --option=permissions --panel=admin` dilanjutkan `sudo -u www-data php artisan security:sync-rbac --shield`, lalu `optimize:clear` + hard refresh.
- **Operation Manager & karyawan:** OM dapat tambah/edit/hapus (soft-delete per record saja, tanpa bulk/force-delete) karyawan, dengan scope: lihat = proyek yang ia ikuti + diri sendiri + bawahan langsung + karyawan buatannya; edit/hapus hanya di proyek di mana ia **Project Manager** (+ karyawan buatannya). Di luar scope, record tidak terlihat (akses URL langsung = 404/403).

### J. Audit & Penautan User OM ↔ Karyawan (Scope Kosong: Dashboard/Teams Kosong)
Scope OM dihitung dari `Project Manager` proyek (berbasis user) **dan** karyawan tertaut (`employees_employees.user_id`) yang di-assign ke proyek. User impor sering belum tertaut ke karyawan (email karyawan hasil impor kerap email pribadi) sehingga dashboard & Teams-nya kosong walau role sudah benar. Panduan audit, penautan massal berbasis email persis, pencarian nama, verifikasi duplikat, hingga penetapan PM: [docs/OM-USER-EMPLOYEE-LINKING.md](docs/OM-USER-EMPLOYEE-LINKING.md).

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
   - **Employment Type (Tipe Karyawan):** Pilih dari master (`Permanent`, `Contract` / PKWT, `Konsultan`, `Freelance`, dll.). Status **Outsource / Outtask** dikenali aplikasi dari nama/kode tipe, job title, atau jenis evaluasi (bukan selalu tipe seed default).
   - **Aturan kebersihan master (wajib):** Dropdown tipe **tidak lagi punya tombol "+"** — tipe baru hanya dibuat lewat **Employees → Configurations → Employment Types** oleh Admin/HRD dengan satu ejaan baku. Sistem menolak nama duplikat walau beda huruf besar/kecil/spasi. Bulk import juga **tidak membuat tipe baru diam-diam**: nama tipe di Excel yang belum ada akan dikosongkan dan dilaporkan dalam notifikasi peringatan setelah import — buat dulu tipenya di Configurations, lalu impor ulang atau isi manual.
   - **Start Join Date (Tanggal Mulai Bergabung):** Pilih tanggal mulai bekerja.
   - **End Contract Date (Tanggal Berakhir Kontrak):** Isi tanggal berakhir kontrak (kosongkan untuk *Permanent*).
2. Klik **Save Changes**.

### C. Bulk Import Karyawan (Excel `.xlsx` / CSV)
1. Buka menu **Employees → Employees** (`/admin/employees/employees`).
2. Klik tombol **`Template Impor Karyawan` (`Employee Import Template`)** di pojok kanan atas untuk mengunduh file `template-import-employees.xlsx`.
3. Isi kolom: `name`, `work_email`, `work_phone`, `mobile_phone`, `job_title`, `department`, `employment_type`, `join_date`, `contract_end_date`, `identification_id`.
4. Klik tombol **`Impor Karyawan Excel / CSV` (`Import Employees Excel / CSV`)**, unggah file yang sudah diisi, lalu klik **Submit**.
5. **Perilaku import = menambah DAN mengganti, kuncinya `work_email`:**

   | Kondisi baris | Perilaku |
   | :--- | :--- |
   | Email sama dengan karyawan aktif yang ada (tidak peka huruf besar/kecil) | **UPDATE**: field yang terisi di file menimpa data lama (nama, telepon, jabatan, departemen, tipe, tanggal bergabung & berakhir kontrak); sel kosong **tidak** menghapus data lama |
   | Email kosong / tidak ditemukan | **CREATE baru** — walau nama sama, menjadi karyawan baru (risiko duplikat bila email tidak konsisten) |
   | Nama kosong | Dilewati (`skipped`) |
   | Tipe karyawan tidak dikenal | Tidak dibuat otomatis — tipe dikosongkan dan dilaporkan pada notifikasi peringatan setelah import (lihat §2.B) |
   | Departemen tidak dikenal | Dibuat otomatis dengan nama sesuai file |

   Hasil import (jumlah `created` / `updated` / `skipped`) tampil pada notifikasi. Untuk update massal (mis. perpanjang tanggal kontrak banyak karyawan), pastikan email di file **persis sama** dengan di sistem agar data menimpa, bukan menggandakan.

### C2. Membersihkan Master Employment Type
Daftar Employment Type di semua dropdown (form karyawan, Riwayat Kontrak, Job Position) berasal dari satu master: **Employees → Configurations → Employment Types**. Urutan pembersihan yang aman:

1. **Audit pemakaian dulu** (server) — tipe yang dihapus akan mengosongkan tipe pada karyawan/kontrak/job position yang memakainya (tidak error, tetapi data hilang):

   ```bash
   cd /var/www/auraerp && sudo -u www-data env HOME=/tmp php artisan tinker --execute='$types = \Illuminate\Support\Facades\DB::table("employees_employment_types")->orderBy("name")->get(); foreach ($types as $t) { $e = \Illuminate\Support\Facades\DB::table("employees_employees")->where("employment_type_id", $t->id)->whereNull("deleted_at")->count(); $c = \Illuminate\Support\Facades\DB::table("employees_employee_contracts")->where("employment_type_id", $t->id)->count(); $j = \Illuminate\Support\Facades\DB::table("employees_job_positions")->where("employment_type_id", $t->id)->count(); echo "{$t->id} | {$t->name} | karyawan: {$e} | kontrak: {$c} | job position: {$j}\n"; }'
   ```

2. **Pemakai 0 di semua kolom → hapus langsung** di Configurations → Employment Types (kandidat warisan seeder: `Apprenticeship, Interim, Intern, Seasonal, Statutory, Student, Temporary, Thesis`).
3. **Duplikat yang masih terpakai** (mis. `contract hc` vs `Contract-HC`, `Employee`/`Full-Time` vs `Permanent`): pilih satu nama tujuan, alihkan pemakainya via tinker (ganti `ID_SUMBER` & `ID_TUJUAN` sesuai hasil audit), baru hapus tipe sumbernya di Configurations:

   ```bash
   sudo -u www-data env HOME=/tmp php artisan tinker --execute='\Illuminate\Support\Facades\DB::table("employees_employees")->where("employment_type_id", ID_SUMBER)->update(["employment_type_id" => ID_TUJUAN]); \Illuminate\Support\Facades\DB::table("employees_employee_contracts")->where("employment_type_id", ID_SUMBER)->update(["employment_type_id" => ID_TUJUAN]); \Illuminate\Support\Facades\DB::table("employees_job_positions")->where("employment_type_id", ID_SUMBER)->update(["employment_type_id" => ID_TUJUAN]); echo "dialihkan";'
   ```

4. **Target akhir yang disarankan:** `Permanent` (tujuan gabungan `Employee`/`Full-Time`), `Contract`, `Contract-HC`, `Contract - Non HC` (pertahankan bila HC/Non-HC memang pembeda bisnis), `OutTask Project`, `Outsource` (buat baru bila belum ada), `Konsultan`, `Freelance` — dari ±20 nama menjadi ±8. Pencegahan duplikat baru sudah diatur sistem (lihat §2.B).

### D. Menugaskan Proyek dari Profil Karyawan (`Assigned Projects`)
1. Buka **Employees → Employees** → pilih karyawan → pastikan **Pengguna Terkait (`Related User`)** sudah diisi bila karyawan perlu login/akses proyek.
2. Buka tab **Proyek Ditugaskan (`Assigned Projects`)** (`/admin/employees/employees/{id}/assigned-projects`).
3. Pilih satu atau beberapa **Proyek**, lalu klik **Simpan Penugasan**.
4. Penugasan ini tersinkron dengan layar **Project → Tugaskan Karyawan** (data & followers yang sama). Lihat juga **§7**.

### E. Riwayat Kontrak Karyawan (Kontrak ke-1, ke-2, dst.)
1. Buka profil karyawan → tab **Riwayat Kontrak** (`/{record}/contracts`). Setiap baris adalah satu kontrak: tipe (PKWT/PKWTT/Outsource/Outtask), nomor kontrak, periode, durasi (bulan), status, dan sumber pencatatan.
2. **Kontrak Baru**: tombol *Kontrak Baru* untuk kontrak pertama/baru. **Perpanjang Kontrak**: tombol *Perpanjang Kontrak* — tanggal mulai terisi otomatis sehari setelah kontrak terakhir berakhir; simpan sebagai **Aktif**.
3. Saat kontrak baru berstatus Aktif, kontrak aktif sebelumnya otomatis berubah status menjadi **Diperpanjang**, dan tanggal berakhir + tipe berjalan di profil karyawan tersinkron dari kontrak aktif terbaru.
4. Daftar karyawan menampilkan badge **Kontrak ke-N** (kontrak terakhir) di samping tipe karyawan.
5. Akumulasi masa PKWT dihitung dari seluruh kontrak bertipe PKWT/Contract — dasar pemantauan batas maksimal PKWT **60 bulan** (PP 35/2021); alert ambang batas & estimasi uang kompensasi menyusul di fase berikutnya.
6. Data awal: jalankan `php artisan employees:backfill-contracts` **satu kali** setelah deploy — sistem membuat *Kontrak ke-1* dari Tanggal Bergabung & tanggal berakhir berjalan untuk karyawan yang belum punya riwayat (tidak menggandakan bila dijalankan ulang). Riwayat historis sebelum sistem dipakai dapat dilengkapi manual lewat *Kontrak Baru* (sesuaikan tanggal & statusnya).

### F. Memantau Kontrak yang Akan Habis (Alert 30 Hari) & Approval Cuti Berjenjang (SLA 24 Jam)
- **Dashboard Karyawan (`People Dashboard`):** Buka **Dashboard → Dashboard Karyawan** (`/admin/employees/dashboard`, **Admin/HRD** penuh, **OM** hanya scope timnya; permission `page_employee_people_dashboard` + 3 widget `widget_employee_*`). Filter atas: Departemen / Tipe Karyawan / Lokasi Kerja / Company.
  - **KPI bisa diklik:** Karyawan Aktif, Karyawan Baru 30 hari, Kontrak Berakhir/Lewat (→ tab daftar terkait), Tanpa Related User, Tanpa Proyek.
  - **Tabel Kontrak Mendekati / Melewati Tanggal Berakhir:** karyawan aktif lewat/≤3 bulan dari akhir kontrak, badge sisa hari, filter Expired/≤7/≤30/31–90 hari; **baris bisa diklik** ke Ringkasan 360 (+ aksi View/Edit).
  - **Tabel Perlu Perhatian:** tanpa Related User / departemen / jabatan / atasan / email, atau tanpa proyek; **baris bisa diklik** ke Ringkasan 360.
  - Daftar **Employees** tetap halaman direktori/CRUD — dashboard ini untuk monitoring, bukan pengganti daftar.
- **Sidebar & Badge Navigasi:** Sidebar desktop selalu tampil expanded dengan label (tidak diciutkan jadi rail ikon; di HP tetap drawer). Urutan grup mengikuti alur kerja harian: Dashboard → Karyawan → Proyek → Cuti → Rekrutmen → Kontak/Sales/Purchase/Inventory/dll → Plugin/Pengaturan/Bantuan. Badge angka hanya pada item actionable (tersembunyi bila 0, maks `99+`, cache 60 detik, hover untuk keterangan): **Proyek** = proyek aktif lewat tenggat/berakhir ≤90 hari (merah bila ada yang lewat tenggat), **Karyawan** = kontrak lewat/berakhir ≤30 hari, **Time Off** = pengajuan menunggu approval, **Tugaskan Karyawan** = karyawan aktif tanpa Related User.
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
2. **Akses terbatas:** hanya **Admin**, **HRD**, dan **Operation Manager** (hard-check role + permission `page_evaluation_performance_evaluation_dashboard`). Staff/Kasir/Accounting ditolak meskipun punya grant evaluasi perorangan.
3. Fitur pada Dashboard Evaluasi:
   - **Filter Real-Time:** Filter berdasarkan *Jenis / Periode Evaluasi*, *Status Evaluasi*, *Departemen*, dan *Karyawan*.
   - **4 Kartu KPI:** *Total Evaluasi (PKWT vs Outsource)*, *Status Evaluasi Berjalan (`In Progress` vs `Completed`)*, *Rata-rata Poin D (Manajemen Kerja)*, dan *Kelayakan Pengangkatan Tetap*.
   - **2 Grafik Visual:** Grafik Batang *Distribusi Jenis & Periode Evaluasi* dan Grafik Doughnut *Komposisi Rekomendasi Keputusan (Poin I)*.
   - **Tabel Monitoring Approval Berjenjang (`L1 -> Karyawan -> L2`)** dilengkapi tombol hijau **`Download Excel (.xlsx)`** untuk mengunduh rekapitulasi lengkap 22 kolom dalam format Microsoft Excel.
   - **Scope OM:** untuk Operation Manager seluruh angka/grafik/tabel hanya menghitung karyawan dalam scope operasionalnya (anggota proyeknya + rantai approval L1/L2-nya) — termasuk hitungan *Program Goal* pada kartu Kelayakan; Admin & HRD melihat semua. Bila muncul toast *Error while loading page* khusus untuk OM, itu gejala regresi scope pada widget statistik (query rencana kinerja tidak boleh memakai kolom approval khas tabel evaluasi) — sudah diperbaiki; laporkan bila berulang setelah deploy terbaru.

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
   - **Bukan** untuk mengatur siapa **Manajer Proyek** — itu di langkah 3.
   - **Dari Proyek:** menu **Project → Tugaskan Karyawan (`Assign Employees`)** (`/admin/project/assign-employees`) → pilih **Proyek**, lalu pilih satu atau beberapa **Karyawan**, klik **Simpan Penugasan**.
   - **Dari Karyawan:** buka **Employees → Employees** → pilih karyawan → tab **Proyek Ditugaskan (`Assigned Projects`)** → pilih proyek → **Simpan Penugasan**.
   - **Unassign karyawan:** di layar yang sama, **hilangkan centang** karyawan / proyek yang tidak lagi ditugaskan → **Simpan Penugasan**. Melepas karyawan dari proyek juga melepaskan mereka dari task terbuka proyek tersebut (lihat langkah 5).
   - Kedua cara memakai data penugasan yang sama, dan otomatis menambahkan karyawan yang punya partner/user terkait sebagai **pengikut (followers)** agar mendapat akses proyek.
   - **Prasyarat akses login:** Pastikan karyawan punya **Pengguna Terkait (`Related User`)**. Tanpa ini, penugasan tetap tersimpan, tetapi karyawan belum bisa login/mengakses proyek.
   - Layar Project Assign: permission `page_project_assign_project_employees`. Tab di Employee mengikuti izin update Employee (plugin Projects harus terpasang).
3. **Assign / Unassign Manajer Proyek (`Project Manager`) — satu orang per proyek:**
   - Field di form proyek: **Manajer Proyek (`Project Manager`)** → kolom DB `projects_projects.user_id` (relasi ke **User**, biasanya user dengan role **Operation Manager**).
   - **Cara massal (disarankan):** **Project → Atur Project Manager** (`/admin/project/assign-project-managers`, khusus Admin; permission `page_project_assign_project_managers`) — semua proyek dalam satu tabel, ganti PM lewat dropdown per baris (pilihan hanya user berperan **Operation Manager**), langsung tersimpan dan tersinkron ke tim proyek. Chip di atas tabel menampilkan jumlah proyek per OM; filter **Belum ada manajer** + badge menu (jumlah proyek tanpa PM) memudahkan memburu proyek yatim. Mengganti/mengosongkan PM tidak menghapus PM lama dari anggota tim.
   - **Bulk via Excel/CSV (di halaman yang sama):** tombol **Template Project Manager** menghasilkan `template-assign-project-managers.xlsx` yang **sudah terisi semua proyek + email PM saat ini** (sheet kedua: daftar email OM yang valid). Ubah kolom `new_manager_email` (kosongkan = lepas PM), lalu **Impor Excel / CSV**. (Jangan tertukar dengan tombol **Template Impor Proyek** di halaman daftar Projects — itu template impor proyek umum.) Proyek dicocokkan via `project_id` (cadangan: nama persis), manajer via email dan wajib berperan Operation Manager; hasil impor menampilkan hitungan diperbarui/tidak berubah/dilewati beserta daftar baris bermasalah.
   - **Assign / ganti:**
     1. Buka **Project → Projects** (`/admin/project/projects`) → Edit proyek.
     2. Di bagian **Additional / tambahan**, pilih user pada **Manajer Proyek**.
     3. **Save**. User tersebut otomatis masuk anggota Tim proyek (sinkron saat simpan proyek).
   - **Unassign (lepaskan):**
     1. Edit proyek yang sama.
     2. **Kosongkan** field **Manajer Proyek** (clear), atau ganti ke user lain.
     3. **Save**. Hanya boleh ada **satu** manajer; mengganti user = melepas manajer lama.
   - **Cek cepat:** **Project → Dashboard** (`/admin/project`) → widget **Operation Manager — Proyek sebagai Manajer Proyek** (OM + jumlah proyek aktif sebagai manajer).
   - **Bedakan:** Manajer Proyek ≠ daftar karyawan assign (langkah 2) ≠ **Assignees** di Task.
4. **Penugasan kerja harian lewat Task:**
   - **Project → Tasks** → isi **Penerima Tugas (`Assignees`)** (daftar **User**).
   - Hak akses juga dipengaruhi **Visibilitas (`Visibility`)** proyek.
5. **Dashboard beban kerja Operation Manager (`Project → Dashboard`):**
   - Buka **Project → Dashboard** (`/admin/project`).
   - Widget **Item aksi OM** (scope OM/Admin/HRD): cuti pending, kontrak ≤ 30 hari, tugas terlambat, karyawan ditugaskan tanpa Related User.
   - Widget **Proyek Mendekati / Melewati Tenggat** (Admin/OM, permission `widget_project_expiring_projects_widget`): proyek aktif yang sudah lewat `end_date` atau berakhir dalam 3 bulan, urut paling mendesak. Kolom proyek/customer/PM/tanggal berakhir/badge sisa hari/progress tugas/tugas terlambat; **baris & nama proyek bisa diklik** ke View (ada juga aksi View/Edit), filter rentang Expired/≤30/31–60/61–90 hari, mengikuti filter atas Project & Customer.
   - Widget **Kehadiran hari ini**: hadir / terlambat / belum absen (scope).
   - Widget **Operation Manager — Proyek sebagai Manajer Proyek** menampilkan OM + jumlah proyek aktif sebagai **Manajer Proyek** (hasil assign di langkah 3).
   - Widget **Anggaran Operasional & Realisasi Opex (proyek saya)** (OM & Team Leader, permission `widget_project_operational_budget_widget`): per proyek yang dikelola/dipimpin tampil **Anggaran Operasional · Biaya Vendor (aktual) · Sisa Anggaran · Serapan % + status** dari bill vendor *posted* yang terkait proyek. Widget ini sengaja **tanpa nilai kontrak dan tanpa data invoice** — nilai kontrak adalah data finansial khusus Admin & tim budget-control (lihat poin 10).
   - Widget **Tanggung Jawab Proyek & Team Leader** (Admin + **Operation Manager & Team Leader**, permission `widget_project_project_responsibility_widget`): satu baris per proyek aktif — **Proyek · Customer · Project Manager · Team Leader · Jumlah Anggota · Progress Tugas (% selesai/total) · Tugas Terlambat (badge merah) · Sisa Waktu ke end date**; baris dapat diklik ke detail proyek dan mengikuti filter atas Project Dashboard. Scope otomatis: OM melihat proyek yang dikelola/diikutinya, **Team Leader hanya proyek yang dipimpinnya**, Admin semua. Sama seperti widget opex, widget ini **tanpa nilai kontrak** — murni tanggung jawab & kesehatan proyek.
   - **Dashboard → Hari Saya** (`/admin/my-day`) — kartu fokus harian per peran (Admin/HRD/OM/Staff/Kasir).
   - **Laporan Ops Mingguan** (`/admin/weekly-operations-report`) — metrik internal + beban jam/proyek per karyawan + unduh Excel. Permission: `page_project_weekly_operations_report` (**Admin / HRD / OM**; data mengikuti `OperationalScope`).
   - **Laporan Mingguan Proyek untuk Klien** (`/admin/customer-project-weekly-report`) — Excel siap klien: pilih **1 proyek**, rentang tanggal kustom, **preview di layar**, lalu unduh. Judul task overdue/masking diterapkan; jam kerja disembunyikan. Permission: `page_project_customer_project_weekly_report` (**Admin / OM**; **bukan** HRD).
   - Daftar **Projects**: kolom **Kesehatan** (progress task, anggota, overdue, sisa hari).
   - **Tugaskan Karyawan**: opsi karyawan menampilkan kapasitas (`N proyek · X jam/minggu`); tombol **Buat Related User yang hilang**. Checkbox penugasan disinkronkan Livewire saat ganti proyek.
   - **Employee → Ringkasan 360** (`…/overview`) — proyek, kapasitas, cuti, aset, evaluasi, buat Related User 1-klik.
   - **Team Board** (`/admin/team-board`): pilihan tampilan **Kartu Tim / Ringkas / Daftar** + **pencarian** nama tim/proyek + **sortir** (nama, anggota, proyek, task, progress, nilai, terakhir diperbarui) yang tersimpan per sesi, plus mode **Kanban Proyek** (seret karyawan antar proyek) untuk Admin/OM — rinciannya di Bagian 1.C poin 5.
   - Permission terkait (setelah `security:sync-rbac`): `page_support_my_day_dashboard`, `page_project_weekly_operations_report`, `page_project_customer_project_weekly_report`, `widget_project_attendance_presence_widget`, widget OM.
6. **Link Tim ↔ Proyek eksplisit:** di **Settings → Teams** pilih **Proyek terkait**. Sinkron anggota & Team Board mengutamakan link ini; jika kosong, fallback cocok nama. Melepas karyawan dari proyek juga melepaskan mereka dari task terbuka proyek tersebut.
7. **Reminder Berjenjang 3, 2, dan 1 Bulan Sebelum `End Date` Proyek:**
   - Sistem memberi badge peringatan dan notifikasi otomatis pada sisa waktu `61–90 hari` (**3 Bulan**), `31–60 hari` (**2 Bulan**), `0–30 hari` (**1 Bulan**), dan saat **Berakhir** (`php artisan projects:notify-expiring-projects`).
8. **Bulk Import Proyek:**
   - Klik **`Template Impor Proyek`** (`template-import-projects.xlsx`) di halaman **Projects**, lalu unggah melalui tombol **`Impor Proyek Excel / CSV`**.
9. **Kontrol Anggaran Proyek (tim Accounting — Manajer, Staff & Admin):**
   - Buka **Project → Kontrol Anggaran Proyek** (`/admin/project/budget-control`; permission `page_project_project_budget_control`): per proyek tampil **Nilai Kontrak**, **Terinvoice** & **Belum terbayar** (invoice pelanggan *posted*), **Anggaran Operasional** (field proyek `operational_cost`), **Biaya Vendor (realisasi)** (bill vendor *posted*), **Sisa Anggaran** = anggaran − biaya vendor (merah bila negatif), dan **Serapan** = biaya vendor ÷ anggaran (selalu dihitung sistem, tidak dapat diedit manual — yang diedit hanya inputnya: anggaran & pengaitan bill). Kolom jam timesheet sengaja tidak ditampilkan di halaman keuangan ini (metrik operasional itu tempatnya di Project Dashboard/Laporan Ops). Status otomatis: **Aman** (< 80%), **Waspada ≥ 80%**, **Over budget** (≥ 100%, atau ada biaya tetapi anggaran belum diisi), **Belum ada anggaran**. Tersedia pencarian, filter status, kartu ringkasan total, dan tombol **Ekspor Excel** (`project-budget-control.xlsx`).
   - **Prasyarat data:** invoice pelanggan & bill vendor harus **dikaitkan ke proyek** lewat field **Proyek** pada form invoice/bill (muncul setelah kolom Partner; pilihan invoice mengikuti customer proyek). Dokumen lama yang belum dikaitkan tidak ikut terhitung — kaitkan lewat Edit dokumen (dokumen *posted/cancel* tidak bisa diubah; kaitkan dokumen sejak dibuat ke depan).
   - **Scope:** Admin & **Accounting Manager** melihat seluruh perusahaan; **Accounting (staff)** hanya proyek timnya (scope yang sama dengan Hari Saya); saat staff belum tergabung tim mana pun, halaman mengikuti fallback Hari Saya (seluruh perusahaan).
   - **Daftar Projects/Tasks untuk role finance selalu penuh:** Accounting & Accounting Manager melihat seluruh daftar proyek/task terlepas dari Resource Permission (`OperationalScope` mengenali kedua role; global scope kepemilikan/`OwnershipScope` dilonggarkan khusus untuk role full-list Admin/HRD/finance di kedua resource). Jadi Accounting Manager **tidak wajib** ber-Resource Permission `Global` dan **tidak wajib** punya record karyawan tertaut hanya untuk mengontrol anggaran. Troubleshooting bila daftar tetap kosong/menu belum muncul: **logout/login ulang** setelah perubahan role (cache permission sesi), dan ingat tab default daftar Projects adalah **My Projects** (hanya proyek yang dipimpin user) — buka tab **Customer Projects** untuk melihat semuanya.
   - **Pembagian tugas yang disarankan:** staff accounting membuat/mengubah invoice & bill serta mengaitkannya ke proyek (tidak bisa menghapus dokumen keuangan); **Accounting Manager** yang memegang hak hapus dokumen keuangan dan memantau proyek berstatus Waspada/Over budget untuk eskalasi ke Operation Manager.
   - **Mengatur anggaran operasional:** halaman ini pada dasarnya read-only, tetapi **Admin & Accounting Manager** dapat mengisi/mengubah **Anggaran Operasional** tiap proyek langsung dari tabelnya (tombol **Atur Anggaran/Ubah** di kolom Anggaran Operasional → isi nominal → **Simpan**). Yang diubah hanya angka `operational_cost` — nilai kontrak, tanggal, Project Manager, dan field proyek lain tetap wewenang Admin/OM lewat Edit Project, dan Accounting staff tidak mendapat tombol ini (anggaran adalah keputusan manajerial). Karena itu role Accounting/Accounting Manager sengaja **tidak** diberi `update_project_project`.
   - **Mengatur nilai kontrak:** dengan cara yang sama, **Admin & Accounting Manager** dapat mengisi/mengubah **Nilai Kontrak** tiap proyek dari kolom Nilai Kontrak (tombol **Atur Nilai/Ubah**). Perubahan tercatat di activity log proyek (`project_value` atribut ter-log) sehingga dapat diaudit; Accounting staff hanya melihat, tidak mengubah nilai kontrak.
10. **Kerahasiaan Nilai Kontrak Proyek (kebijakan perusahaan):**
   - **Nilai kontrak (`project_value`) hanya boleh diketahui Admin dan tim budget-control (Accounting & Accounting Manager)** — diatur satu gerbang pusat `OperationalScope::canViewProjectValue()`. Role operasional (Operation Manager, Team Leader, Staff, HRD, Kasir) **tidak melihat nilai kontrak di mana pun**: badge & filter nilai di daftar Projects, field nilai di View/Edit Project (field disembunyikan; OM yang mengedit proyek tidak mengubah nilai tersimpan), total & daftar nilai di detail Customer, agregat nilai di kartu Team Board, Ringkasan 360 karyawan, notifikasi pengingat proyek berakhir (isi notifikasi tanpa nilai untuk non-finance), konteks AI auraAI, dan API proyek (field `project_value` tidak dikirim).
   - Sebagai gantinya OM/Team Leader memantau **anggaran operasional, realisasi biaya vendor, sisa anggaran, dan serapan** lewat widget di poin 5 — cukup untuk mengendalikan pengeluaran opex tanpa mengetahui nilai kontrak/margin proyek.

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
| **Evaluasi Kinerja (Monitoring & Dashboard)** | `/admin/performance-evaluation-dashboard` & `/admin/evaluations/evaluations` | **Export Excel (`Download Excel .xlsx`)** — 22 kolom nilai & status approval berjenjang (Admin/HRD/OM) |
| **Laporan Ops Mingguan (internal)** | `/admin/weekly-operations-report` | Unduh Excel metrik operasional (Admin/HRD/OM) |
| **Laporan Mingguan Proyek (klien)** | `/admin/customer-project-weekly-report` | Preview + Excel siap klien, 1 proyek, rentang tanggal kustom (Admin/OM) |
| **Employees (Karyawan)** | `/admin/employees/employees` | **Template & Bulk Import** (`template-import-employees.xlsx`) |
| **Customers & Vendors** | `/admin/contact/contacts` | **Template & Bulk Import** (`template-import-customers-vendors.xlsx`) |
| **Projects (Proyek)** | `/admin/project/projects` | **Template & Bulk Import** (`template-import-projects.xlsx`) |
| **Equipment (Aset)** | `/admin/maintenance/equipments` | **Template & Bulk Import** (`template-import-equipment-assets.xlsx`) |

---

## 12. Panduan Update / Deploy di Server VPS

Setiap kali ada pembaruan kode dari GitHub fork (`bionte4/auraERP` branch **`master`**), jalankan perintah berikut di VPS (`/var/www/auraerp`, `ubuntu@VM-18-102-ubuntu`).

**Langkah standar:**

```bash
cd /var/www/auraerp && \
sudo chown -R ubuntu:www-data /var/www/auraerp && \
sudo chmod -R ug+rwX storage bootstrap/cache && \
git remote add fork https://github.com/bionte4/auraERP.git 2>/dev/null || true && \
git fetch fork && \
git merge fork/master && \
composer install --no-dev --optimize-autoloader && \
sudo chown -R www-data:www-data storage bootstrap/cache public && \
sudo chmod -R ug+rwx storage bootstrap/cache && \
sudo -u www-data php artisan migrate --force --no-interaction && \
sudo -u www-data php artisan integrations:install --no-interaction && \
sudo -u www-data php artisan projects:sync-team-memberships && \
sudo -u www-data php artisan security:sync-rbac && \
sudo -u www-data php artisan optimize:clear && \
sudo -u www-data php artisan optimize
```

**Jika `filament:upgrade` / Artisan gagal dengan Permission denied:**
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
sudo rm -f storage/logs/laravel.log
sudo -u www-data touch storage/logs/laravel.log
sudo -u www-data php artisan filament:upgrade
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan optimize
```

**Catatan deploy:**
1. Selalu pakai **`sudo -u www-data php artisan …`** agar log & cache tidak jadi milik `ubuntu`/`root` lalu memblokir web/CLI berikutnya.
2. `composer install` memicu `filament:upgrade` (post-autoload-dump). Ownership `storage` + `bootstrap/cache` + `public` harus writable oleh user yang menjalankan Composer/Artisan.
3. `security:sync-rbac` **wajib** setelah perubahan RBAC: menyetel ulang baseline Admin/HRD/OM/Accounting/Accounting Manager/Kasir/Staff/Team Leader dan **mencabut** grant non-baseline (termasuk audit/backup dari non-Admin).
4. `integrations:install` dan `projects:sync-team-memberships` aman dijalankan ulang (idempotent).
5. Setelah deploy RBAC: logout/login ulang di browser, lalu uji singkat:
   - Staff/Kasir **tidak** bisa buka Dashboard Evaluasi
   - HRD **tidak** punya Customer Project Weekly Report / widget aksi OM
   - OM **tidak** punya menu penuh Sales/Purchase/Inventory
   - Admin tetap penuh akses; OM bisa preview+unduh laporan klien
