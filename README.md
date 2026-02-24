# KSP Alfarma UMMADA Cirebon

Sistem informasi manajemen Koperasi Simpan Pinjam (KSP) untuk **Alfarma UMMADA Cirebon**. Aplikasi web berbasis Laravel untuk mengelola nasabah, simpanan, pinjaman, pembagian SHU, laporan, dan operator.

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | PHP 8.0.2+, Laravel 9.x |
| **Frontend** | Blade, Bootstrap 5, Livewire 2.x, Laravel UI |
| **Build** | Vite 2.x, Sass, PostCSS |
| **Database** | MySQL (default) |
| **Auth** | Laravel Auth (session), Laravel Sanctum (API) |
| **Export/Report** | Maatwebsite Excel, FPDF (setasign/fpdf) |

### Dependensi utama (Composer)

- `laravel/framework` ^9.19  
- `laravel/ui` ^3.4  
- `livewire/livewire` ^2.12  
- `laravelcollective/html` ^6.3.0  
- `maatwebsite/excel` ^3.1.40  
- `setasign/fpdf` ^1.8.4  
- `laravel/sanctum` ^2.14.1  

### Dependensi frontend (NPM)

- `vite`, `laravel-vite-plugin`  
- `bootstrap` ^5.1.3  
- `sass`, `sass-loader`  
- `axios`, `lodash`, `@popperjs/core`  

---

## Fitur Utama

### 1. Autentikasi & Pengguna
- Login / Register  
- Reset password  
- Manajemen **Operator** (daftar user, tambah user)  
- **Profile** (profil aplikasi/organisasi, tampil & edit)  

### 2. Nasabah
- CRUD nasabah (no rekening, nama, alamat, telp, foto, no KTP, saldo, status pinjaman)  
- Pencarian nasabah  
- Transaksi nasabah (simpanan/penarikan)  
- Detail nasabah & riwayat transaksi  

### 3. Pinjaman
- CRUD pinjaman (total, angsuran, persen, skema, status, keterangan)  
- Pencarian pinjaman  
- Recheck pinjaman aktif  
- Get nama nasabah (untuk form/autocomplete)  

### 4. SHU (Sisa Hasil Usaha)
- Dashboard SHU: jumlah nasabah, kas, total pinjam, saldo, laba  
- Proses pembagian SHU (laba, dana operasional, simpanan %)  
- TTP (Tanda Terima Pembayaran) SHU & cetak buku  

### 5. Laporan
- **PDF**: laporan transaksi umum, transaksi per nasabah, pinjaman per nasabah (FPDF)  
- **Excel**: export data (Maatwebsite Excel)  
- Filter/parameter per nasabah untuk laporan transaksi & pinjaman  

### 6. Dashboard
- Statistik: total nasabah, kas tersedia, dana dipinjam, jumlah operator  
- Grafik (chart) berdasarkan view `chart`  
- Data dari view: `sisa_kas`, `tot_pinjam`, `laba`  

### 7. Komponen Livewire
- **Nasabah**: index, create, update, detail  
- **Transaksi** (Transact)  
- **Operator**: index, create  
- **Profil**: index, create, update  

---

## Struktur Database (inti)

- **users** – operator/admin  
- **nasabahs** – data nasabah (no_rekening, nama_lengkap, alamat, telp, foto, no_ktp, saldo_akhir, status_pinjaman)  
- **transaksis** – transaksi simpanan/penarikan/SHU  
- **pinjamans** – data pinjaman  
- **angsurans** – angsuran pinjaman  
- **pengembalians** – pengembalian  
- **general_ledgers** – pembukuan (termasuk operasional & SHU)  
- **profiles** – profil organisasi (status active)  

### View (untuk laporan & dashboard)
- `sisa_kas`, `tot_pinjam`, `laba`  
- `kas_masuk`, `kas_keluar`, `chart`  

---

## Persyaratan

- PHP >= 8.0.2  
- Composer  
- Node.js & NPM (untuk Vite & asset)  
- MySQL (atau driver DB lain yang didukung Laravel)  
- Ekstensi PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML  

---

## Instalasi

1. **Clone & masuk direktori**
   ```bash
   cd KSP-Alfarma-UMMADA-Cirebon
   ```

2. **Install dependensi PHP**
   ```bash
   composer install
   ```

3. **Salin environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Atur database di `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=user
   DB_PASSWORD=password
   ```

5. **Jalankan migrasi**
   ```bash
   php artisan migrate
   ```

6. **Install dependensi frontend & build**
   ```bash
   npm install
   npm run build
   ```
   Untuk development:
   ```bash
   npm run dev
   ```

7. **Jalankan aplikasi**
   ```bash
   php artisan serve
   ```
   Buka: http://localhost:8000  

---

## Route Penting

| Method | URI | Keterangan |
|--------|-----|------------|
| GET | `/` | Halaman login |
| GET | `/dashboard` | Dashboard (perlu login) |
| GET/POST | `/nasabah`, `/nasabah/search`, `/nasabah/transaksi` | Manajemen nasabah |
| Resource | `/pinjaman` | Manajemen pinjaman |
| GET/POST | `/shu`, `/shu/proc`, `/shu/ttp_buku`, `/shu/ttp` | SHU |
| GET | `/laporan/lappdf`, `/laporan/lapxls` | Laporan PDF/Excel |
| GET | `/operator` | Daftar operator |
| GET | `/profile` | Profil |

Route debug (hapus di production): `/db-test`, `/cekdb`, `/dbtestraw`.

---

## Helper Global

File `app/myhelper.php` (autoload di `composer.json`):

- **tgl_id($d)** – format tanggal dari `Y-m-d` ke `dd-mm-yyyy`  
- **tglAdd($d, $n)** – tambah `$n` bulan pada tanggal `$d`  

---

## Lisensi

Proyek berdasarkan template Laravel (MIT).  
Sesuaikan lisensi dengan kebijakan organisasi KSP Alfarma UMMADA Cirebon.
