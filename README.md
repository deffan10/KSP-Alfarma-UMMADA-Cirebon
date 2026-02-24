<p align="center">
  <strong>🏦 KSP Alfarma UMMADA Cirebon</strong>
</p>
<p align="center">
  <em>Sistem Informasi Manajemen Koperasi Simpan Pinjam</em>
</p>
<p align="center">
  <strong>v3.2.1</strong>
</p>

---

## 📖 Tentang

Aplikasi web untuk mengelola **nasabah**, **simpanan**, **pinjaman**, **pembagian SHU**, **laporan**, dan **operator** Koperasi Simpan Pinjam Alfarma UMMADA Cirebon — dibangun dengan Laravel & Livewire.

---

## 🛠 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| 🔧 **Backend** | PHP 8.0.2+, Laravel 9.x |
| 🎨 **Frontend** | Blade, Bootstrap 5, Livewire 2.x, Laravel UI |
| 📦 **Build** | Vite 2.x, Sass, PostCSS |
| 🗄 **Database** | MySQL |
| 🔐 **Auth** | Laravel Auth (session), Laravel Sanctum (API) |
| 📄 **Export/Report** | Maatwebsite Excel, FPDF |

### 📌 Composer
`laravel/framework` · `livewire/livewire` · `laravel/ui` · `maatwebsite/excel` · `setasign/fpdf` · `laravelcollective/html` · `laravel/sanctum`

### 📌 NPM
`vite` · `laravel-vite-plugin` · `bootstrap` · `sass` · `axios` · `lodash`

---

## ✨ Fitur Utama

### 🔐 Autentikasi & Pengguna
- Login / Register / Reset password  
- Manajemen **Operator** (daftar & tambah user)  
- **Profile** koperasi (logo, nama, edit)  

### 👥 Nasabah
- CRUD nasabah (no rekening, nama, alamat, telp, foto, no KTP, saldo, status pinjaman)  
- **Filter status pinjaman** (Semua / Ada / Tidak ada)  
- Pencarian nama & no. rekening  
- Transaksi simpanan/penarikan  
- Detail & riwayat transaksi  

### 💰 Pinjaman
- CRUD pinjaman (total, jumlah angsuran, bunga, skema flat)  
- Tampilan **cicilan per bulan** di tabel list  
- **Re-check** status pinjaman (tutup yang sudah lunas)  
- **Tombol Lunas** — tandai lunas agar nasabah bisa pinjam lagi  
- **Relaksasi angsuran** — ubah tenor (mis. 5 → 7 bulan), cicilan otomatis dihitung ulang  
- Pencarian pinjaman  

### 📊 SHU (Sisa Hasil Usaha)
- Dashboard: jumlah nasabah, kas, total pinjam, saldo, laba  
- Proses pembagian SHU (laba, dana operasional, % simpanan)  
- TTP & cetak buku SHU  

### 📑 Laporan
- **PDF** — Laporan transaksi: kop (logo, nama koperasi, alamat, telepon); tabel transaksi + penyesuaian kas; tabel **PERINCIAN DANA** (Kas Tersedia, Dana Bergulir, Jumlah Simpanan Wajib, Keuntungan Bagi Hasil (berjalan), Total Saldo (Kas + Pinjaman)). Juga PDF per nasabah & per pinjaman.  
- **Excel** — Laporan transaksi (termasuk **penyesuaian kas**), kolom Jenis (Transaksi / Penyesuaian Kas)  
- **Rekap Mutasi Kas** — bandingkan kas buku besar vs transaksi vs saldo bank  
- **Penyesuaian Kas** — jurnal operasional luar KSP (mengurangi Kas Tersedia)  

### 📈 Dashboard
- Statistik: total nasabah, kas tersedia, dana dipinjam, jumlah operator  
- **Chart** total simpanan per bulan (Wajib + Sukarela), 12 bulan berurutan  

### ⚡ Livewire
Nasabah (index, create, update, detail) · Transaksi · Operator · Profil  

---

## 🗃 Database (inti)

| Tabel | Keterangan |
|-------|------------|
| `users` | Operator / admin |
| `nasabahs` | Data nasabah (no_rekening, nama, alamat, saldo_akhir, status_pinjaman, dll.) |
| `transaksis` | Simpanan, penarikan, pinjaman, pengembalian, SHU |
| `pinjamans` | Data pinjaman (total, angsuran, persen, skema, status) |
| `angsurans` | Cicilan per periode |
| `pengembalians` | Riwayat pembayaran angsuran |
| `general_ledgers` | Pembukuan (operasional, SHU, penyesuaian kas) |
| `profiles` | Profil koperasi (active) |

**View:** `sisa_kas` · `tot_pinjam` · `laba` · `kas_masuk` · `kas_keluar` · `chart`  

**Catatan:** `tot_pinjam` (Dana Bergulir) = **total sisa pinjaman yang belum dibayar** (jumlah angsuran status belum lunas), bukan jumlah awal pinjaman. Hanya pinjaman aktif (status=1).

---

## 📋 Persyaratan

- PHP ≥ 8.0.2  
- Composer  
- Node.js & NPM  
- MySQL  
- Ekstensi PHP: BCMath, Ctype, Fileinfo, GD (untuk logo PNG di PDF), JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML  

---

## 🚀 Instalasi

```bash
# 1. Clone & masuk direktori
cd KSP-Alfarma-UMMADA-Cirebon

# 2. Dependensi PHP
composer install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Atur DB di .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 5. Migrasi
php artisan migrate

# 6. Frontend
npm install
npm run build    # production
# npm run dev    # development

# 7. Jalankan
php artisan serve
```

Buka **http://localhost:8000**

---

## 🗺 Route Penting

| Method | URI | Keterangan |
|--------|-----|------------|
| GET | `/` | Login |
| GET | `/dashboard` | Dashboard |
| Resource | `/nasabah` | Nasabah |
| Resource | `/pinjaman` | Pinjaman |
| GET | `/pinjaman/recheck-active` | Re-check pinjaman |
| GET/POST | `/pinjaman/relaksasi/{id}` | Relaksasi angsuran |
| POST | `/pinjaman/{id}/lunas` | Tandai lunas |
| GET/POST | `/shu` | SHU |
| GET | `/laporan/lappdf`, `/laporan/lapxls` | Laporan transaksi PDF (berkop) / Excel |
| GET | `/rekap-kas` | Rekap mutasi kas |
| GET/POST | `/penyesuaian-kas` | Jurnal penyesuaian kas |
| GET | `/operator` | Operator |
| GET | `/profile` | Profil |

---

## 🔧 Helper Global

`app/myhelper.php` (autoload):

- **tgl_id($d)** — format tanggal ke `dd-mm-yyyy`  
- **tglAdd($d, $n)** — tambah `$n` bulan pada tanggal `$d`  

---

## 📋 Changelog

### v3.0.2

#### ✨ Ditambah
- **Filter nasabah** — Filter status pinjaman (Semua / Ada pinjaman / Tidak ada) di list nasabah.
- **Tabel nasabah** — Kolom Saldo & Status Pinjaman; tombol aksi tetap (Transaksi, Edit, Delete, Detail).
- **Cicilan/bulan di list pinjaman** — Kolom nominal cicilan per bulan di tabel DATA PINJAMAN.
- **Relaksasi angsuran** — Ubah jumlah angsuran (mis. 5 → 7 bulan); cicilan per bulan dihitung ulang otomatis. Tombol di halaman pinjaman & detail angsuran.
- **Tombol Lunas** — Ganti tombol Delete jadi **Lunas** (jika semua angsuran sudah dibayar) dan **Batalkan** (jika masih ada angsuran). Lunas = pinjaman hilang dari list, nasabah bisa pinjam lagi.
- **Re-check pinjaman** — Hanya set status nasabah “tidak ada pinjaman” jika benar-benar tidak ada pinjaman aktif tersisa (perbaikan untuk nasabah dengan banyak pinjaman).
- **Rekap Mutasi Kas** (`/rekap-kas`) — Bandingkan kas dari buku besar, transaksi harian, dan saldo bank; input saldo bank untuk hitung mutasi.
- **Penyesuaian Kas** (`/penyesuaian-kas`) — Jenis transaksi **penyesuaian** (operasional luar KSP). Jurnal pengeluaran kas (Tohir, Beras, biaya bank, dll.) mengurangi Kas Tersedia.
- **Laporan transaksi Excel** — Sumber data transaksi + penyesuaian kas; kolom **Jenis** (Transaksi / Penyesuaian Kas).
- **Laporan transaksi PDF** — Kop (logo, nama koperasi, alamat, telepon); data transaksi + penyesuaian; tabel **PERINCIAN DANA** di bawah: Kas Tersedia, Dana Bergulir (sisa pinjaman), Jumlah Simpanan Wajib, Keuntungan Bagi Hasil (berjalan), Total Saldo (Kas + Pinjaman).
- **Dokumen rekap mutasi** — `REKAP-MUTASI.md` & `REKAP-MUTASI-DETAIL.md` (penyebab selisih kas, transaksi gantung, daftar penyesuaian).

#### 🔧 Diperbaiki
- **Chart dashboard** — Sumber data jelas (simpanan wajib + sukarela per bulan); 12 bulan berurutan; label sumbu & judul; view `chart` tanpa hardcode nama database.
- **Livewire nasabah** — Perbaikan syntax Blade (`@endisset` ganda) yang menyebabkan error "unexpected token endif".
- **FPDF logo PNG** — Error "Interlacing not supported" diatasi dengan konversi logo ke non-interlaced via GD (`imageinterlace($im, 0)`).
- **Dana Bergulir** — View `tot_pinjam` diubah: sekarang **sisa pinjaman yang belum dibayar** (jumlah angsuran belum lunas), bukan total nominal awal pinjaman. Pinjaman lunas tidak ikut dihitung.

#### 📝 Diubah
- **Tabel nasabah** — Kolom dari (No.Rek, Nama, Telepon, Alamat) menjadi (No.Rek, Nama, Saldo, Status Pinjaman, Aksi).
- **Tombol pinjaman** — Delete diganti Lunas/Batalkan; tombol Relaksasi ditambah.
- **Versi aplikasi** — Footer & README: **3.0.2**.

---

## 📜 Lisensi

Proyek berbasis Laravel (MIT). Sesuaikan dengan kebijakan KSP Alfarma UMMADA Cirebon.

---

**Versi:** 3.0.2
