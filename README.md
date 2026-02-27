<p align="center">
  <strong>🏦 KSP Alfarma UMMADA Cirebon</strong>
</p>
<p align="center">
  <em>Sistem Informasi Manajemen Koperasi Simpan Pinjam</em>
</p>
<p align="center">
  <strong>v3.3.1</strong>
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
- **Kas Tersedia** di PERINCIAN DANA = Buku Besar (sama dengan Dashboard)  
- **Re-check** status pinjaman (tutup yang sudah lunas)  
- **Tombol Lunas** — tandai lunas agar nasabah bisa pinjam lagi  
- **Relaksasi angsuran** — ubah tenor (mis. 5 → 7 bulan), cicilan otomatis dihitung ulang  
- **Proses angsuran bulk** — bayar cicilan banyak sekaligus (centang beberapa angsuran)  
- **Tagihkan ulang** — reset status angsuran yang error (mis. orphan pengembalian)  
- Pencarian pinjaman  

### 📊 SHU (Sisa Hasil Usaha)
- Dashboard: jumlah nasabah, kas, total pinjam, saldo, laba  
- Proses pembagian SHU (laba, dana operasional, % simpanan)  
- TTP & cetak buku SHU  

### 📑 Laporan
- **PDF** — Laporan transaksi: kop (logo, nama koperasi, alamat, telepon); tabel transaksi + penyesuaian kas; tabel **PERINCIAN DANA** (Kas Tersedia, Dana Bergulir, Jumlah Simpanan Wajib, Keuntungan Bagi Hasil (berjalan), Total Saldo (Kas + Pinjaman)). Juga PDF per nasabah & per pinjaman.  
- **Excel** — Laporan transaksi (termasuk **penyesuaian kas**), kolom Jenis (Transaksi / Penyesuaian Kas)  
- **Rekap Mutasi Kas** — bandingkan kas buku besar vs transaksi vs saldo bank  
- **Penyesuaian Kas** — jurnal **pengurangan** kas (operasional luar KSP: vendor, beras, biaya bank) dan **penambahan** kas (sponsor, donasi, hibah); keduanya tercatat di Buku Besar dan tampil di laporan PDF/Excel serta Rekap Kas  

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
| `general_ledgers` | Pembukuan (operasional, SHU, penyesuaian kas: `penyesuaian` = keluar, `penyesuaian_masuk` = masuk) |
| `profiles` | Profil koperasi (active) |

**View:** `sisa_kas` · `tot_pinjam` · `laba` · `kas_masuk` · `kas_keluar` · `chart`  

**Catatan:** `tot_pinjam` (Dana Bergulir) = **total sisa pinjaman yang belum dibayar** (jumlah angsuran status belum lunas), bukan jumlah awal pinjaman. Hanya pinjaman aktif (status=1).

---

## 💵 Sumber Data Kas (Buku Besar)

| Halaman | Sumber Kas | Keterangan |
|---------|------------|------------|
| **Dashboard** | **Buku Besar** (`sisa_kas`) | Kas Tersedia = view `sisa_kas` (kas_masuk − kas_keluar, termasuk penyesuaian kas). |
| **Pinjaman** | **Buku Besar** (`sisa_kas`) | Kas Tersedia di PERINCIAN DANA sama dengan Dashboard (diseragamkan v3.3.0). |
| **Rekap Kas** | Keduanya + saldo bank | Menampilkan Buku Besar, transaksi harian (masuk − keluar dari `transaksis`), dan selisih; input saldo bank untuk rekonsiliasi. |

**Kenapa Buku Besar?** Penyesuaian kas (pengurangan & penambahan, mis. operasional luar KSP atau sponsor/donasi) hanya tercatat di `general_ledgers` dan terhitung di view `sisa_kas` (via `kas_masuk` / `kas_keluar`). Transaksi harian saja tidak termasuk penyesuaian, sehingga bisa beda. Dashboard dan Pinjaman memakai Buku Besar agar angka konsisten. Rekap Kas dipakai untuk cek selisih Buku Besar vs transaksi vs bank.

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
| GET | `/dashboard` | Dashboard (statistik, chart, Kas Tersedia dari Buku Besar) |
| Resource | `/nasabah` | Nasabah (CRUD, transaksi, detail) |
| POST | `/nasabah/search` | Pencarian nasabah |
| POST | `/nasabah/transaksi` | Transaksi simpanan/penarikan |
| Resource | `/pinjaman` | Pinjaman (CRUD, list cicilan, Kas Tersedia dari Buku Besar) |
| POST | `/pinjaman/search` | Pencarian pinjaman |
| GET | `/pinjaman/recheck-active` | Re-check pinjaman aktif |
| GET/POST | `/pinjaman/relaksasi/{id}` | Relaksasi angsuran (ubah tenor) |
| POST | `/pinjaman/{id}/lunas` | Tandai lunas |
| POST | `/pinjaman/proses-angsuran-bulk` | Proses angsuran bulk (cicilan banyak) |
| POST | `/pinjaman/{id}/tagihkan-ulang` | Tagihkan ulang angsuran |
| POST | `/pinjaman/get_name` | Get nama (AJAX) |
| GET | `/shu` | SHU (dashboard, proses pembagian) |
| POST | `/shu/proc` | Proses SHU |
| GET | `/shu/ttp_buku` | Cetak buku SHU (TTP) |
| POST | `/shu/ttp` | TTP (Tanda Terima Pembagian) |
| GET | `/laporan/lappdf` | Laporan transaksi PDF (berkop, PERINCIAN DANA) |
| GET | `/laporan/lapxls` | Laporan transaksi Excel (transaksi + penyesuaian kas) |
| POST | `/laporan/transNas`, `/laporan/pinjNas` | Laporan PDF per nasabah / per pinjaman |
| GET | `/rekap-kas` | Rekap mutasi kas (Buku Besar vs transaksi vs bank) |
| GET/POST | `/penyesuaian-kas` | Daftar & input penyesuaian kas (pengurangan / penambahan) |
| DELETE | `/penyesuaian-kas/{id}` | Hapus penyesuaian kas |
| GET | `/operator` | Daftar operator |
| GET | `/profile`, `/profile/{id}` | Profil koperasi |

---

## 🔧 Helper Global

`app/myhelper.php` (autoload):

- **tgl_id($d)** — format tanggal ke `dd-mm-yyyy`  
- **tglAdd($d, $n)** — tambah `$n` bulan pada tanggal `$d`  

---

## 📋 Changelog

### v3.3.1

#### ✨ Ditambah
- **Penyesuaian kas penambahan** — Di menu Penyesuaian Kas bisa pilih **Penambahan kas** (sponsor, donasi, hibah) selain **Pengurangan kas** (operasional luar KSP). Penambahan memakai jenis transaksi `penyesuaian_masuk` dan masuk ke view `kas_masuk`, sehingga Kas Tersedia (Buku Besar) bertambah. Form: pilihan tipe (Pengurangan / Penambahan), nominal, keterangan. Tabel daftar menampilkan kolom Tipe (badge Penambahan/Pengurangan). Laporan PDF, Excel, dan Rekap Kas (rincian Buku Besar) sudah include penyesuaian masuk.

#### 📝 Diubah
- **Penyesuaian Kas** — Judul halaman disederhanakan jadi "Penyesuaian Kas"; deskripsi menjelaskan kedua tipe (pengurangan & penambahan). Hapus penyesuaian membalik efek ke Kas Tersedia (penambahan dihapus = kas berkurang, pengurangan dihapus = kas bertambah).

---

### v3.3.0

#### 📝 Diubah
- **Kas Tersedia diseragamkan** — Dashboard dan halaman Pinjaman memakai sumber yang sama: **Buku Besar** (view `sisa_kas`). Sebelumnya Pinjaman pakai hitungan dari transaksi harian (masuk − keluar), sehingga angka bisa beda (mis. belum termasuk penyesuaian kas). Sekarang angka Kas Tersedia di Dashboard dan di PERINCIAN DANA (Pinjaman) sama.
- **Label Pinjaman** — Di PERINCIAN DANA, label "Dana Tersedia (dari transaksi harian)" diubah menjadi **"Kas Tersedia"** (konsisten dengan Dashboard).
- **Rekap Kas** — Tetap menampilkan Buku Besar dan transaksi harian untuk rekonsiliasi; tidak diubah.

---

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

**Versi:** 3.3.1
