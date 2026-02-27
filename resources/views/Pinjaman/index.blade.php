@extends('Template-0')
@section('content')
<div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Pinjaman</h1>
          </div> 
    <div class="row justify-content-between">
        <div class="col-4">
            <a href="{{ url('pinjaman/recheck-active') }}" class="btn btn-warning">
                <i class="fas fa-sync"></i> Re-Check Status Pinjaman
            </a>
            {!! Form::open(array('url'=>route('pinjaman.proses-angsuran-bulk'),'method'=>'post','class'=>'d-inline')) !!}
            {!! Form::button('<i class="fas fa-money-check"></i> Proses Angsuran',['type'=>'submit','class'=>'btn btn-success',"onclick"=>"return confirm('Proses 1 angsuran untuk semua peminjam yang punya tagihan?')"]) !!}
            {!! Form::close() !!}
        </div>
        <div class="col-4">
            {!! Form::open(array('url'=>'pinjaman/search','class'=>'d-flex','role'=>'search')) !!}
            {!! Form::text('keyword',null,['class'=>'form-control me-2','placeholder'=>'Search']) !!}
            {!! Form::button('<i class="fas fa-search"></i>',['type'=>'submit','class'=>'btn btn-flat']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    <br><br>
<table class="table table-striped table-responsive-sm">
    <tr><th colspan="2" class="text-center"><b>PERINCIAN DANA</b></th></tr>
    @isset($kas,$tot_pinjam)
    <tr><td>DANA BERGULIR</td><td>{{ 'Rp.'. number_format($tot_pinjam->total) }}</td></tr>
    <tr><td>DANA TERSEDIA</td><td>{{ 'Rp.'. number_format($kas->total) }}</td></tr>
    <tr><td>Total Cicilan/Bulan (kita terima)</td><td>{{ 'Rp.'. number_format($total_cicilan_bulan ?? 0) }}</td></tr>
    @endisset
</table>
<br>
{!! link_to('pinjaman/create','+Tambah Data',['class'=>'btn btn-danger btn-sm']) !!}
<br><br>
<table class="table table-striped" >
    <tr><th colspan="9" class="text-center"><b>DATA PINJAMAN</b></th></tr>
    <tr><th>Tanggal Peminjaman</th><th>Nama</th><th>Jumlah Pinjaman</th><th>Jumlah Angsuran</th><th>Sisa Angsuran</th><th>Cicilan/Bulan</th><th>Keterangan</th><th colspan="2"></th></tr>
    @isset($pinjaman)
    @foreach($pinjaman as $p)
    <tr><td>{{ tgl_id($p->created_at) }}</td><td>{{ $p->nama_lengkap }}</td><td>{{ number_format($p->total) }}</td><td>{{ $p->angsuran }}</td><td>{{ $sisa_angsuran[$p->id] ?? 0 }}</td><td>Rp {{ isset($cicilan[$p->id]) ? number_format($cicilan[$p->id], 0, ',', '.') : '-' }}</td><td>{{ $p->ket }}</td>
    <td width="240" class="">
        @php
        $hasUnpaid = \App\Models\Angsuran::where(['pinjaman_id' => $p->id,'status'=>'1'])->exists();
        $hasPengembalian = \DB::table('pengembalians')->where('pinjaman_id', $p->id)->exists();
        $hasPaidAngsuran = \DB::table('angsurans')->where('pinjaman_id', $p->id)->where('status', '0')->exists();
        $showTagihkanUlang = $hasPengembalian || $hasPaidAngsuran;
        @endphp
        @if ($hasUnpaid)
        {!! Form::open(array('url'=>'pinjaman/'.$p->id,'method'=>'delete')) !!}
        {!! Form::button('<i class="fas fa-times"></i> Batalkan',['type'=>'submit','class'=>'btn btn-danger btn-sm',"onclick"=>"return confirm('Batalkan pinjaman? Data pinjaman akan dihapus.')"]) !!}
        {!! Form::close() !!}
        {!! link_to('pinjaman/'.$p->id,'Angsuran',['class'=>'btn btn-warning btn-sm']) !!}
        <a href="{{ route('pinjaman.relaksasi', $p->id) }}" class="btn btn-info btn-sm" title="Ubah jumlah angsuran (relaksasi)"><i class="fas fa-calendar-alt"></i> Relaksasi</a>
        @else
        {!! Form::open(array('url'=>route('pinjaman.lunas', $p->id),'method'=>'post')) !!}
        {!! Form::button('<i class="fas fa-check"></i> Lunas',['type'=>'submit','class'=>'btn btn-success btn-sm',"onclick"=>"return confirm('Tandai pinjaman sebagai lunas? Nasabah dapat mengajukan pinjaman baru.')"]) !!}
        {!! Form::close() !!}
        {!! link_to('pinjaman/'.$p->id,'Angsuran',['class'=>'btn btn-warning btn-sm disabled']) !!}
        @endif
        @if ($showTagihkanUlang)
        {!! Form::open(array('url'=>route('pinjaman.tagihkan-ulang', $p->id),'method'=>'post','class'=>'d-inline')) !!}
        {!! Form::button('<i class="fas fa-undo"></i> Tagihkan Ulang',['type'=>'submit','class'=>'btn btn-outline-secondary btn-sm',"onclick"=>"return confirm('Batalkan 1 angsuran terakhir untuk peminjam ini? Angsuran akan ditambah lagi.')"]) !!}
        {!! Form::close() !!}
        @endif
    @endforeach
    {!! $pinjaman->render() !!}
    @if (Session::has('pesan'))
    <div class='alert alert-warning alert-dismissible fade show' role='alert'>
    {{ Session::get('pesan') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @endisset
    </td></tr>
</table>
</section> 
</div> 
@stop