# 🏥 Sistem Informasi Manajemen Griya PMI

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-^8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-v7.0-646CFF?style=for-the-badge&logo=vite&logoColor=white)

Sistem Informasi Manajemen **Griya PMI** (Palang Merah Indonesia) adalah platform berbasis web yang dirancang untuk mengelola operasional panti/griya pelayanan sosial PMI secara digital, transparan, dan terintegrasi. Platform ini mencakup pengelolaan warga binaan, logistik barang, permohonan kunjungan, penerimaan donasi, monitoring kesehatan, pencatatan keuangan, hingga pengajuan reimbursement.

---

## 📋 Daftar Isi

1. [Penjelasan Proyek](#-penjelasan-proyek)
2. [Fitur Utama](#-fitur-utama)
   - [Akses Publik & User (Donatur)](#1-akses-publik--user-donatur)
   - [Panel Admin Griya](#2-panel-admin-griya)
   - [Panel Superadmin](#3-panel-superadmin)
3. [Teknologi & Dependensi](#-teknologi--dependensi)
4. [Persyaratan Sistem](#-persyaratan-sistem)
5. [Panduan Instalasi](#-panduan-instalasi)
6. [Konfigurasi Environment (.env)](#-konfigurasi-environment-env)
7. [Akun Default / Akun Uji Coba](#-akun-default--akun-uji-coba)
8. [Panduan Penggunaan](#-panduan-penggunaan)
   - [Menjalankan Aplikasi](#menjalankan-aplikasi)
   - [Ekspor Laporan (PDF & Excel)](#ekspor-laporan-pdf--excel)
9. [Struktur Direktori Proyek](#-struktur-direktori-proyek)
10. [Lisensi](#-lisensi)

---

##  Penjelasan Proyek

Platform ini dibangun menggunakan framework **Laravel 12** dan **Tailwind CSS v4** untuk memberikan solusi pengelolaan data terpadu pada Palang Merah Indonesia (PMI), khususnya dalam memfasilitasi operasional Griya PMI.

Tujuan utama dari sistem ini adalah:
- **Transparansi Donasi & Logistik**: Memungkinkan donatur dan masyarakat melihat kebutuhan mendesak serta menyalurkan bantuan secara tepat.
- **Digitalisasi Rekam Medis & Monitoring**: Memudahkan pengelola memantau riwayat kesehatan warga binaan, kontrol obat, rujukan medis/RSJ, dan rekam pemeriksaan rutin.
- **Efisiensi Akuntabilitas Keuangan**: Sistem pencatatan keuangan internal dan alur persetujuan *reimbursement* bertingkat antara Admin dan Superadmin.
- **Kemudahan Pelayanan Publik**: Memudahkan keluarga atau instansi melakukan pendaftaran kunjungan warga binaan secara online.

---

##  Fitur Utama

### 1. Akses Publik & User (Donatur)
- **Beranda & Profil Griya**: Informasi seputar kegiatan Griya PMI, kebutuhan barang mendesak, serta artikel berita terbaru.
- **Permohonan Kunjungan**: Publik dapat mengajukan jadwal kunjungan warga binaan secara mandiri.
- **Donasi Online**: Pendaftaran donasi berupa barang atau uang tunai, lengkap dengan bukti transfer dan pelacakan status verifikasi.
- **Cek Status**: Pengecekan status real-time untuk permohonan kunjungan maupun donasi yang diajukan.
- **Manajemen Profil**: Pengaturan akun user, verifikasi email, dan pembaruan kata sandi.

### 2. Panel Admin Griya
- **Dashboard Operasional**: Ringkasan statistik warga binaan, stok logistik, status kunjungan, dan donasi masuk.
- **Manajemen Warga Binaan**: Pendataan identitas warga binaan (WBP) beserta pencetakan/ekspor data ke format PDF & Excel.
- **Manajemen Logistik**: Pengelolaan barang masuk, pengeluaran logistik, penyesuaian stok, serta publikasi kebutuhan barang ke halaman publik.
- **Persetujuan Kunjungan**: Verifikasi, persetujuan (*Approve*), atau penolakan (*Reject*) pengajuan kunjungan dari masyarakat.
- **Verifikasi Donasi**: Proses validasi transaksi donasi masuk hingga status selesai (*Completed*).
- **Monitoring Kesehatan**:
  - Catatan pemeriksaan fisik rutin & riwayat penyakit.
  - Pengelolaan stok & distribusi obat warga binaan.
  - Catatan rujukan medis dan rujukan Rumah Sakit Jiwa (RSJ).
- **Keuangan & Reimbursement**: Pencatatan arus kas (pemasukan/pengeluaran) serta pengajuan klaim biaya (*reimbursement*) kepada Superadmin.
- **Manajemen Artikel**: Pembuatan dan publikasi berita/kegiatan Griya PMI.

### 3. Panel Superadmin
- **Semua Fitur Admin**: Akses penuh ke seluruh fitur operasional Admin.
- **ACC & Validasi Reimbursement**: Persetujuan/penolakan akhir klaim *reimbursement* yang diajukan oleh staf Admin, dilengkapi ekspor laporan PDF/Excel.
- **Manajemen Akun Griya**: Pembuatan, pembaruan, dan penonaktifan akun pengelola (Admin & Superadmin).
- **Manajemen Akun Publik**: Pengelolaan dan pemblokiran/aktifasi akun pengguna publik.

---

## Teknologi & Dependensi

| Kategori | Teknologi / Paket | Deskripsi |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 12.x | PHP Web Framework modern |
| **Language** | PHP ^8.2 | Versi PHP minimal yang disyaratkan |
| **Database** | MySQL / MariaDB | Database Relasional |
| **Frontend Styling** | Tailwind CSS v4 & Vite | Utility-first CSS framework & bundler |
| **Role & Authorization** | `spatie/laravel-permission` | Manajemen Role (Superadmin, Admin, User) |
| **PDF Export** | `barryvdh/laravel-dompdf` | Generator laporan berbentuk PDF |
| **Excel Export** | `maatwebsite/excel` | Generator ekspor spreadsheet Excel |
| **Utilities** | `laravel/tinker`, `laravel/pail` | Interactive shell & real-time log tailing |

---

##  Persyaratan Sistem

Sebelum menginstal proyek ini, pastikan perangkat Anda memenuhi persyaratan berikut:
- **PHP** versi `>= 8.2` (ekstensi aktif: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `gd`)
- **Composer** versi `>= 2.x`
- **Node.js** versi `>= 18.x` dan **npm** versi `>= 9.x`
- **Database Server**: MySQL `>= 5.7` / MariaDB `>= 10.3` (misal via Laragon atau XAMPP)

---

## 🚀 Panduan Instalasi

Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan proyek di lingkungan lokal Anda:

### 1. Pull Repository dari Branch Staging
Clone repositori atau dapatkan pembaruan kode terbaru dari branch `staging`:

```bash
# Jika baru pertama kali mengklon repositori:
git clone -b staging <repository-url>
cd TA_PMI

# Atau jika repositori sudah ada lokal, masuk ke direktori, switch ke staging dan lakukan pull:
git checkout staging
git pull origin staging
```

### 2. Install Dependensi PHP (Composer)
Jalankan Composer untuk mengunduh seluruh pustaka PHP:
```bash
composer install
```

### 3. Install Dependensi Frontend (npm)
Install paket Node.js untuk kompilasi Tailwind CSS & Vite:
```bash
npm install
```

### 4. Salin File Konfigurasi Environment
Buat file `.env` dari contoh `.env.example`:
```bash
copy .env.example .env
```

### 5. Generate Application Key
Buat kunci enkripsi aplikasi Laravel:
```bash
php artisan key:generate
```

### 6. Konfigurasi Database & Migrasi
1. Buat database baru di MySQL dengan nama **`TA_PMI`** (atau sesuaikan pada file `.env`).
2. Jalankan perintah migrasi tabel beserta *seeder* data awal:
```bash
php artisan migrate:fresh --seed
```

### 7. Buat Symbolic Link Storage
Agar file upload (seperti bukti donasi, gambar artikel, file medis) dapat diakses publik:
```bash
php artisan storage:link
```

### 8. Build Aset Frontend
Jalankan kompilasi aset dengan Vite:
```bash
npm run build
```

---

##  Konfigurasi Environment (.env)

Pastikan variabel-variabel kunci berikut telah disesuaikan pada file `.env`:

```ini
APP_NAME="Griya PMI"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=TA_PMI
DB_USERNAME=root
DB_PASSWORD=

# Session & Cache
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# Mail Settings (Opsional - untuk verifikasi email)
MAIL_MAILER=log
MAIL_FROM_ADDRESS="no-reply@griyapmi.id"
MAIL_FROM_NAME="${APP_NAME}"
```

---

##  Akun Default / Akun Uji Coba

Setelah menjalankan perintah `php artisan migrate:fresh --seed`, tersedia 3 akun bawaan untuk keperluan pengujian:

| Role | Email | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Superadmin** | `superadmin@griyapmi.id` | `password` | Full Control, ACC Reimbursement, Kelola Akun Staf |
| **Admin** | `admin@griyapmi.id` | `password` | Kelola Warga Binaan, Logistik, Donasi, Kunjungan, Monitoring Medis |
| **User / Donatur** | `user@griyapmi.id` | `password` | Pengajuan Donasi, Permohonan Kunjungan, Cek Status |

---

##  Panduan Penggunaan

### Menjalankan Aplikasi

Anda dapat menjalankan server lokal Laravel dan Vite secara bersamaan:

#### Opsi 1: Menjalankan Sekaligus (Melalui Script Composer)
```bash
composer run dev
```

#### Opsi 2: Menjalankan Secara Terpisah
1. **Server Laravel (PHP Backend):**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser di: **`http://localhost:8000`**

2. **Vite Development Server (Frontend Asset Hot-Reload):**
   ```bash
   npm run dev
   ```

### Ekspor Laporan (PDF & Excel)

Staf Admin dan Superadmin dapat mengunduh laporan pada modul-modul berikut melalui tombol **Export PDF** atau **Export Excel**:
- **Data Warga Binaan**: `/admin/warga-binaan/export/pdf` dan `/excel`
- **Data Kunjungan**: `/admin/kunjungan/export/pdf` dan `/excel`
- **Data Monitoring Kesehatan**: `/admin/monitoring/{id}/pemeriksaan/export/pdf` dan `/excel`
- **Data Reimbursement**: `/admin/reimbursement/export/pdf` dan `/excel`
- **Laporan ACC Reimbursement**: `/superadmin/acc-reimbursement/export/pdf` dan `/excel`

---

##  Struktur Direktori Proyek

```text
TA_PMI/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Controller Admin (Logistik, Warga Binaan, Donasi, Medis, Keuangan, dll)
│   │   │   ├── Public/         # Controller Publik (Beranda, Artikel, Kebutuhan Mendesak)
│   │   │   ├── Superadmin/     # Controller Superadmin (ACC Reimbursement, Akun Griya)
│   │   │   └── User/           # Controller User/Donatur (Donasi, Kunjungan, Profil)
│   │   └── Middleware/         # Custom Middleware Role & Auth
│   └── Models/                 # Eloquent ORM Models
├── config/                     # Konfigurasi aplikasi
├── database/
│   ├── migrations/             # Skema tabel database
│   └── seeders/                # Data seeder (UserSeeder, RoleSeeder, LogistikSeeder, WargaBinaanSeeder)
├── public/                     # Public assets & index.php
├── resources/
│   ├── css/                    # Tailwind CSS styles
│   ├── js/                     # Script JavaScript entry points
│   └── views/                  # Blade Views (Layouts, Admin, User, Public pages)
├── routes/
│   ├── web.php                 # Web routes aplikasi
│   └── console.php             # Command routes
├── storage/                    # Storage upload, log, & cache
└── vite.config.js              # Konfigurasi Vite bundler
```

---

##  Lisensi

Platform ini dikembangkan untuk **Griya PMI (Palang Merah Indonesia)**. Hak cipta dilindungi undang-undang. Framework Laravel dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).
