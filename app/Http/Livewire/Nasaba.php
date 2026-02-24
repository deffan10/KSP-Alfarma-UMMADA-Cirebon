<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Nasabah;

class Nasaba extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $search = '';
    /** Filter status pinjaman: all, ada, tidak */
    public $filterPinjaman = 'all';
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

        $nasaba = $nasaba->paginate(10);
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
