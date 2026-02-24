# Rekap Mutasi Kas — Analisis Detail (Bank vs Excel KSP)

Dokumen ini menggabungkan **Rekening Koran (PDF)** dan **Laporan Excel KSP** untuk membedah selisih **Rp 4.676.990** antara saldo aplikasi (Rp 12.062.500) dan saldo bank (Rp 7.385.510).

---

## 1. Ringkasan Perbandingan Saldo & Arus Kas

| Deskripsi | Total di Bank (PDF) | Status di Excel KSP | Catatan |
|-----------|---------------------|----------------------|---------|
| **Saldo akhir (24 Feb 26)** | **Rp 7.385.510** | Aplikasi: **Rp 12.062.500** | **Selisih: Rp 4.676.990** |
| Pembayaran Beras bulanan | Rp 13.511.000 (akumulasi) | Tidak tercatat | Pengeluaran operasional rutin |
| Pembayaran ke **Tohir** | Rp 13.570.000 (akumulasi) | Tidak tercatat | 4x transfer besar (Okt–Feb) |
| Pencairan pinjaman (Feb) | Rp 9.703.463 | Sebagian | Miftahul, Widianto, Lalu Royan ada; **Astara (Feb) & Tohir tidak** |
| Biaya admin, SMS, pajak | Rp 16.084 | Tidak tercatat | Akumulasi biaya bank & pajak bunga |
| Pendapatan bunga & QRIS | Rp 114.247 (sekitar) | Tidak tercatat | Pemasukan ke bank belum diinput |

---

## 2. Transaksi “Gantung” (Ada di Bank, Tidak / Belum di KSP)

Transaksi di bawah ini **mengurangi atau menambah saldo bank** tetapi **tidak ditemukan** di laporan Excel KSP. Inilah yang membuat kas di aplikasi terlihat lebih besar dari saldo bank.

### 2.1 Pengeluaran (Debet) — belum dicatat sebagai kas keluar di KSP

| Tanggal | Nama / Uraian | Debet (Rp) | Keterangan |
|---------|----------------|------------|------------|
| 31/10/25 | TOHIR | 3.450.000 | Tidak ada di Excel |
| 09/12/25 | TOHIR | 3.450.000 | Tidak ada di Excel |
| 07/01/26 | TOHIR | 3.450.000 | Tidak ada di Excel |
| 04/02/26 | TOHIR | 3.220.000 | Tidak ada di Excel |
| **Subtotal Tohir** | | **13.570.000** | |
| 28/11/25 | BERAS NOVEMBER | 3.396.000 | Operasional |
| 30/12/25 | BERAS DESEMBER | 3.323.000 | Operasional |
| 29/01/26 | BERAS JANUARI | 3.323.000 | Operasional |
| *(Okt)* | BERAS OKTOBER | 3.469.000 | (jika ada di bank) |
| **Subtotal Beras** | | **~13.511.000** | |
| 16/02/26 | ASTARA (angsuran/pencairan Feb) | 482.500 | Di Excel Astara hanya s/d Jan |
| Rutin | Pajak & admin bank | 16.084 | Akumulasi |

### 2.2 Pemasukan (Kredit) — belum dicatat sebagai kas masuk di KSP

| Tanggal | Uraian | Kredit (Rp) | Keterangan |
|---------|--------|-------------|------------|
| 19/02/26 | Transfer dari Nina Karlina | 400.000 | Belum diinput |
| Feb 26 | QRIS (4 transaksi) | 73.422 | Belum diinput |
| 20/02/26 | Bunga bank (net of tax) | ~40.853 | Belum diinput |

---

## 3. Kesesuaian Data Pinjaman (Feb 2026)

| Nama (Bank) | Jumlah (Bank) | Di Excel KSP | Keterangan |
|-------------|----------------|--------------|------------|
| MIFTAHUL JANAH | 3.000.000 | ✅ 3.000.000 (03 Feb) | Sesuai |
| WIDIANTO NUGROHO | 2.000.000 | ✅ 2.000.000 (03 Feb) | Sesuai |
| TOHIR | 3.220.000 | ❌ Tidak ada | Belum masuk data pinjaman/transaksi |
| LALU ROYAN GIASTI(YA) | 1.000.000 | ✅ 1.000.000 (10 Feb) | Sesuai |
| ASTARA | 482.500 | ❌ Tidak ada di Feb | Di Excel Astara terakhir Jan; ini kemungkinan angsuran atau pencairan Feb |

**Total pencairan Feb di bank:** Rp 9.703.463  
**Total di Excel KSP (Feb):** Rp 6.000.000 (Miftahul + Widianto + Lalu Royan)  
**Kurang tercatat:** Rp 3.703.463 (Tohir 3.220.000 + Astara 482.500 + selisih lain)

---

## 4. Daftar Penyesuaian (Adjustment) agar Saldo Buku ≈ Saldo Bank

Agar saldo kas di aplikasi mendekati **Rp 7.385.510**, berikut penyesuaian yang perlu dipertimbangkan.

### 4.1 Pengeluaran kas yang harus dicatat (mengurangi kas)

| No | Uraian | Jumlah (Rp) | Jenis di aplikasi | Cara input saat ini |
|----|--------|-------------|--------------------|----------------------|
| 1 | Pembayaran Tohir (Feb) | 3.220.000 | Operasional / debet | Tutup Buku (TTP) atau jurnal penyesuaian* |
| 2 | Pencairan / angsuran Astara (Feb) | 482.500 | Pinjaman atau debet | Pastikan ada transaksi pinjaman/angsuran Astara Feb |
| 3 | Biaya admin & pajak bank | 16.084 | Operasional | TTP atau jurnal penyesuaian* |
| 4 | Pembayaran Tohir (Okt–Jan) + Beras (Okt–Jan) | Sisanya | Operasional | Idem; akumulasi agar mutasi tertutup |

\* Saat ini aplikasi **tidak punya menu khusus “Jurnal Penyesuaian Kas”**. Yang bisa dipakai:
- **Tutup Buku (TTP):** input **Kas** = saldo bank aktual (Rp 7.385.510) saat tutup buku periode tersebut, sehingga periode berikutnya buku besar mulai dengan angka yang benar.
- **Ke depan:** tambah fitur **Jurnal Kas** (insert ke `general_ledgers`: operasional, debet, pinjaman) agar tiap pengeluaran (Tohir, Beras, pajak, dll.) bisa dicatat satu per satu.

### 4.2 Pemasukan kas yang harus dicatat (menambah kas)

| No | Uraian | Jumlah (Rp) | Cara input |
|----|--------|-------------|------------|
| 1 | Transfer dari Nina Karlina | 400.000 | Transaksi nasabah (simpanan) atau jurnal kas masuk* |
| 2 | Pemasukan QRIS | 73.422 | Idem |
| 3 | Bunga bank (net) | ~40.853 | Jurnal kas masuk* |

\* Jika tidak ada menu jurnal, bisa dibantu lewat **Tutup Buku** dengan mengisi **Kas** = saldo bank (yang sudah termasuk semua masuk/keluar di atas).

---

## 5. Kesimpulan Singkat

1. **Penyebab selisih Rp 4.676.990:**  
   Pengeluaran **Tohir**, **Beras**, **Astara (Feb)**, dan **biaya bank/pajak** sudah keluar di rekening bank tetapi **tidak/belum** dicatat sebagai kas keluar di aplikasi. Sebagian pemasukan (Nina Karlina, QRIS, bunga) juga belum masuk di buku.

2. **Tohir & Beras:**  
   Konsisten tidak muncul di Excel KSP. Perlu diputuskan apakah ini **operasional** (biaya) atau **pinjaman/vendor**. Setelah itu, setiap pembayaran ke Tohir dan pembayaran Beras **harus dicatat sebagai pengeluaran kas** (debet/operasional) di aplikasi.

3. **Langkah praktis saat ini:**  
   - Gunakan **Pengurus → Rekap Mutasi Kas** untuk bandingkan kas buku vs saldo bank.  
   - Saat **Tutup Buku**, isi **Kas** = **Saldo bank** dari rekening koran (mis. Rp 7.385.510) agar periode berikutnya tidak mengambang.  
   - Ke depan: **catat setiap** transfer keluar (Tohir, Beras, Astara, dll.) dan masuk (Nina Karlina, QRIS, bunga) di aplikasi, atau sediakan **Jurnal Penyesuaian Kas** agar mutasi bisa dirapikan per transaksi.

---

*Dokumen ini merangkum analisis perbandingan Rekening Koran (PDF) dan Data Excel KSP (Okt 2025 – Feb 2026) untuk keperluan rekap mutasi kas.*
