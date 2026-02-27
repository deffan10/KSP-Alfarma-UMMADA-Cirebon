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
     * Daftar jurnal penyesuaian kas / operasional luar KSP + form tambah.
     */
    public function index()
    {
        $data['penyesuaian'] = General_ledger::where('jenis_transaksi', 'penyesuaian')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $data['kas'] = \DB::table('sisa_kas')->first();
        return view('PenyesuaianKas.index', $data);
    }

    /**
     * Simpan jurnal penyesuaian kas (pengeluaran kas / operasional luar KSP).
     */
    public function store(Request $request)
    {
        $request->validate([
            'total' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $total = (int) $request->total;
        $user_id = \Auth::id();

        General_ledger::create([
            'transaksi_id' => 0,
            'total' => $total,
            'jenis_transaksi' => 'penyesuaian',
            'keterangan' => $request->keterangan ?: 'Penyesuaian kas / operasional luar KSP',
            'user_id' => $user_id,
            'status_pembukuan' => '1',
        ]);

        Session::flash('pesan', 'Jurnal penyesuaian kas berhasil dicatat. Kas tersedia akan berkurang sebesar Rp ' . number_format($total, 0, ',', '.'));
        return redirect()->route('penyesuaian-kas.index');
    }

    /**
     * Hapus satu jurnal penyesuaian (mis. jika salah input / duplikat). Kas Tersedia akan bertambah kembali sebesar nominal tersebut.
     */
    public function destroy($id)
    {
        $row = General_ledger::where('id', $id)->where('jenis_transaksi', 'penyesuaian')->first();
        if (!$row) {
            Session::flash('pesan', 'Data penyesuaian tidak ditemukan.');
            return redirect()->route('penyesuaian-kas.index');
        }
        $total = $row->total;
        $row->delete();
        Session::flash('pesan', 'Penyesuaian Rp ' . number_format($total, 0, ',', '.') . ' telah dihapus. Kas Tersedia akan bertambah kembali.');
        return redirect()->route('penyesuaian-kas.index');
    }
}
