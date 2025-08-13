# Syneps Academy — LMS untuk PKL

**Deskripsi Singkat**  
Syneps Academy platform Learning Management System (LMS). Fitur dirancang untuk manajemen peserta, ujian, pembayaran, monitoring, serta komunitas alumni. Beberapa fitur masih dalam pengembangan.

---

## Fitur Utama

-   **Manajemen Batch** peserta (kelompok berdasarkan periode)
-   **Manajemen Peserta** (pendaftaran, status: pending/active/alumni)
-   **Multi-kelas & Pengajar** — tiap kelas bisa punya pengajar/mentor berbeda
-   **Deteksi kecurangan saat ujian** (proctoring)
-   **Manajemen Pembayaran** — _(dalam pengembangan)_
-   **Leaderboard** peserta berdasarkan skor ujian
-   **Forum Alumni** _(dalam pengembangan)_
-   **Backup data per-batch** _(dalam pengembangan)_
-   **Cron job tagihan per bulan** _(dalam pengembangan)_
-   **Download nilai PDF** — batch aktif atau semua batch
-   **Tambah/Hapus Ujian** dengan form dinamis + export Excel
-   **Pendaftaran peserta dengan auto-email akun** _(dalam pengembangan)_
-   **Chart Analytics** nilai (Chart.js)
-   Reset data untuk **pengajar/mentor** dan **peserta**
-   Multi-role: `admin`, `pengajar` (mentor), `peserta`

> Fitur yang sedang dikembangkan ditandai _(dalam pengembangan)_.

---

## Tech Stack

-   **Laravel** (PHP)
-   **Blade** + **Tailwind CSS**
-   **Flowbite** (UI Components)
-   **SweetAlert2** (Notifikasi)
-   **Chart.js** (Chart & Analytics)
-   **PDF & Excel Export** (Laravel packages)
-   **Laravel Scheduler / cron** (Task Automation)

---

## Instalasi (Local Development)

### 1. Clone Repository

```bash
git clone <repo-url>
cd <repo-folder>
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Setup Environment

```bash
cp .env.example .env
```

Edit file `.env` sesuai konfigurasi database & app kamu.

**Contoh `.env` minimal:**

```env
APP_NAME="Syneps Academy"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=syneps_academy
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@synepsacademy.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Generate App Key

```bash
php artisan key:generate
```

### 5. Migrasi Database

```bash
php artisan migrate
```

### 6. Jalankan Seeder (opsional, untuk data awal)

```bash
php artisan db:seed
```

Seeder utama menggunakan **`DatabaseSeeder`**.  
Jika ingin jalankan seeder tertentu:

```bash
php artisan db:seed --class=NamaSeeder
```

### 7. Jalankan Server

```bash
php artisan serve
```

### 8. Jalankan Frontend (Vite)

```bash
npm run dev
```

---

## Perintah Penting

| Perintah                   | Keterangan                     |
| -------------------------- | ------------------------------ |
| `composer install`         | Install dependency PHP         |
| `npm install`              | Install dependency Node        |
| `php artisan migrate`      | Migrasi database               |
| `php artisan db:seed`      | Jalankan seeder DatabaseSeeder |
| `php artisan serve`        | Jalankan server Laravel        |
| `npm run dev`              | Compile dan watch asset        |
| `php artisan schedule:run` | Tes scheduler                  |
| `php artisan queue:work`   | Jalankan queue worker          |

---

## Alur Pendaftaran & Akun

1. Peserta daftar lewat form pendaftaran.
2. Admin verifikasi data peserta.
3. Jika diterima, sistem akan **otomatis kirim email** untuk membuat akun _(dalam pengembangan)_.
4. Status peserta: `pending` → `active` → `alumni`.

---

## Struktur Role

-   **Admin** → akses penuh (batch, peserta, pengajar, ujian, laporan, backup)
-   **Pengajar/Mentor** → manajemen soal, penilaian, kelas terkait
-   **Peserta** → akses materi, ujian, leaderboard

---

## Export & Backup

-   **PDF** — nilai per batch (aktif/semua batch)
-   **Excel** — data ujian
-   **Backup Per-batch** — _(dalam pengembangan)_

---
