<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Nasabah;
use App\Models\Transaksi;

class Nasaba extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $search = '';
    /** Filter status pinjaman: all, ada, tidak */
    public $filterPinjaman = 'all';
    /** Jumlah data per halaman */
    public $perPage = 10;
    /** ID nasabah yang dicentang (untuk bulk aksi) */
    public $selectedIds = [];
    /** Modal bulk transaksi */
    public $showBulkTransaksiModal = false;
    public $bulkJumlah = '';
    public $bulkJenis = '';
    public $no_rekening, $nama_lengkap, $alamat, $telp, $no_ktp, $saldo_akhir, $status, $foto;

    public function render()
    {
        $nasaba = Nasabah::orderBy('id', 'desc');

        if (!empty($this->search)) {
            $nasaba->where(function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('no_rekening', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterPinjaman === 'ada') {
            $nasaba->where('status_pinjaman', '1');
        } elseif ($this->filterPinjaman === 'tidak') {
            $nasaba->where('status_pinjaman', '0');
        }

        $nasaba = $nasaba->paginate((int) $this->perPage ?: 10);
        return view('livewire.nasabah.index', ['nasaba' => $nasaba]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterPinjaman()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /** Toggle satu nasabah di checklist */
    public function toggleSelect($id)
    {
        $id = (int) $id;
        if (in_array($id, $this->selectedIds)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        } else {
            $this->selectedIds = array_merge($this->selectedIds, [$id]);
        }
    }

    /** Pilih semua nasabah di halaman saat ini */
    public function selectAllPage($ids)
    {
        $ids = is_array($ids) ? $ids : json_decode($ids, true);
        if (!is_array($ids)) return;
        $this->selectedIds = array_values(array_unique(array_merge($this->selectedIds, $ids)));
    }

    /** Hapus semua pilihan */
    public function clearSelection()
    {
        $this->selectedIds = [];
    }

    /** Buka modal bulk transaksi (hanya jika ada yang terpilih) */
    public function openBulkTransaksiModal()
    {
        if (count($this->selectedIds) > 0) {
            $this->bulkJumlah = '';
            $this->bulkJenis = 'wajib';
            $this->showBulkTransaksiModal = true;
        }
    }

    public function closeBulkTransaksiModal()
    {
        $this->showBulkTransaksiModal = false;
        $this->bulkJumlah = '';
        $this->bulkJenis = '';
    }

    /** Proses bulk transaksi: wajib/sukarela saja, sama jumlah & jenis untuk semua terpilih */
    public function submitBulkTransaksi()
    {
        $this->validate([
            'bulkJumlah' => 'required|numeric|min:1',
            'bulkJenis'  => 'required|in:wajib,sukarela',
        ], [
            'bulkJumlah.required' => 'Jumlah wajib diisi.',
            'bulkJumlah.numeric'  => 'Jumlah harus angka.',
            'bulkJenis.in'       => 'Jenis hanya Simpanan Wajib atau Sukarela.',
        ]);

        $total = (float) $this->bulkJumlah;
        $jenis = $this->bulkJenis;
        $user_id = \Auth::id();
        $done = 0;
        $errors = [];

        foreach ($this->selectedIds as $nasabah_id) {
            $nasabah = Nasabah::find($nasabah_id);
            if (!$nasabah) continue;
            $saldo = (float) ($nasabah->saldo_akhir ?? 0);
            $nsaldo = $saldo + $total;
            $nasabah->saldo_akhir = $nsaldo;
            $nasabah->save();
            Transaksi::create([
                'nasabah_id' => $nasabah_id,
                'total' => $total,
                'jenis_transaksi' => $jenis,
                'user_id' => $user_id,
            ]);
            $done++;
        }

        $this->closeBulkTransaksiModal();
        $this->selectedIds = [];
        session()->flash('pesan', 'Bulk transaksi berhasil: ' . $done . ' nasabah ('.$jenis.' Rp '.number_format($total, 0, ',', '.').').');
    }

    public function edit($id)
    {
        $data = Nasabah::findOrFail($id);
        $this->data_id = $id;
        $this->no_rekening = $data->no_rekening;
        $this->nama_lengkap = $data->nama_lengkap;
        $this->telp = $data->telp;
        $this->alamat = $data->alamat;
        $this->no_ktp = $data->no_ktp;
        $this->saldo_akhir = $data->saldo_akhir;
        if ($data->status_pinjaman == '0'){
            $this->status = "Tidak ada pinjaman";
        }else{
            $this->status = "Ada pinjaman";
        }
        // keep existing foto filename to display in modal
        $this->foto = $data->foto;
    }

    public function resetInputFields()
    {
        $this->no_rekening = '';
        $this->nama_lengkap = '';
        $this->telp = '';
        $this->alamat = '';
        $this->no_ktp = '';
    }

    public function store()
    {
    	$validation = $this->validate([
    		'no_rekening' => 'required',
            'nama_lengkap'=> 'required',
            // 'foto' => 'image|max:1024',
    	]);
        if (!empty($this->foto)){
        // if foto is an uploaded file (UploadedFile) store it, else if it's a filename leave it
        if (is_object($this->foto)) {
            $name = md5($this->foto . microtime()).'.'.$this->foto->extension();          
            $this->foto->storeAs('foto', $name, 'public');
        } else {
            $name = $this->foto;
        }
    Nasabah::create([
            'no_rekening'=> $this->no_rekening,
            'nama_lengkap'=> $this->nama_lengkap,
            'telp'=> $this->telp,
            'alamat'=> $this->alamat,
            'no_ktp'=> $this->no_ktp,
            'foto'=> $name
        ]);
    }else{
        Nasabah::create([
            'no_rekening'=> $this->no_rekening,
            'nama_lengkap'=> $this->nama_lengkap,
            'telp'=> $this->telp,
            'alamat'=> $this->alamat,
            'no_ktp'=> $this->no_ktp,
        ]);
    }
    	session()->flash('pesan', 'Data telah ditambahkan.');
    	$this->resetInputFields();
    	$this->emit('nasabahStore');
    }

    public function update()
    {
        $validate = $this->validate([
    		'no_rekening' => 'required',
            'nama_lengkap' => 'required',
        ]);
        $data = Nasabah::find($this->data_id);
        $update = [
            'no_rekening'=> $this->no_rekening,
            'nama_lengkap'=> $this->nama_lengkap,
            'telp'=> $this->telp,
            'alamat'=> $this->alamat,
            'no_ktp'=> $this->no_ktp
        ];

        // handle foto upload if user provided a new file
        if ($this->foto && is_object($this->foto)) {
            $name = md5($this->foto . microtime()).'.'.$this->foto->extension();
            $this->foto->storeAs('foto', $name, 'public');

            // delete old file if exists
            if (!empty($data->foto) && Storage::disk('public')->exists('foto/'.$data->foto)) {
                Storage::disk('public')->delete('foto/'.$data->foto);
            }

            $update['foto'] = $name;
        }

        $data->update($update);
        session()->flash('pesan', 'Data telah diupdate.');
        $this->resetInputFields();
        $this->emit('nasabahStore');
    }

    public function delete($id)
    {
        Nasabah::find($id)->delete();
        session()->flash('pesan', 'Data telah dihapus.');
    }
}
