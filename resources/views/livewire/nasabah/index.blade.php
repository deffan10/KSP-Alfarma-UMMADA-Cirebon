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
    <div class="col-md-3">
        <label class="form-label">Tampilkan</label>
        <select class="form-control form-control-sm" wire:model="perPage">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <small class="text-muted">data per halaman</small>
    </div>
</div>
@if(count($selectedIds) > 0)
<div class="alert alert-info py-2 mb-2 d-flex align-items-center justify-content-between flex-wrap">
    <span><strong>{{ count($selectedIds) }} data terpilih.</strong></span>
    <span>
        <button type="button" class="btn btn-sm btn-primary me-1" wire:click="openBulkTransaksiModal">
            <i class="fas fa-cash-register"></i> Transaksi
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="clearSelection">Batal pilih</button>
    </span>
</div>
@endif
{!! link_to('nasabah/create','+Tambah Data',['class'=>'btn btn-danger btn-sm']) !!}
<br><br>
<table class="table table-striped table-responsive-sm">
    <thead>
    <tr>
        <th width="40">
            <button type="button" class="btn btn-sm btn-outline-secondary p-0 px-1" wire:click="selectAllPage({{ json_encode($nasaba->pluck('id')->toArray() ?? []) }})" title="Pilih semua di halaman ini">
                <i class="fas fa-check-double"></i>
            </button>
        </th>
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
        <td>
            <input type="checkbox" wire:click="toggleSelect({{ $n->id }})" @if(in_array($n->id, $selectedIds)) checked @endif>
        </td>
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

{{-- Modal Bulk Transaksi --}}
@if($showBulkTransaksiModal)
<div class="modal show" tabindex="-1" style="display:block; background: rgba(0,0,0,.5);" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Transaksi — {{ count($selectedIds) }} nasabah</h5>
                <button type="button" class="btn-close" wire:click="closeBulkTransaksiModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Jumlah dan jenis yang sama akan diterapkan ke semua nasabah terpilih (misal: simpanan wajib bulanan).</p>
                <div class="mb-3">
                    <label class="form-label">Jumlah (Rp)</label>
                    <input type="number" class="form-control" wire:model="bulkJumlah" min="1" step="1" placeholder="Contoh: 50000">
                    @error('bulkJumlah') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Transaksi</label>
                    <select class="form-control" wire:model="bulkJenis">
                        <option value="wajib">Simpanan Wajib</option>
                        <option value="sukarela">Simpanan Sukarela</option>
                    </select>
                    @error('bulkJenis') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeBulkTransaksiModal">Batal</button>
                <button type="button" class="btn btn-primary" wire:click="submitBulkTransaksi">
                    <i class="fas fa-cash-register"></i> Proses
                </button>
            </div>
        </div>
    </div>
</div>
@endif
</div>

