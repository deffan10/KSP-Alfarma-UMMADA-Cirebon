@extends('Template-0')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Penyesuaian Kas</h1>
        </div>
        <p class="text-muted">Mencatat penyesuaian kas di luar transaksi KSP: <strong>Pengurangan</strong> (operasional, vendor, beras, biaya bank) &mdash; mengurangi Kas Tersedia; <strong>Penambahan</strong> (sponsor, donasi, hibah) &mdash; menambah Kas Tersedia.</p>

        <div class="card card-primary mb-4">
            <div class="card-header">
                <h4>Tambah Jurnal Penyesuaian</h4>
            </div>
            <div class="card-body">
                {!! Form::open(['url' => route('penyesuaian-kas.store'), 'method' => 'post']) !!}
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="d-block mb-2">Tipe</label>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="tipe" id="tipe_keluar" value="keluar" class="form-check-input" checked>
                            <label for="tipe_keluar" class="form-check-label">Pengurangan kas</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="tipe" id="tipe_masuk" value="masuk" class="form-check-input">
                            <label for="tipe_masuk" class="form-check-label">Penambahan kas (sponsor, donasi, dll.)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Jumlah (Rp)</label>
                            <input type="number" name="total" class="form-control" min="1" required placeholder="Contoh: 3220000">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Keterangan (uraian)</label>
                            <input type="text" name="keterangan" class="form-control" maxlength="255" placeholder="Contoh: Sponsor acara; Pembayaran Tohir Feb">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <strong>Kas Tersedia saat ini:</strong> Rp {{ isset($kas->total) ? number_format($kas->total, 0, ',', '.') : '0' }}
            </div>
        </div>

        <table class="table table-striped table-bordered table-responsive-sm">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Keterangan</th>
                    <th class="text-end">Jumlah</th>
                    <th width="80">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penyesuaian as $p)
                @php $is_masuk = $p->jenis_transaksi === 'penyesuaian_masuk'; @endphp
                <tr>
                    <td>{{ $p->created_at ? $p->created_at->format('d-m-Y H:i') : '-' }}</td>
                    <td>
                        @if($is_masuk)
                            <span class="badge bg-success">Penambahan</span>
                        @else
                            <span class="badge bg-secondary">Pengurangan</span>
                        @endif
                    </td>
                    <td>{{ $p->keterangan ?? ($is_masuk ? 'Penyesuaian kas (penambahan)' : 'Penyesuaian kas / operasional luar KSP') }}</td>
                    <td class="text-end {{ $is_masuk ? 'text-success' : '' }}">{{ $is_masuk ? '+' : '' }} Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                    <td>
                        {!! Form::open(['url' => route('penyesuaian-kas.destroy', $p->id), 'method' => 'delete', 'class' => 'd-inline']) !!}
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus penyesuaian ini? Efek ke Kas Tersedia akan dibalik.');"><i class="fas fa-trash"></i></button>
                        {!! Form::close() !!}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada data penyesuaian kas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $penyesuaian->links() }}

        @if (Session::has('pesan'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ Session::get('pesan') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </section>
</div>
@stop
