# ERD — auraERP (Opsora)

| Field | Nilai |
| :--- | :--- |
| **Versi** | 1.2 |
| **Tanggal** | 2026-10-19 |
| **Revisi** | Clarifikasi Manajer Proyek (`user_id`) vs pivot Assign Employees |
| **Sumber** | Migrasi plugin `plugins/webkul/*` (bukan dump DB penuh) |
| **Schema detail** | [DATABASE-SCHEMA.md](./DATABASE-SCHEMA.md) |
| **PRD** | [PRD-auraERP.md](./PRD-auraERP.md) |

Dokumen ini menggambarkan **domain operasional Opsora** (Security/RBAC, HR, Absensi, Time Off, Performance/Evaluasi, Proyek, Timesheet, POS). Modul ERP generik (Sales/Purchase/Inventory penuh, Accounting ledger) ada di AureusERP inti dan tidak digambar lengkap di sini.

---

## 1. Peta domain (konteks)

```mermaid
flowchart TB
  subgraph Identity["Identity & Access"]
    U[users]
    R[roles / permissions]
    T[teams]
    C[companies]
  end

  subgraph HR["People"]
    E[employees_employees]
    D[employees_departments]
    WL[employees_work_locations]
  end

  subgraph Time["Time & Presence"]
    ATT[attendances_attendances]
    SITE[attendances_sites]
    LEAVE[time_off_leaves]
  end

  subgraph Perf["Performance"]
    PLAN[performance_plans]
    GOAL[performance_goals]
    EVAL[evaluations_evaluations]
    RATE[evaluations_ratings]
  end

  subgraph Delivery["Project Delivery"]
    P[projects_projects]
    PE[projects_project_employees]
    TASK[projects_tasks]
    TS[analytic_records timesheets]
    PART[partners_partners]
  end

  subgraph Retail["POS"]
    REG[pos_registers]
    SES[pos_sessions]
    ORD[pos_orders]
  end

  U --> E
  U --> R
  U --> T
  E --> D
  E --> ATT
  E --> LEAVE
  E --> PLAN
  E --> EVAL
  E --> PE
  P --> PE
  P --> TASK
  P --> PART
  TASK --> TS
  ATT --> SITE
  PLAN --> GOAL
  EVAL --> RATE
  REG --> SES --> ORD
```

---

## 2. ERD — Identity, RBAC & organisasi

```mermaid
erDiagram
  companies ||--o{ users : "default_company"
  companies ||--o{ employees_employees : has

  users ||--o{ model_has_roles : ""
  roles ||--o{ model_has_roles : ""
  roles ||--o{ role_has_permissions : ""
  permissions ||--o{ role_has_permissions : ""
  users ||--o{ model_has_permissions : ""
  permissions ||--o{ model_has_permissions : ""

  users ||--o{ user_team : ""
  teams ||--o{ user_team : ""
  projects_projects ||--o| teams : "project_id logical link"

  users ||--o| employees_employees : "Related User (1:1)"
  employees_departments ||--o{ employees_employees : department
  employees_employees ||--o{ employees_employees : "parent_id Manager"
  employees_employees ||--o{ employees_employees : "coach_id"
  employees_work_locations ||--o{ employees_employees : placement
  employees_employment_types ||--o{ employees_employees : type
  partners_partners ||--o{ employees_employees : partner

  companies {
    bigint id PK
    string name
  }

  users {
    bigint id PK
    string name
    string email
    string resource_permission "global|group|individual"
    bigint default_company_id FK
  }

  roles {
    bigint id PK
    string name "Admin|HRD|OM|..."
    string guard_name
  }

  permissions {
    bigint id PK
    string name
    string guard_name
  }

  teams {
    bigint id PK
    string name
    bigint project_id "index only, bukan FK DB"
  }

  employees_employees {
    bigint id PK
    string name
    bigint user_id FK "unique Related User"
    bigint company_id FK
    bigint department_id FK
    bigint parent_id FK "Atasan L1"
    bigint coach_id FK
    bigint work_location_id FK
    bigint employment_type_id FK
    date join_date
    date contract_end_date
    boolean is_active
  }
```

---

## 3. ERD — Absensi & Time Off

```mermaid
erDiagram
  employees_employees ||--o{ attendances_attendances : clocks
  employees_employees ||--o| attendances_face_profiles : "1:1 descriptor"
  attendances_attendances ||--o{ attendances_tracks : "GPS track points"
  attendances_sites ||--o{ attendances_attendances : "check_in_site"
  attendances_sites ||--o{ attendances_attendances : "check_out_site"

  employees_employees ||--o{ time_off_leaves : requests
  time_off_leave_types ||--o{ time_off_leaves : type
  employees_employees ||--o{ time_off_leaves : "first_approver L1"
  employees_employees ||--o{ time_off_leaves : "second_approver L2"
  users ||--o{ time_off_leaves : requester_user

  attendances_sites {
    bigint id PK
    string name
    decimal latitude
    decimal longitude
    int radius_meters
    boolean is_active
    bigint company_id
  }

  attendances_attendances {
    bigint id PK
    bigint employee_id FK
    date work_date
    datetime check_in
    datetime check_out
    decimal check_in_latitude
    decimal check_in_longitude
    bigint check_in_site_id FK
    bigint check_out_site_id FK
    string status
  }

  attendances_face_profiles {
    bigint id PK
    bigint employee_id FK "unique"
    json descriptor
  }

  attendances_tracks {
    bigint id PK
    bigint attendance_id FK
    datetime recorded_at
    decimal latitude
    decimal longitude
    int accuracy
  }

  time_off_leaves {
    bigint id PK
    bigint employee_id FK
    bigint holiday_status_id FK
    bigint manager_id FK
    bigint first_approver_id FK
    bigint second_approver_id FK
    bigint employee_company_id FK
    string state
    timestamp request_date_from
    timestamp request_date_to
    decimal number_of_days
  }
```

---

## 4. ERD — Performance Plans & Evaluasi

```mermaid
erDiagram
  employees_employees ||--o{ performance_plans : owns
  performance_plans ||--o{ performance_goals : contains

  employees_employees ||--o{ evaluations_evaluations : "dinilai"
  employees_employees ||--o{ evaluations_evaluations : "reviewer L1"
  employees_employees ||--o{ evaluations_evaluations : "gm_approver L2"
  employees_employees ||--o{ evaluations_evaluations : "director_approver"
  performance_plans ||--o{ evaluations_evaluations : "plan_id logical link"
  evaluations_evaluations ||--o{ evaluations_ratings : "poin D aspek"

  performance_plans {
    bigint id PK
    bigint employee_id FK
    string title
    date period_start
    date period_end
    string status
  }

  performance_goals {
    bigint id PK
    bigint performance_plan_id FK
    string title
    tinyint weight
    tinyint progress
    string status
  }

  evaluations_evaluations {
    bigint id PK
    bigint employee_id FK
    bigint reviewer_id FK
    bigint gm_approver_id FK
    bigint director_approver_id FK
    bigint performance_plan_id "logical link, bukan FK DB"
    string title
    string evaluation_type
    string status
    decimal goal_achievement_percentage
    string recommendation_decision
    timestamp employee_signed_at
    timestamp reviewer_signed_at
    timestamp gm_signed_at
    timestamp director_signed_at
  }

  evaluations_ratings {
    bigint id PK
    bigint evaluation_id FK
    string title
    tinyint weight
    tinyint score "1-5"
    text comment
    text description
    int sort
  }
```

---

## 5. ERD — Proyek, penugasan, task & timesheet

```mermaid
erDiagram
  partners_partners ||--o{ projects_projects : customer
  companies ||--o{ projects_projects : company
  users ||--o{ projects_projects : "Project Manager user_id"
  projects_project_stages ||--o{ projects_projects : stage

  projects_projects ||--o{ projects_project_employees : assigned
  employees_employees ||--o{ projects_project_employees : member
  users ||--o{ projects_project_employees : "optional user"

  projects_projects ||--o{ projects_tasks : has
  projects_tasks ||--o{ projects_tasks : "parent subtask"
  projects_task_stages ||--o{ projects_tasks : stage
  projects_tasks ||--o{ projects_task_users : assignees
  users ||--o{ projects_task_users : assignee

  projects_projects ||--o{ analytic_records : timesheet
  projects_tasks ||--o{ analytic_records : timesheet
  users ||--o{ analytic_records : logged_by

  projects_projects {
    bigint id PK
    string name
    bigint partner_id FK "Customer"
    bigint user_id FK "Manajer Proyek"
    date start_date
    date end_date
    decimal project_value
    decimal operational_cost
    string visibility
    boolean is_active
  }

  projects_project_employees {
    bigint id PK
    bigint project_id FK
    bigint employee_id FK
    bigint user_id FK
  }

  projects_tasks {
    bigint id PK
    string title
    string state
    datetime deadline
    bigint project_id FK
    bigint parent_id FK
    decimal allocated_hours
    decimal total_hours_spent
    decimal progress
  }

  analytic_records {
    bigint id PK
    string type
    date date
    decimal unit_amount "jam"
    decimal amount
    bigint project_id FK
    bigint task_id FK
    bigint user_id FK
  }
```

**Catatan laporan:**

- **Laporan Ops Mingguan** — query agregat `projects_*` + `analytic_records` + absensi/cuti (tidak ada tabel laporan terpisah).
- **Laporan Mingguan Proyek untuk Klien** — sama; masking diterapkan di layer export (bukan kolom DB).

---

## 6. ERD — POS

```mermaid
erDiagram
  companies ||--o{ pos_registers : has
  pos_registers ||--o{ pos_sessions : opens
  users ||--o{ pos_sessions : cashier
  pos_sessions ||--o{ pos_cash_movements : movements
  pos_sessions ||--o{ pos_orders : sells
  pos_registers ||--o{ pos_orders : register
  users ||--o{ pos_orders : cashier_id
  partners_partners ||--o{ pos_orders : customer
  pos_orders ||--o{ pos_order_lines : lines
  pos_orders ||--o{ pos_order_payments : payments
  pos_payment_methods ||--o{ pos_order_payments : method

  pos_registers {
    bigint id PK
    string name
    string code UK
    boolean is_active
  }

  pos_sessions {
    bigint id PK
    bigint pos_register_id FK
    bigint user_id FK
    string status "open|closed"
    decimal opening_balance
    decimal closing_cash_counted
    decimal cash_difference
  }

  pos_orders {
    bigint id PK
    string name UK
    bigint pos_session_id FK
    bigint cashier_id FK
    string status
    decimal total_amount
    decimal paid_amount
  }

  pos_order_lines {
    bigint id PK
    bigint pos_order_id FK
    bigint product_id
    string product_name
    decimal qty
    decimal line_total
  }
```

---

## 7. ERD — Security audit & blokir IP

```mermaid
erDiagram
  security_audit_trails {
    bigint id PK
    string event
    string description
    string actor_type
    bigint actor_id
    string actor_label
    string subject_type
    bigint subject_id
    string subject_label
    string ip_address
    text user_agent
    string guard
    json properties
    timestamp created_at
  }

  security_blocked_ips {
    bigint id PK
    string ip_address UK
    int failed_attempts
    string reason
    string last_email
    timestamp blocked_until
    timestamps created_updated
  }
```

*(Morph `actor` / `subject` biasanya menunjuk `users` atau model terkait peristiwa. `teams.project_id` dan `evaluations.performance_plan_id` adalah tautan logis ber-index/kolom, **bukan** foreign key constraint DB.)*

---

## 8. Relasi kunci bisnis (narasi)

| Relasi | Kardinalitas | Makna operasional |
| :--- | :--- | :--- |
| `users` ↔ `employees_employees` | 0..1 / 1 | Related User; wajib untuk login & akses proyek |
| `employees.parent_id` | pohon | Approver L1; L2 = parent-of-parent / dept manager |
| `projects.partner_id` | N:1 | Customer pemilik banyak proyek |
| `projects_project_employees` | N:M | Assign Employees (anggota kerja proyek; unassign = hapus baris pivot) |
| `teams.project_id` / nama Tim ≈ nama Proyek | 0..1 | Sinkron anggota Tim ↔ assign proyek |
| `projects.user_id` | N:1 | **Manajer Proyek** (1 user); set/clear di form proyek, terpisah dari pivot assign |
| `evaluations.reviewer_id` / `gm_approver_id` | N:1 | Tanda tangan L1 / L2 |
| `analytic_records.project_id` + `task_id` | N:1 | Timesheet jam kerja (internal; disembunyikan di laporan klien) |
| `attendances_sites` | 1:N attendance | Geofence clock-in/out |

---

## 9. Di luar cakupan diagram

Tabel AureusERP generik (contoh): `sales_orders`, `purchases_orders`, `inventories_*`, `accounts_account_moves`, `products_products`, `maintenance_equipments`, chatter/activity, settings. Lihat migrasi masing-masing plugin bila diperlukan.

Untuk daftar kolom & FK lengkap domain Opsora, lihat **[DATABASE-SCHEMA.md](./DATABASE-SCHEMA.md)**.
