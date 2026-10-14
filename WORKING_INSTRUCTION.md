# WORKING INSTRUCTION (WI) — AURA ERP

Dokumen ini berisi panduan operasional lengkap untuk seluruh modul kustom yang telah dibangun di **auraERP** (`https://opsora.my.id` / `https://auraerp.my.id`).

---

## 1. Akun Login & Hak Akses

Halaman login Admin Panel: **`/admin/login`**

| Peran | Email | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **HRD / Manajemen** | `hrd@opsora.my.id` | `Demo-hrd-2026` | Mengelola Karyawan, Kontrak, Departemen, Lokasi Kerja (Geofence), Rekap Absensi, Rencana Kinerja (KPI), Evaluasi Kinerja, Aset/Peralatan, dan `auraAI` seluruh perusahaan. |
| **Karyawan (Staf)** | `karyawan@opsora.my.id` | `Demo-karyawan-2026` | Melakukan Absensi (`/admin/absen`), melihat riwayat absensi pribadi, melihat peralatan milik sendiri, dan menggunakan `auraAI` khusus untuk data pribadinya sendiri. |

*(Untuk membuat ulang akun demo kapan saja di server: `php8.4 artisan db:seed --class="Webkul\\Security\\Database\\Seeders\\DemoUserSeeder" --no-interaction`)*

---

## 2. Pengelolaan Karyawan, Tipe Karyawan & Alert Kontrak (≤ 30 Hari)

### A. Mengatur Departemen
1. Buka menu **Employees → Configurations → Departments** (`/admin/employees/departments`).
2. Klik **New Department** untuk membuat departemen baru atau klik **Edit** pada departemen yang ada untuk mengatur *Manager* dan *Parent Department*.

### B. Mengisi Tipe Karyawan & Masa Kontrak
1. Buka menu **Employees → Employees** (`/admin/employees/employees`).
2. Klik **New Employee** atau pilih **Edit** pada karyawan yang sudah ada.
3. Masuk ke tab **Work Information (Informasi Kerja)** → bagian **Organization Details (Detail Organisasi)**:
   - **Employment Type (Tipe Karyawan):** Pilih salah satu dari:
     - `Permanent`
     - `Contract`
     - `Konsultan`
     - `Freelance`
   - **Start Join Date (Tanggal Mulai Bergabung):** Pilih tanggal mulai bekerja.
   - **End Contract Date (Tanggal Berakhir Kontrak):** Isi tanggal berakhir kontrak (khusus karyawan *Contract / Konsultan / Freelance*; kosongkan untuk *Permanent*).
4. Klik **Save Changes**.

### C. Memantau Kontrak yang Akan Habis (Alert 30 Hari)
- **Tab Filter Cepat:** Di halaman daftar **Employees**, klik tab **`Kontrak Segera Berakhir` (`Expiring Contracts`)** untuk menampilkan seluruh karyawan aktif yang masa kontraknya tersisa `≤ 30 hari` atau sudah terlewat.
- **Badge Peringatan:** Pada kartu karyawan akan muncul badge kuning (**`X hari lagi`**) bila sisa kontrak `≤ 30 hari`, dan badge merah bila kontrak telah berakhir.
- **Notifikasi Otomatis:**
  - Muncul *toast* peringatan otomatis saat HRD/Admin membuka halaman **Employees**.
  - Dikirimkan otomatis ke **Lonceng Notifikasi** di pojok kanan atas setiap hari pukul **08:00** pagi melalui scheduler Laravel (`php8.4 artisan employees:notify-expiring-contracts`).

---

## 3. Absensi Geofence Multi-Lokasi & Kamera (HP vs Laptop)

### A. Mendaftarkan Lokasi Kerja / Proyek (Oleh HRD / Admin)
1. Buka menu **Employees → Attendance ▾ → Lokasi kerja (`Work sites`)** (`/admin/employees/attendance-sites`).
2. Klik **Lokasi kerja baru (`New work site`)**.
3. Lengkapi data lokasi:
   - **Nama lokasi:** Contoh: *Kantor Pusat*, *Site Proyek Pelanggan A*.
   - **Latitude & Longitude:** Klik tombol **Gunakan posisi GPS saat ini** bila Anda sedang berada di lokasi tersebut, atau tempel koordinat dari Google Maps.
   - **Radius diizinkan (meter):** Jarak maksimal yang diperbolehkan untuk absen (antara `20` s/d `5000` meter, misal `150` m).
   - **Aktif:** Pastikan toggle dalam keadaan aktif.
4. Klik **Save**.

### B. Cara Karyawan Melakukan Clock In / Clock Out
1. Buka menu **Employees → Attendance ▾ → Absen (`Clock`)** (`/admin/absen`).
2. Izinkan akses **Lokasi (GPS)** pada browser. Sistem otomatis menghitung jarak (*Haversine*) ke seluruh **Lokasi Kerja** aktif dan memilih titik terdekat.
3. **Aturan Kamera Otomatis Berdasarkan Perangkat:**
   - **HP / Smartphone / Tablet:** Kamera depan otomatis menyala. Sistem melakukan verifikasi kecocokan wajah (*Face Matching*) dan mengambil foto selfie sebelum tombol **Clock In / Clock Out** diaktifkan.
   - **Laptop / PC / Netbook:** Kamera **tidak dibuka**. Karyawan cukup berada di dalam radius GPS lokasi kerja untuk langsung menekan tombol **Clock In / Clock Out**.

---

## 4. Rencana Kinerja (KPI) & Evaluasi Kinerja

1. **Rencana Kinerja (KPI / Target):**
   - Buka menu **Employees → Performance ▾ → Performance plans** (`/admin/performance/plans`).
   - Buat rencana kinerja untuk karyawan dan tentukan periode tanggalnya.
   - Pada bagian bawah (**Goals / Sasaran**), tambahkan butir-butir KPI beserta **Weight (Bobot %)** dan **Progress (%)**. Skor tertimbang dihitung otomatis.
2. **Evaluasi Kinerja (Penilaian 1–5):**
   - Buka menu **Employees → Performance ▾ → Performance evaluation** (`/admin/evaluations/evaluations`).
   - Buat dokumen evaluasi karyawan, lalu isi nilai kriteria pada tabel **Ratings** (skala `1` s/d `5` beserta bobot).

---

## 5. Manajemen Aset (Karyawan, Proyek, Customer) & Pergerakan Aset

### A. Mendaftarkan Aset / Peralatan Baru
1. Buka menu **Maintenance → Equipment (`Peralatan`)** (`/admin/maintenance/equipments`).
2. Klik **New Equipment (`Peralatan Baru`)**.
3. Pada bagian **General & Product Information**:
   - Isi **Nama Aset / Peralatan** (contoh: *MacBook Pro M4 #A01*, *Router Mikrotik Site A*), **Model**, **Serial Number**, **Effective Date**, **Cost**, dan **Warranty Expiration Date**.
   - Pilih **Vendor (`Pemasok`)**. Daftar pilihan telah disaring agar hanya menampilkan vendor eksternal (tidak mencampur nama karyawan/user internal). Klik tombol **`+`** di sebelah kanan dropdown Vendor untuk menambahkan vendor baru secara langsung.
4. Pada bagian **Settings (`Pengaturan`)**:
   - **Assigned Employee (`Karyawan Pemegang Aset`):** Pilih karyawan yang memegang aset tersebut (*Owner User* akan terisi otomatis bila karyawan memiliki akun user).
   - **Project (`Proyek`):** Pilih proyek tempat aset ditempatkan (*Customer* akan terisi otomatis mengikuti klien dari proyek tersebut).
   - **Customer (`Customer / Klien`):** Pilih customer pemilik/lokasi proyek, atau klik tombol **`+`** untuk menambah customer baru secara langsung.
   - **Used in location (`Digunakan di lokasi`):** Isi detail ruangan/gedung/site.
5. Klik **Create / Save**.

### B. Mencatat Pergerakan / Perpindahan Aset (`Relocation`, `Replacement`, `Return`, dll.)
Setiap kali aset berpindah tangan, dipindah ke proyek/lokasi lain, diganti unit baru (*replacement*), atau dikembalikan:
1. Buka menu **Maintenance → Equipment**, lalu klik **Edit** atau **View** pada aset terkait.
2. Gulir ke bawah ke bagian **Riwayat Pergerakan / Perpindahan Aset (`Asset Movement / Transfer History`)**.
3. Klik tombol **Catat Pergerakan / Pindah Aset (`Record Asset Movement`)**.
4. Pilih **Jenis Pergerakan (`Movement Type`)**:
   - **`Penugasan / Serah Terima (Assignment)`**: Menyerahkan aset ke karyawan, proyek, customer, dan lokasi tujuan.
   - **`Relokasi / Pindah Lokasi (Relocation)`**: Memindahkan aset ke lokasi, proyek, atau karyawan baru. Data asal (*Dari Karyawan, Dari Proyek, Dari Lokasi*) otomatis direkam sebelum status aset diperbarui.
   - **`Penggantian Unit (Replacement)`**:
     - Pilih **Aset Pengganti (Unit Baru)** yang akan menggantikan unit lama ini.
     - Isi **Lokasi Tujuan** untuk unit lama yang ditarik (contoh: *Gudang Servis*).
     - Sistem **otomatis memindahkan** Karyawan pemegang, Proyek, Customer, dan Lokasi aktif dari unit lama ke **Unit Pengganti**, sekaligus mengosongkan penugasan pada unit lama.
   - **`Pengembalian ke Gudang/Kantor (Return)`**: Melepas penugasan karyawan/proyek/customer dan mengembalikan lokasi aset ke gudang/kantor.
   - **`Perbaikan / Servis (Repair)`**: Mencatat perpindahan aset untuk keperluan perbaikan.
   - **`Afkir / Penghapusan (Scrap)`**: Melepas penugasan aset dan otomatis mengisi tanggal afkir (`scraped_at`).
5. Isi **Tanggal Pergerakan**, **No. Referensi / BAST**, dan **Catatan / Alasan**, lalu klik **Create**.
6. Riwayat perpindahan akan tersimpan di tabel histori dan otomatis tercatat sebagai *Note* di **Chatter** aset tersebut.

---

## 6. Penggunaan `auraAI Assistant` (Floating Chatbot & In-Chatter AI)

### A. Floating Chatbot `✨ auraAI` (Pojok Kanan Bawah)
1. Klik tombol melayang **`✨ auraAI`** di pojok kanan bawah layar.
2. Klik salah satu tombol cepat (*quick chips*) atau ketik pertanyaan bebas:
   - *“Absensi hari ini”*
   - *“Kontrak akan berakhir”*
   - *“Ringkasan kinerja & evaluasi”*
   - *“Ringkasan karyawan”*
   - *“Aset proyek & karyawan”* / *“Bagaimana riwayat replacement aset?”*
3. **Keamanan Data Otomatis (Role-Aware):**
   - **Admin & HRD:** Menerima laporan dan ringkasan seluruh perusahaan.
   - **Karyawan Biasa:** Hanya menerima informasi mengenai profil, sisa kontrak, absensi, nilai kinerja/evaluasi, dan daftar aset milik dirinya sendiri.

### B. Tombol `✨ Tanya AI` & Mention `@ai` di Panel Chatter Dokumen
1. Buka halaman detail (**View**) dokumen apa pun yang memiliki tombol **Chatter** (seperti Karyawan, Aset/Equipment, Proyek, Evaluasi, Rencana Kinerja).
2. Klik tombol **Chatter**, lalu klik tombol **`✨ Tanya AI` (`Ask AI`)** di deretan atas panel Chatter:
   - Kosongkan kolom instruksi untuk membuat **ringkasan otomatis** dokumen tersebut, atau ketik pertanyaan spesifik, lalu klik **Buat Jawaban AI di Chatter**.
3. Atau, saat mengirim pesan melalui tombol **Message** di Chatter, cukup sertakan kata **`@ai`** di dalam isi pesan—maka `auraAI` akan otomatis membalas di thread diskusi dokumen tersebut.

---

## 7. Panduan Update / Deploy di Server VPS

Setiap kali ada pembaruan kode dari GitHub (`bionte4/auraERP`), jalankan perintah berikut di VPS (`ubuntu@VM-18-102-ubuntu`):

```bash
cd /var/www/auraerp
git pull origin main
php8.4 artisan migrate --force --no-interaction
php8.4 artisan shield:generate --all --panel=admin --no-interaction
php8.4 artisan optimize:clear
```
