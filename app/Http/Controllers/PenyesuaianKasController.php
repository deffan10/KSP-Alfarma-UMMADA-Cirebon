<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\General_ledger;
use Session;

class PenyesuaianKasController extends Controller
{
    public function __construct()
    {
        $this->middleware('check');
    }

    /**
     * Daftar jurnal penyesuaian kas (pengurangan & penambahan) + form tambah.
     */
    public function index()
    {
        $data['penyesuaian'] = General_ledger::whereIn('jenis_transaksi', ['penyesuaian', 'penyesuaian_masuk'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $data['kas'] = \DB::table('sisa_kas')->first();
        return view('PenyesuaianKas.index', $data);
    }

    /**
     * Simpan jurnal penyesuaian kas.
     * tipe: keluar = pengurangan kas (operasional luar KSP), masuk = penambahan kas (sponsor, donasi, dll).
     */
    public function store(Request $request)
    {
        $request->validate([
            'total' => 'required|numeric|min:1',
            'tipe' => 'required|in:keluar,masuk',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $total = (int) $request->total;
        $user_id = \Auth::id();
        $is_masuk = $request->tipe === 'masuk';
        $jenis = $is_masuk ? 'penyesuaian_masuk' : 'penyesuaian';
        $keterangan_default = $is_masuk ? 'Penyesuaian kas (penambahan)' : 'Penyesuaian kas / operasional luar KSP';

        General_ledger::create([
            'transaksi_id' => 0,
            'total' => $total,
            'jenis_transaksi' => $jenis,
            'keterangan' => $request->keterangan ?: $keterangan_default,
            'user_id' => $user_id,
            'status_pembukuan' => '1',
        ]);

        if ($is_masuk) {
            Session::flash('pesan', 'Jurnal penyesuaian kas (penambahan) berhasil dicatat. Kas tersedia bertambah Rp ' . number_format($total, 0, ',', '.'));
        } else {
            Session::flash('pesan', 'Jurnal penyesuaian kas berhasil dicatat. Kas tersedia berkurang Rp ' . number_format($total, 0, ',', '.'));
        }
        return redirect()->route('penyesuaian-kas.index');
    }

    /**
     * Hapus satu jurnal penyesuaian. Efek ke Kas Tersedia tergantung tipe (penambahan dihapus = kas berkurang, pengurangan dihapus = kas bertambah).
     */
    public function destroy($id)
    {
        $row = General_ledger::where('id', $id)->whereIn('jenis_transaksi', ['penyesuaian', 'penyesuaian_masuk'])->first();
        if (!$row) {
            Session::flash('pesan', 'Data penyesuaian tidak ditemukan.');
            return redirect()->route('penyesuaian-kas.index');
        }
        $total = $row->total;
        $was_masuk = $row->jenis_transaksi === 'penyesuaian_masuk';
        $row->delete();
        if ($was_masuk) {
            Session::flash('pesan', 'Penyesuaian (penambahan) Rp ' . number_format($total, 0, ',', '.') . ' telah dihapus. Kas Tersedia akan berkurang sebesar nominal tersebut.');
        } else {
            Session::flash('pesan', 'Penyesuaian Rp ' . number_format($total, 0, ',', '.') . ' telah dihapus. Kas Tersedia akan bertambah kembali.');
        }
        return redirect()->route('penyesuaian-kas.index');
    }
}
