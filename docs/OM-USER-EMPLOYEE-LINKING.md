# Panduan Audit & Penautan User Operation Manager ↔ Karyawan

## Latar Belakang

Scope operasional seorang Operation Manager (OM) dihitung dari **dua sumber data yang berbeda**:

1. **Proyek yang dipimpin** — kolom `user_id` (Project Manager) pada tabel proyek. Hak ini berbasis **user**, tidak butuh data karyawan.
2. **Keanggotaan proyek** — karyawan yang tertaut ke user (`employees_employees.user_id`) lalu di-assign ke proyek lewat pivot `projects_project_employees`. Hak ini berbasis **karyawan**.

Bila user OM tidak tertaut ke karyawan mana pun **dan** tidak tercatat sebagai Project Manager di proyek mana pun, scopenya kosong total. Gejala khasnya:

| Gejala | Penyebab |
| :--- | :--- |
| Dashboard proyek kosong; widget *Operation Managers* menampilkan namanya dengan **Projects 0** | Bukan PM di proyek mana pun |
| *Attendance today (scope)* = 0 | Karyawan tidak tertaut / tidak ter-assign ke proyek |
| Halaman **Teams** hanya menampilkan tim tempat ia anggota, tombol hanya **View** (tanpa Edit) | Ia hanya anggota tim (user); hak Edit tim hanya untuk PM proyek / pembuat tim |
| Dropdown filter proyek berisi semua proyek pada tab lama | Sesi/tab browser dari sebelum deploy — hard-refresh (Ctrl+Shift+R) atau logout → login |

> Semua perintah di bawah dijalankan di VPS dari `/var/www/auraerp` dengan awalan `sudo -u www-data env HOME=/tmp`.

## Langkah 1 — Audit semua user OM

```bash
cd /var/www/auraerp && sudo -u www-data env HOME=/tmp php artisan tinker --execute='$users = \Webkul\Security\Models\User::role("Operation Manager")->orderBy("name")->get(); echo "Total user Operation Manager: ".$users->count()."\n\n"; echo str_pad("ID",6).str_pad("NAMA",28).str_pad("EMPLOYEE",10).str_pad("PM",5).str_pad("ANGGOTA",9)."KANDIDAT EMP (email sama)\n"; foreach ($users as $u) { $e = \Webkul\Employee\Models\Employee::where("user_id", $u->id)->first(); $pm = \Webkul\Project\Models\Project::where("user_id", $u->id)->count(); $member = $e ? \Illuminate\Support\Facades\DB::table("projects_project_employees")->where("employee_id", $e->id)->count() : 0; $cand = $e ? "-" : (\Webkul\Employee\Models\Employee::where("work_email", $u->email)->whereNull("user_id")->pluck("id")->implode(", ") ?: "-"); echo str_pad($u->id, 6).str_pad(\Illuminate\Support\Str::limit($u->name, 26), 28).str_pad($e ? "#".$e->id : "BELUM", 10).str_pad($pm, 5).str_pad($member, 9).$cand."\n"; }'
```

Cara membaca hasil:

- **EMPLOYEE** — `BELUM` = user belum tertaut ke karyawan; `#123` = id karyawan yang tertaut.
- **PM** — jumlah proyek di mana ia Project Manager (hak kelola berbasis user).
- **ANGGOTA** — jumlah proyek di mana karyawannya ter-assign sebagai anggota.
- **KANDIDAT EMP** — id karyawan yang `work_email`-nya **persis sama** dengan email user tetapi belum tertaut; aman ditautkan massal pada Langkah 2. Boleh berisi lebih dari satu id (kandidat duplikat — verifikasi dulu di Langkah 3).

## Langkah 2 — Penautan massal berbasis email persis

Hanya menautkan bila email user == `work_email` karyawan dan kedua sisi belum tertaut:

```bash
sudo -u www-data env HOME=/tmp php artisan tinker --execute='$n = 0; foreach (\Webkul\Security\Models\User::role("Operation Manager")->get() as $u) { if (\Webkul\Employee\Models\Employee::where("user_id", $u->id)->exists()) { continue; } $e = \Webkul\Employee\Models\Employee::where("work_email", $u->email)->whereNull("user_id")->first(); if ($e) { $e->update(["user_id" => $u->id]); $n++; echo "tertaut: {$u->email} -> employee #{$e->id} ({$e->name})\n"; } } echo "Total tertaut otomatis: {$n}\n";'
```

Bila ada beberapa kandidat untuk satu user, perintah ini mengambil yang pertama — verifikasi dengan Langkah 3 dan pindahkan tautan bila yang terpilih ternyata duplikat kosong.

## Langkah 3 — Verifikasi kandidat duplikat

Ganti `ID_A`/`ID_B` dengan id kandidat dari audit. Bandingkan isi datanya — yang berisi riwayat adalah record yang benar:

```bash
sudo -u www-data env HOME=/tmp php artisan tinker --execute='foreach ([ID_A, ID_B] as $id) { $e = \Webkul\Employee\Models\Employee::find($id); echo "#{$e->id} {$e->name} | email: {$e->work_email} | user_id: ".($e->user_id ?? "NULL")." | dept_id: ".($e->department_id ?? "-")." | job_id: ".($e->job_id ?? "-")." | proyek anggota: ".\Illuminate\Support\Facades\DB::table("projects_project_employees")->where("employee_id", $id)->count()." | kontrak: ".\Webkul\Employee\Models\EmployeeContract::where("employee_id", $id)->count()."\n"; }'
```

Bila tautan terlanjur jatuh ke duplikat yang kosong, pindahkan:

```bash
sudo -u www-data env HOME=/tmp php artisan tinker --execute='\Webkul\Employee\Models\Employee::find(ID_SALAH)->update(["user_id" => null]); \Webkul\Employee\Models\Employee::find(ID_BENAR)->update(["user_id" => USER_ID]); echo "tautan dipindah\n";'
```

> Record duplikat yang kalah **jangan langsung dihapus** — catat untuk pembersihan duplikat karyawan secara terpisah setelah dipastikan tidak terpakai modul lain. Kolom yang lebih lengkap di duplikat (mis. `job_id`) boleh disalin manual ke record utama.

## Langkah 4 — Pencarian berdasarkan nama (kandidat email tidak ketemu)

Data karyawan hasil impor sering memakai email pribadi (Gmail, email klien) sehingga pencocokan email gagal. Cari dengan **kata nama yang khas** (kata kedua/nama belakang — bukan kata umum seperti Muhammad/Agus/Mohamad), tampilkan semua status tautan:

```bash
sudo -u www-data env HOME=/tmp php artisan tinker --execute='foreach (["iskandar", "usman", "nazarudin"] as $kw) { $cands = \Webkul\Employee\Models\Employee::where("name", "ilike", "%".$kw."%")->get(); echo "== ".$kw." ==\n"; foreach ($cands as $c) { echo "   #{$c->id} {$c->name} | {$c->work_email} | user_id: ".($c->user_id ?? "NULL")."\n"; } if ($cands->isEmpty()) { echo "   (tidak ada)\n"; } }'
```

Bila ketemu pasangan yang yakin benar (nama persis sama, `user_id: NULL`), tautkan satu per satu:

```bash
sudo -u www-data env HOME=/tmp php artisan tinker --execute='\Webkul\Employee\Models\Employee::find(EMP_ID)->update(["user_id" => USER_ID]);'
```

> ⚠️ **Jangan menautkan orang yang berbeda** hanya karena nama depan mirip (contoh nyata: user *Mohamad Usman Andi* vs karyawan *USMAN RACHMAN* adalah dua orang berbeda). Bila ragu, konfirmasi ke HRD.

## Langkah 5 — Bila karyawan memang belum ada datanya

Buat lewat UI agar field wajib terisi dengan benar:

1. **Employees → New** — isi **Name** persis nama orangnya, **Work Email** = email user-nya, dan field **User** = user yang bersangkutan → simpan.
2. Departemen & jabatan boleh dilengkapi menyusul oleh HRD.

## Langkah 6 — Tetapkan peran di proyek

Penautan karyawan saja tidak memberi hak kelola. Untuk proyek yang memang dipimpin OM tersebut:

1. Tetapkan sebagai Project Manager — paling cepat secara massal lewat **Project → Atur Project Manager** (`/admin/project/assign-project-managers`, khusus Admin): pilih user OM pada dropdown baris proyeknya, langsung tersimpan dan tersinkron ke tim proyek. Untuk penetapan banyak proyek sekaligus tersedia **Unduh Template** (template Excel terisi semua proyek + email PM saat ini; ubah kolom `new_manager_email`) lalu **Impor Excel / CSV** di halaman yang sama. Cara satuan: **Project → Edit proyek → Project Manager = user yang bersangkutan** → simpan. Seketika proyek muncul di dashboard-nya dan tim proyek menjadi bisa ia edit.
2. Untuk proyek di mana ia hanya anggota: **Project → Assign Employees** → tambahkan karyawannya (hak lihat saja).

## Langkah 7 — Penutup & verifikasi

```bash
sudo -u www-data php artisan projects:sync-team-memberships && sudo -u www-data php artisan optimize:clear
```

1. Jalankan ulang audit Langkah 1 — target: tidak ada lagi `BELUM` pada kolom EMPLOYEE, dan kolom PM/ANGGOTA terisi sesuai penugasan nyata.
2. Minta user terkait **logout → login**, lalu periksa: Dashboard proyek terisi, Teams proyek yang dipimpin menampilkan Edit, dropdown filter proyek hanya berisi proyek penugasannya.

## Catatan operasional

- OM tanpa karyawan tertaut **tetap bisa** mengedit tim proyek yang ia PM-kan, karena hak PM berbasis user. Karyawan tertaut melengkapi scope keanggotaan (absensi, evaluasi, daftar karyawan proyek).
- Temuan lapangan (Okt 2026): dari 10 user OM, hanya 2 yang tertaut sejak awal; akar penyebabnya data karyawan hasil impor memakai email pribadi dan sebagian user OM belum punya data karyawan sama sekali.
- Untuk user non-OM (Team Leader/Staff) pola auditnya sama — ganti `"Operation Manager"` pada Langkah 1–2 dengan nama role yang sesuai.
