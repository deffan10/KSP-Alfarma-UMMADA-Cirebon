@extends('Template-0')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Relaksasi Angsuran</h1>
        </div>

        <p class="text-muted">Ubah jumlah angsuran (mis. 5 bulan jadi 7 bulan). Jumlah cicilan per bulan akan dihitung ulang otomatis.</p>

        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><td width="220">Nama</td><td>{{ $pinjaman->nama_lengkap }}</td></tr>
                    <tr><td>No. Rekening</td><td>{{ $pinjaman->no_rekening }}</td></tr>
                    <tr><td>Jumlah Pinjaman</td><td>Rp {{ number_format($pinjaman->total, 0, ',', '.') }}</td></tr>
                    <tr><td>Bunga</td><td>{{ $pinjaman->persen }}% ({{ $pinjaman->skema }})</td></tr>
                    <tr><td>Jumlah angsuran saat ini</td><td>{{ $pinjaman->angsuran }} bulan</td></tr>
                    <tr><td>Sudah dibayar</td><td>{{ $paid_count }} kali</td></tr>
                    <tr><td>Sisa belum bayar</td><td>{{ $unpaid_count }} kali</td></tr>
                </table>

                {!! Form::open(['url' => url('pinjaman/relaksasi'), 'method' => 'post']) !!}
                {!! Form::hidden('pinjaman_id', $pinjaman->id) !!}
                <div class="form-group">
                    <label for="new_angsuran">Jumlah angsuran baru (bulan)</label>
                    {!! Form::number('new_angsuran', $pinjaman->angsuran, ['class' => 'form-control', 'min' => max(1, $paid_count), 'required' => true]) !!}
                    <small class="form-text text-muted">Minimal sama dengan angsuran yang sudah dibayar ({{ $paid_count }}).</small>
                </div>
                <div class="form-group">
                    {!! Form::submit('Simpan & Hitung Ulang Cicilan', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ url('pinjaman/'.$pinjaman->id) }}" class="btn btn-secondary">Batal</a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
</div>
@stop
