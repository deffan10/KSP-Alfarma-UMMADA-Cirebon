<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RekapKasController extends Controller
{
    public function __construct()
    {
        $this->middleware('check');
    }

    /**
     * Rekap mutasi kas: bandingkan kas dari general_ledgers vs transaksis vs saldo bank.
     */
    public function index(Request $request)
    {
        $saldo_bank_raw = $request->input('saldo_bank');
        $saldo_bank_input = $saldo_bank_raw !== null && $saldo_bank_raw !== '' ? (float) preg_replace('/[^0-9]/', '', $saldo_bank_raw) : null;
        if ($saldo_bank_input !== null && $saldo_bank_input == 0) {
            $saldo_bank_input = null;
        }

        // Kas dari view (general_ledgers) — yang dipakai dashboard
        $kas_gl = \DB::table('sisa_kas')->first();
        $kas_dari_gl = $kas_gl ? (float) $kas_gl->total : 0;

        // Kas dari transaksis (buku harian)
        $masuk = \DB::table('transaksis')
            ->whereIn('jenis_transaksi', ['wajib', 'sukarela', 'pengembalian', 'denda'])
            ->sum('total');
        $keluar = \DB::table('transaksis')
            ->whereIn('jenis_transaksi', ['pinjaman', 'debet'])
            ->sum('total');
        $kas_dari_transaksis = $masuk - $keluar;

        // Rincian per jenis (transaksis)
        $rincian_masuk = \DB::table('transaksis')
            ->whereIn('jenis_transaksi', ['wajib', 'sukarela', 'pengembalian', 'denda'])
            ->selectRaw('jenis_transaksi, SUM(total) as total')
            ->groupBy('jenis_transaksi')
            ->get();
        $rincian_keluar = \DB::table('transaksis')
            ->whereIn('jenis_transaksi', ['pinjaman', 'debet'])
            ->selectRaw('jenis_transaksi, SUM(total) as total')
            ->groupBy('jenis_transaksi')
            ->get();

        $selisih_gl_vs_trans = $kas_dari_gl - $kas_dari_transaksis;
        $selisih_gl_vs_bank = $saldo_bank_input !== null ? $kas_dari_gl - $saldo_bank_input : null;
        $selisih_trans_vs_bank = $saldo_bank_input !== null ? $kas_dari_transaksis - $saldo_bank_input : null;

        // Rincian Buku Besar (general_ledgers) per jenis — untuk cek kenapa selisih bisa selalu sama
        $gl_masuk = \DB::table('general_ledgers')
            ->whereIn('jenis_transaksi', ['wajib', 'sukarela', 'pengembalian', 'denda', 'shu'])
            ->where('status_pembukuan', '1')
            ->selectRaw('jenis_transaksi, SUM(total) as total')
            ->groupBy('jenis_transaksi')
            ->get();
        $gl_keluar = \DB::table('general_ledgers')
            ->whereIn('jenis_transaksi', ['debet', 'operasional', 'pinjaman', 'penyesuaian'])
            ->where('status_pembukuan', '1')
            ->selectRaw('jenis_transaksi, SUM(total) as total')
            ->groupBy('jenis_transaksi')
            ->get();
        $total_gl_masuk = $gl_masuk->sum('total');
        $total_gl_keluar = $gl_keluar->sum('total');

        return view('RekapKas.index', [
            'kas_dari_gl' => $kas_dari_gl,
            'kas_dari_transaksis' => $kas_dari_transaksis,
            'masuk' => $masuk,
            'keluar' => $keluar,
            'rincian_masuk' => $rincian_masuk,
            'rincian_keluar' => $rincian_keluar,
            'saldo_bank_input' => $saldo_bank_input,
            'selisih_gl_vs_trans' => $selisih_gl_vs_trans,
            'selisih_gl_vs_bank' => $selisih_gl_vs_bank,
            'selisih_trans_vs_bank' => $selisih_trans_vs_bank,
            'gl_masuk' => $gl_masuk,
            'gl_keluar' => $gl_keluar,
            'total_gl_masuk' => $total_gl_masuk,
            'total_gl_keluar' => $total_gl_keluar,
        ]);
    }
}
