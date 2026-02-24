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

        <p class="mt-3 small text-muted">
            <strong>Keterangan:</strong> "Kas Tersedia" di dashboard dihitung dari tabel <code>general_ledgers</code> (Buku Besar). Transaksi harian (simpanan, pinjaman, angsuran) tercatat di <code>transaksis</code>. Jika Buku Besar tidak disinkronkan dengan transaksi harian, angka akan berbeda. Masukkan Saldo Bank dari rekening koran untuk melihat mutasi.
        </p>
    </section>
</div>
@stop
