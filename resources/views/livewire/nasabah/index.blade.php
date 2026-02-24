<div>
@include('livewire.nasabah.update')
@include('livewire.nasabah.detail')
<!-- @include('livewire.nasabah.create') -->
<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label">Filter Pinjaman</label>
        <select class="form-control form-control-sm" wire:model="filterPinjaman">
            <option value="all">Semua</option>
            <option value="ada">Ada pinjaman</option>
            <option value="tidak">Tidak ada pinjaman</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Cari</label>
        {{ Form::text('search', '', ['class' => 'form-control form-control-sm', 'wire:model' => 'search', 'placeholder' => 'Nama atau no. rekening']) }}
    </div>
</div>
{!! link_to('nasabah/create','+Tambah Data',['class'=>'btn btn-danger btn-sm']) !!} 
<br><br>
<table class="table table-striped table-responsive-sm">
    <thead>
    <tr>
        <th>Nomor Rekening</th>
        <th>Nama</th>
        <th>Saldo</th>
        <th>Status Pinjaman</th>
        <th>Aksi</th>
    </tr>
    </thead>
    <tbody>
    @isset($nasaba)
    @foreach($nasaba as $n)
    <tr>
        <td>{{ $n->no_rekening }}</td>
        <td>{{ $n->nama_lengkap }}</td>
        <td>Rp {{ number_format($n->saldo_akhir ?? 0, 0, ',', '.') }}</td>
        <td>{{ ($n->status_pinjaman ?? 0) == '1' ? 'Ada' : 'Tidak' }}</td>
        <td width="200" class="border-end-0">
            {!! link_to('nasabah/'.$n->id,' Transaksi ',['class'=>'fas fa-coins']) !!}
            <a href="#" data-toggle="modal" data-target="#updateModal" class="fas fa-edit" wire:click="edit({{ $n->id }})" style="color:#47c363"> Edit</a>
            <a href="#" wire:click.prevent="delete({{ $n->id }})" class="fas fa-trash-alt" style="color:#fc544b" onclick="confirm('Anda yakin?') || event.stopImmediatePropagation()"> Delete </a>
            <a href="#" data-toggle="modal" data-target="#detailModal" class="fas fa-eye" wire:click="edit({{ $n->id }})"> Detail</a>
        </td>
    </tr>
    @endforeach
    @endisset
    </tbody>
</table>
@if (Session::has('pesan'))
    <div class='alert alert-warning alert-dismissible fade show' role='alert'>
    {{ Session::get('pesan') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
    </div>
@endif
@isset($nasaba)
{{ $nasaba->links() }}
@endisset
</div>

