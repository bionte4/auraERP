# Database Schema — auraERP (Opsora)

| Field | Nilai |
| :--- | :--- |
| **Versi** | 1.2 |
| **Tanggal** | 2026-10-19 |
| **Revisi** | Clarifikasi `projects.user_id` = Manajer Proyek (assign di form, bukan Assign Employees) |
| **DB target** | PostgreSQL (produksi VPS) |
| **Sumber kebenaran** | Migrasi di `plugins/webkul/*/database/migrations` |
| **ERD** | [ERD-auraERP.md](./ERD-auraERP.md) |

Dokumen ini merangkum **tabel inti domain Opsora**. Kolom sangat lebar (mis. profil HR) diringkas ke field operasional; lihat file migrasi untuk daftar lengkap.

Konvensi:

- PK: `id` bigint auto-increment kecuali disebut lain
- Soft delete: kolom `deleted_at` bila disebutkan
- Timestamps: `created_at` / `updated_at` kecuali disebut lain

---

## 1. Identity & RBAC

### `users`

| Kolom (utama) | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | bigint PK | |
| `name`, `email` | string | |
| `password` | string | |
| `is_active` | bool | |
| `default_company_id` | FK → `companies` | |
| `resource_permission` | string/enum | `global` / `group` / `individual` |
| MFA columns | — | migrasi multi-factor |

### Spatie Permission

| Tabel | Peran |
| :--- | :--- |
| `roles` | Nama role (`Admin`, `HRD`, `Operation Manager`, …), `guard_name` |
| `permissions` | Nama permission Shield (`view_any_…`, `page_…`, `widget_…`) |
| `role_has_permissions` | Pivot role ↔ permission (**diset ulang** oleh `security:sync-rbac`) |
| `model_has_roles` | User ↔ role |
| `model_has_permissions` | Direct user permission (jarang dipakai; baseline role menang setelah sync) |

### `teams` / `user_team`

| Kolom | Keterangan |
| :--- | :--- |
| `teams.id`, `teams.name` | Nama Tim (sering = nama Proyek); **tanpa** `company_id` |
| `teams.project_id` | `unsignedBigInteger` + index → `projects_projects` (**logical link**, bukan FK constraint) |
| `user_team` | Pivot `user_id` ↔ `team_id` |

### `security_audit_trails`

| Kolom | Keterangan |
| :--- | :--- |
| `event` | Enum/string peristiwa keamanan |
| `description` | Ringkasan |
| `actor_type`, `actor_id` | Morph pelaku |
| `subject_type`, `subject_id` | Morph objek |
| `ip_address`, `user_agent`, `guard` | Konteks request |
| `properties` | JSON detail |
| `created_at` | Tanpa `updated_at` |

### `security_blocked_ips`

IP yang diblokir sementara akibat brute-force login (lihat WI §1.F).

| Kolom | Keterangan |
| :--- | :--- |
| `ip_address` | string(45) **UNIQUE** |
| `failed_attempts` | uint, default 0 |
| `reason` | nullable |
| `last_email` | email terakhir yang gagal dari IP ini |
| `blocked_until` | timestamp indexed, nullable |
| `created_at`, `updated_at` | timestamps |

---

## 2. Company & Partner

### `companies`

Perusahaan tenant/default company user & dokumen.

### `partners_partners`

Kontak Customer/Vendor. Dipakai sebagai:

- Customer pada `projects_projects.partner_id`
- Opsional partner karyawan / pelanggan POS

---

## 3. Employees (HR)

### `employees_employees`

| Kolom (operasional) | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | PK | |
| `name` | string | |
| `user_id` | FK → `users` UNIQUE | Related User |
| `company_id` | FK → `companies` | |
| `department_id` | FK → `employees_departments` | |
| `job_id` | FK → `employees_job_positions` | |
| `partner_id` | FK → `partners_partners` | |
| `work_location_id` | FK → `employees_work_locations` | Penempatan (peta Welcome) |
| `parent_id` | FK self | Manager / Approver L1 |
| `coach_id` | FK self | Coach |
| `employment_type_id` | FK → `employees_employment_types` | Permanent / Contract / … |
| `join_date` | date | |
| `contract_end_date` | date | Alert ≤ 30 hari |
| `identification_id` | string | NIK |
| `job_title` | string | |
| `work_email`, `work_phone`, `mobile_phone` | string | |
| `is_active` | bool | |
| `deleted_at` | soft delete | |

Master terkait: `employees_departments`, `employees_job_positions`, `employees_work_locations` (+ lat/lng penempatan), `employees_employment_types`, skills/resume (lihat migrasi employees).

---

## 4. Absensi

### `attendances_sites`

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `name` | string | Nama lokasi geofence |
| `latitude`, `longitude` | decimal(10,7) | |
| `radius_meters` | uint | default 200 |
| `is_active` | bool | |
| `company_id` | bigint nullable | |

### `attendances_attendances`

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `employee_id` | FK | |
| `work_date` | date | UNIQUE bersama `employee_id` |
| `check_in`, `check_out` | datetime | |
| `check_in_latitude/longitude/accuracy` | decimal/int | GPS |
| `check_out_latitude/longitude/accuracy` | decimal/int | GPS |
| `check_in_site_id`, `check_out_site_id` | FK → sites | |
| `status` | string | |
| `note` | text | |

### `attendances_face_profiles`

| Kolom | Keterangan |
| :--- | :--- |
| `employee_id` | UNIQUE FK |
| `descriptor` | JSON embedding wajah |

### `attendances_tracks`

Titik jejak GPS terkait satu sesi absensi.

| Kolom | Keterangan |
| :--- | :--- |
| `attendance_id` | FK → `attendances_attendances` cascade |
| `recorded_at` | datetime |
| `latitude`, `longitude` | decimal(10,7) |
| `accuracy` | uint nullable |
| `company_id` | nullable index |
| Index | (`attendance_id`, `recorded_at`) |

---

## 5. Time Off

### `time_off_leave_types`

Jenis cuti/izin (`holiday_status`).

### `time_off_leaves`

| Kolom | Keterangan |
| :--- | :--- |
| `employee_id` | FK karyawan |
| `user_id` | User pemohon |
| `manager_id` | FK employees (manager terkait) |
| `holiday_status_id` | FK tipe |
| `first_approver_id`, `second_approver_id` | FK employees (L1/L2) |
| `state` | Status approval |
| `request_date_from`, `request_date_to` | Periode |
| `number_of_days`, `number_of_hours` | Durasi |
| `department_id`, `company_id` | Konteks org |
| `employee_company_id` | FK companies (company karyawan) |
| `calendar_id` | FK calendars nullable |

Tabel terkait: `time_off_leave_allocations`, accrual plans/levels (lihat plugin `time-off`).

---

## 6. Performance & Evaluasi

### `performance_plans`

| Kolom | Keterangan |
| :--- | :--- |
| `employee_id` | FK |
| `title` | |
| `period_start`, `period_end` | |
| `status` | default `draft` |
| `summary` | |
| `company_id` | nullable index |

### `performance_goals`

| Kolom | Keterangan |
| :--- | :--- |
| `performance_plan_id` | FK cascade |
| `title`, `metric`, `target` | |
| `weight`, `progress` | tinyint 0–100 |
| `status` | default `not_started` |
| `sort` | urutan |

### `evaluations_evaluations`

| Kolom | Keterangan |
| :--- | :--- |
| `employee_id` | Karyawan dinilai |
| `reviewer_id` | Approver L1 |
| `gm_approver_id` | Approver L2 |
| `director_approver_id` | Opsional |
| `title`, `evaluation_type` | Mis. `pkwt_q1`, outtask, … |
| `is_final_period` | bool |
| Snapshot | `employee_nik`, `join_date`, `grade_level`, `division_name`, `department_name`, `job_title_snapshot`, `work_location_name` |
| `period_start`, `period_end` | |
| `performance_plan_id` | Link goal setting (**logical**, tanpa FK constraint DB) |
| `goal_achievement_percentage` | decimal |
| `competencies_met_count`, `competencies_total_count` | |
| `status` | `draft` → `in_progress` → `completed` (aplikasi) |
| Teks | `summary`, `strengths`, `areas_for_improvement`, `development_plan`, `employee_comment` |
| Rekomendasi | `recommendation_decision`, `permanent_effective_date`, `extension_months`, `recommendation_reason` |
| Tanda tangan | `employee_signed_at`, `reviewer_signed_at`, `gm_signed_at`, `director_signed_at` |

### `evaluations_ratings`

| Kolom | Keterangan |
| :--- | :--- |
| `evaluation_id` | FK cascade |
| `title` | Aspek (Poin D) |
| `description` | Opsional |
| `weight` | |
| `score` | 1–5 nullable |
| `comment` | Justifikasi |
| `sort` | |

---

## 7. Projects & Timesheet

### `projects_project_stages`

Stage pipeline proyek.

### `projects_projects`

| Kolom | Keterangan |
| :--- | :--- |
| `name` | Index |
| `partner_id` | Customer |
| `company_id` | |
| `user_id` | **Manajer Proyek** (FK → `users`, nullable). Assign/ganti/kosongkan lewat form proyek field **Project Manager** — **bukan** layar Assign Employees |
| `creator_id` | |
| `stage_id` | |
| `start_date`, `end_date` | Reminder 3/2/1 bulan |
| `project_value` | decimal(15,2) nilai kontrak |
| `operational_cost` | decimal(15,2) |
| `allocated_hours` | |
| `visibility` | |
| `allow_timesheets`, `allow_milestones`, … | flags |
| `is_active` | |
| `deleted_at` | soft delete |

### `projects_project_employees` (pivot assign)

| Kolom | Keterangan |
| :--- | :--- |
| `project_id` | FK cascade |
| `employee_id` | FK cascade |
| `user_id` | nullable |
| `creator_id` | nullable |
| UNIQUE | (`project_id`, `employee_id`) |

### `projects_milestones`

Milestone per proyek.

### `projects_task_stages` / `projects_tasks`

| Kolom task utama | Keterangan |
| :--- | :--- |
| `title`, `description`, `state`, `priority` | |
| `deadline` | Overdue detection laporan |
| `project_id`, `milestone_id`, `stage_id`, `parent_id` | |
| `allocated_hours`, `total_hours_spent`, `progress`, … | Jam (internal) |
| `deleted_at` | soft delete |

### `projects_task_users`

Assignee task: UNIQUE (`task_id`, `user_id`).

### `analytic_records` (timesheet)

| Kolom | Keterangan |
| :--- | :--- |
| `type`, `name`, `date` | |
| `unit_amount` | Jam |
| `amount` | Nilai analitik |
| `user_id`, `partner_id`, `company_id`, `creator_id` | |
| `project_id`, `task_id` | Ditambahkan plugin Projects |

Tag/favorit: `projects_tags`, `projects_project_tag`, `projects_task_tag`, `projects_user_project_favorites`.

---

## 8. POS

| Tabel | Isi singkat |
| :--- | :--- |
| `pos_registers` | Register kasir (`code` unique), tax default, paper size, company |
| `pos_payment_methods` | Tunai/QRIS/… |
| `pos_sessions` | Shift open/close, saldo, selisih kas |
| `pos_cash_movements` | Cash in/out per sesi |
| `pos_orders` | Transaksi (`name` unique), total, paid, cashier |
| `pos_order_lines` | Baris produk (snapshot nama/harga) |
| `pos_order_payments` | Pembayaran per metode |

---

## 9. Indeks & constraint penting

| Tabel | Constraint |
| :--- | :--- |
| `employees_employees.user_id` | Unique (satu Related User) |
| `attendances_attendances` | Unique (`employee_id`, `work_date`) |
| `attendances_face_profiles.employee_id` | Unique |
| `projects_project_employees` | Unique (`project_id`, `employee_id`) |
| `projects_task_users` | Unique (`task_id`, `user_id`) |
| `pos_registers.code`, `pos_sessions.name`, `pos_orders.name` | Unique |
| `pos_payment_methods.code` | Unique |
| `security_blocked_ips.ip_address` | Unique |

---

## 10. Entitas tanpa tabel persistensi

| Fitur | Persistensi |
| :--- | :--- |
| Laporan Ops Mingguan | Query runtime + export Excel |
| Laporan Mingguan Proyek Klien | Query runtime + export Excel (masking di service) |
| Dashboard Evaluasi | Agregasi `evaluations_*` |
| My Day / Team Board widgets | Query runtime |
| Baseline RBAC | Disimpan di Spatie tables via `RolePermissionSynchronizer` |

---

## 11. Cara memverifikasi schema di VPS

```bash
cd /var/www/auraerp
sudo -u www-data php artisan db:show
sudo -u www-data php artisan db:table employees_employees
sudo -u www-data php artisan db:table projects_projects
sudo -u www-data php artisan db:table evaluations_evaluations
```

Atau SQL:

```sql
SELECT table_name
FROM information_schema.tables
WHERE table_schema = 'public'
  AND (
    table_name LIKE 'employees_%'
    OR table_name LIKE 'projects_%'
    OR table_name LIKE 'evaluations_%'
    OR table_name LIKE 'performance_%'
    OR table_name LIKE 'attendances_%'
    OR table_name LIKE 'pos_%'
    OR table_name LIKE 'time_off_%'
    OR table_name LIKE 'security_%'
    OR table_name IN ('users','roles','permissions','teams','user_team','companies','partners_partners','analytic_records')
  )
ORDER BY table_name;
```

---

## 12. Referensi migrasi utama

| Domain | Path contoh |
| :--- | :--- |
| Employees | `plugins/webkul/employees/database/migrations/` |
| Attendances | `plugins/webkul/attendances/database/migrations/` |
| Time Off | `plugins/webkul/time-off/database/migrations/` |
| Performance | `plugins/webkul/performance/database/migrations/` |
| Evaluations | `plugins/webkul/evaluations/database/migrations/` |
| Projects | `plugins/webkul/projects/database/migrations/` |
| Analytics/TS | `plugins/webkul/analytics/...` + alter Projects |
| POS | `plugins/webkul/pos/database/migrations/2026_10_15_100000_create_pos_tables.php` |
| Security | `plugins/webkul/security/database/migrations/` |
