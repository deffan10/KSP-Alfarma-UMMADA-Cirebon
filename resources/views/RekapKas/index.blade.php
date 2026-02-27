@extends('Template-0')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Rekap Mutasi Kas</h1>
        </div>
        <p class="text-muted">Bandingkan kas dari Buku Besar (general_ledgers), dari Transaksi harian (transaksis), dan Saldo Bank dari rekening koran.</p>

        {{-- Input saldo bank dari rekening koran --}}
        {!! Form::open(['url' => url('rekap-kas'), 'method' => 'get', 'class' => 'mb-4']) !!}
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label">Saldo Bank (dari rekening koran)</label>
                <input type="text" name="saldo_bank" class="form-control" placeholder="Contoh: 7385510" value="{{ $saldo_bank_input !== null ? number_format($saldo_bank_input, 0, ',', '.') : '' }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Hitung Mutasi</button>
            </div>
        </div>
        {!! Form::close() !!}

        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered table-responsive-sm">
                    <tr><th colspan="2" class="text-center bg-light">Ringkasan Kas</th></tr>
                    <tr>
                        <td width="280">Kas Tersedia (dari Buku Besar / Dashboard)</td>
                        <td><strong>Rp {{ number_format($kas_dari_gl, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Kas dari Transaksi harian (masuk − keluar)</td>
                        <td><strong>Rp {{ number_format($kas_dari_transaksis, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Total masuk (transaksis)</td>
                        <td>Rp {{ number_format($masuk, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Total keluar (transaksis)</td>
                        <td>Rp {{ number_format($keluar, 0, ',', '.') }}</td>
                    </tr>
                    @if($saldo_bank_input !== null)
                    <tr class="table-info">
                        <td>Saldo Bank (input)</td>
                        <td><strong>Rp {{ number_format($saldo_bank_input, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Selisih: Buku Besar − Bank</td>
                        <td class="{{ $selisih_gl_vs_bank != 0 ? 'text-danger' : '' }}">Rp {{ number_format($selisih_gl_vs_bank, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Selisih: Transaksi − Bank (mutasi)</td>
                        <td class="{{ $selisih_trans_vs_bank != 0 ? 'text-warning' : '' }}">Rp {{ number_format($selisih_trans_vs_bank, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Selisih: Buku Besar − Transaksi</td>
                        <td class="{{ $selisih_gl_vs_trans != 0 ? 'text-secondary' : '' }}">Rp {{ number_format($selisih_gl_vs_trans, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-5">
                <table class="table table-sm table-bordered">
                    <tr><th class="text-center">Rincian Kas Masuk (transaksis)</th></tr>
                    @forelse($rincian_masuk as $r)
                    <tr><td>{{ $r->jenis_transaksi }}</td><td class="text-end">Rp {{ number_format($r->total, 0, ',', '.') }}</td></tr>
                    @empty
                    <tr><td class="text-muted">—</td></tr>
                    @endforelse
                </table>
            </div>
            <div class="col-md-5">
                <table class="table table-sm table-bordered">
                    <tr><th class="text-center">Rincian Kas Keluar (transaksis)</th></tr>
                    @forelse($rincian_keluar as $r)
                    <tr><td>{{ $r->jenis_transaksi }}</td><td class="text-end">Rp {{ number_format($r->total, 0, ',', '.') }}</td></tr>
                    @empty
                    <tr><td class="text-muted">—</td></tr>
                    @endforelse
                </table>
            </div>
        </div>

        <h6 class="mt-4">Rincian Buku Besar (general_ledgers)</h6>
        <p class="text-muted small">Komponen penyusun Kas Tersedia (Dashboard). Jika selisih selalu sama padahal sudah ada transaksi baru, cek apakah ada nominal yang sama di sini (mis. 882.500).</p>
        <div class="row">
            <div class="col-md-5">
                <table class="table table-sm table-bordered">
                    <tr><th class="text-center">Masuk (Buku Besar)</th></tr>
                    @forelse($gl_masuk as $r)
                    <tr><td>{{ $r->jenis_transaksi === 'penyesuaian_masuk' ? 'Penyesuaian (masuk)' : $r->jenis_transaksi }}</td><td class="text-end">Rp {{ number_format($r->total, 0, ',', '.') }}</td></tr>
                    @empty
                    <tr><td class="text-muted">—</td></tr>
                    @endforelse
                    <tr class="fw-bold"><td>Total masuk GL</td><td class="text-end">Rp {{ number_format($total_gl_masuk, 0, ',', '.') }}</td></tr>
                </table>
            </div>
            <div class="col-md-5">
                <table class="table table-sm table-bordered">
                    <tr><th class="text-center">Keluar (Buku Besar)</th></tr>
                    @forelse($gl_keluar as $r)
                    <tr><td>{{ $r->jenis_transaksi === 'penyesuaian' ? 'Penyesuaian (keluar)' : $r->jenis_transaksi }}</td><td class="text-end">Rp {{ number_format($r->total, 0, ',', '.') }}</td></tr>
                    @empty
                    <tr><td class="text-muted">—</td></tr>
                    @endforelse
                    <tr class="fw-bold"><td>Total keluar GL</td><td class="text-end">Rp {{ number_format($total_gl_keluar, 0, ',', '.') }}</td></tr>
                </table>
            </div>
        </div>

        <div class="mt-3 p-3 bg-light rounded small">
            <strong>Dari mana angka-angka ini?</strong>
            <ul class="mb-1 mt-1">
                <li><strong>Kas Tersedia (Buku Besar)</strong> = dari tabel <code>general_ledgers</code>: kas_masuk (wajib, sukarela, pengembalian, denda, shu) − kas_keluar (pinjaman, debet, operasional, penyesuaian). Berubah hanya saat <strong>Tutup Buku / SHU</strong> (posting ke Buku Besar) atau saat input <strong>Penyesuaian Kas</strong> / operasional.</li>
                <li><strong>Kas dari Transaksi harian</strong> = dari tabel <strong><code>transaksis</code></strong>: total masuk (wajib, sukarela, pengembalian, denda) − total keluar (pinjaman, debet). Berubah setiap ada transaksi nasabah (simpanan, pinjaman, angsuran). <strong>Operasional (mis. beli beras) tidak ada di transaksis</strong> — hanya di Buku Besar.</li>
            </ul>
            <strong>Kenapa Selisih Buku Besar − Transaksi?</strong> Karena Buku Besar diisi saat Tutup Buku atau Penyesuaian, sedangkan transaksi harian terus bertambah. Jadi angka Buku Besar bisa lebih besar atau lebih kecil daripada “kas murni dari transaksis” sampai ada sinkronisasi (Tutup Buku) atau penyesuaian.
            <br><strong>Biaya operasional (mis. beli beras):</strong> agar Kas Tersedia (Buku Besar) turun dan mendekati sisa di bank, catat pengeluaran itu di menu <strong>Administrasi → Penyesuaian Kas</strong> (jumlah + keterangan, mis. “Operasional warung – beli beras”). Itu akan mengurangi Kas Tersedia (Buku Besar) sebesar nominal yang diinput.
            <br><br><strong>Kenapa selisih bisa selalu sama (mis. 882.500) setelah ada transaksi baru?</strong> (1) Uang masuk angsuran dll hanya masuk ke Buku Harian (transaksis), tidak otomatis ke Buku Besar (general_ledgers). Jadi Buku Besar tidak naik, hanya Transaksi harian yang naik. Kalau Anda bandingkan Buku Besar dengan bank (yang sudah naik karena angsuran), selisih bisa terlihat tetap karena Buku Besar belum ikut naik. (2) Atau ada entri di Buku Besar yang salah/dobel. Cek Rincian Buku Besar di atas.
        </div>
    </section>
</div>
@stop
