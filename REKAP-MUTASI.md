# Rekap Mutasi Kas — KSP Alfarma UMMADA Cirebon

## Ringkasan masalah

| Sumber | Nilai | Keterangan |
|--------|--------|------------|
| **Kas Tersedia di aplikasi (prod)** | Rp 12.062.500 | Dari view `sisa_kas` (buku besar) |
| **Saldo bank (rekening koran)** | Rp 7.385.510 | Saldo akhir periode 01–24 Feb 2026 |
| **Selisih** | **Rp 4.676.990** | Aplikasi lebih besar daripada bank |

---

## Penyebab utama: dua sumber data kas

Di aplikasi, **“Kas Tersedia”** dihitung **hanya dari tabel `general_ledgers`** (buku besar), **bukan** dari transaksi harian di tabel `transaksis`.

### 1. View `sisa_kas` (yang dipakai dashboard)

- **Rumus:** `sisa_kas = kas_masuk - kas_keluar`
- **kas_masuk** = jumlah dari `general_ledgers` dengan `jenis_transaksi` in (`pengembalian`, `shu`, `wajib`, `sukarela`, `denda`) dan `status_pembukuan = 1`
- **kas_keluar** = jumlah dari `general_ledgers` dengan `jenis_transaksi` in (`debet`, `operasional`, `pinjaman`) dan `status_pembukuan = 1`

Jadi **semua angka kas di dashboard hanya tergantung isi `general_ledgers`**.

### 2. Transaksi harian hanya di `transaksis`

- **Simpanan** (wajib, sukarela, debet, denda) → hanya dicatat di **`transaksis`**, **tidak** otomatis masuk **`general_ledgers`**
- **Pinjaman cair** → hanya di **`transaksis`** (`jenis_transaksi = 'pinjaman'`), **tidak** otomatis masuk **`general_ledgers`**
- **Pengembalian angsuran** → hanya di **`transaksis`** (`jenis_transaksi = 'pengembalian'`), **tidak** otomatis masuk **`general_ledgers`**

Akibatnya:

- **general_ledgers** tidak ikut setiap transaksi harian.
- **general_ledgers** hanya berubah saat:
  - Proses **SHU** (operasional, bagi hasil ke nasabah), dan
  - **Tutup buku (TTP)** — di sana operator mengisi manual: kas, tot_pinjam, laba, lalu di-posting ke `general_ledgers` (wajib, pinjaman, shu).

Jadi **“Kas Tersedia” di aplikasi = kas menurut buku besar (general_ledgers)**, yang bisa tidak sama dengan:

- Kas yang dihitung dari **transaksis** (buku harian), dan
- **Saldo bank** (rekening koran).

Itu yang memunculkan **mutasi** (selisih antara buku dan bank).

---

## Rekening koran (contoh periode 01–24 Feb 2026)

Dari file `IBIZ_010701005310567_20260201_20260224_*.pdf`:

| Item | Nilai (Rp) |
|------|------------|
| Saldo awal | 16.611.157 |
| Total debet (keluar) | 9.703.463 |
| Total kredit (masuk) | 477.816 |
| **Saldo akhir** | **7.385.510** |

Transaksi keluar (debet) antara lain: transfer ke Miftahul Janah, Widianto Nugroho, Tohir, Lalu Royan Giasti, Astara (pencairan pinjaman/transfer keluar).  
Masuk: antara lain QRIS, transfer dari Nina Karlina, bunga, pajak.

**📄 Analisis detail (Bank vs Excel KSP):** lihat **[REKAP-MUTASI-DETAIL.md](REKAP-MUTASI-DETAIL.md)** — berisi tabel transaksi gantung (Tohir, Beras, Astara Feb, Nina Karlina, QRIS), daftar penyesuaian, dan langkah praktis.

---

## Cara rekap mutasi (konsep)

1. **Hitung “kas menurut transaksis” (buku harian)**  
   - Kas masuk (dari transaksis): jumlah `total` dimana `jenis_transaksi` in (`wajib`, `sukarela`, `pengembalian`, `denda`)  
   - Kas keluar: jumlah `total` dimana `jenis_transaksi` in (`pinjaman`, `debet`)  
   - **Kas dari transaksis** = total masuk − total keluar  

2. **Bandingkan dengan saldo bank**  
   - **Saldo bank** = sumber benar (rekening koran).  
   - **Selisih** = Kas dari transaksis − Saldo bank = **mutasi**.  

3. **Bandingkan juga dengan “Kas Tersedia” di app**  
   - **Kas Tersedia** = dari `general_ledgers` (sisa_kas).  
   - Selisih (Kas Tersedia − Saldo bank) = gambaran berapa besar buku besar menyimpang dari bank.

Agar mutasi bisa dicek tiap periode, di aplikasi bisa ditambah:

- Halaman **Rekap Kas** yang menampilkan:
  - Kas dari **general_ledgers** (nilai yang sekarang dipakai dashboard),
  - Kas dari **transaksis** (masuk vs keluar per jenis),
  - Input **Saldo bank** (dari rekening koran),
  - **Selisih** (mutasi) antara kas buku vs saldo bank.

---

## Rekomendasi

1. **Jadikan saldo bank sebagai acuan mutasi**  
   Setiap periode (misalnya per bulan), input **Saldo bank** dari rekening koran dan bandingkan dengan kas buku (dari transaksis atau dari general_ledgers). Selisih = mutasi yang perlu dicek (transit, beda waktu valuta, atau koreksi).

2. **Sinkronisasi buku besar dengan transaksi (opsional ke depan)**  
   Agar “Kas Tersedia” mendekati realita:
   - Bisa dipertimbangkan **setiap transaksi simpanan/pinjaman/pengembalian/debet** juga dicatat ke **general_ledgers** (double entry), atau  
   - **Ubah sumber “Kas Tersedia”** dari `general_ledgers` menjadi perhitungan dari **transaksis** (kas masuk − kas keluar), lalu mutasi tetap dijelaskan dengan input saldo bank.

3. **Gunakan halaman Rekap Kas**  
   Di aplikasi sudah ada menu **Pengurus → Rekap Mutasi Kas** (URL: `/rekap-kas`). Pakai untuk:
   - Cek kas dari transaksis vs kas dari general_ledgers,
   - Input saldo bank,
   - Melihat selisih (mutasi) dan mencatat untuk pembukuan.

---

*Dokumen ini membantu menjelaskan mengapa “Kas Tersedia” di aplikasi (Rp 12.062.500) tidak sama dengan saldo bank (Rp 7.385.510) dan cara melakukan rekap mutasi.*
