<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use App\Models\Nasabah;
use App\Models\General_ledger;
use App\Models\Transaksi;
use Session;
use Illuminate\Support\Carbon;

class PinjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('check');
    }

    /**
     * Untuk tiap pinjaman: cicilan yang dipakai = angsuran belum bayar (status 1) jika ada (hasil relaksasi/create), else angsuran terakhir.
     */
    private function getCurrentCicilanPerPinjaman($pinjaman_ids)
    {
        $groups = Angsuran::whereIn('pinjaman_id', $pinjaman_ids)->orderBy('pinjaman_id')->orderBy('id')->get()->groupBy('pinjaman_id');
        return $groups->map(function ($group) {
            $unpaid = $group->where('status', '1')->first();
            if ($unpaid) {
                return (float) $unpaid->jumlah_cicilan;
            }
            return (float) $group->last()->jumlah_cicilan;
        });
    }

    public function index()
    {
        $data['pinjaman'] = Pinjaman::where('status','1')->paginate(20);
        // Kas Tersedia = Buku Besar (sama dengan Dashboard)
        $data['kas'] = \DB::table('sisa_kas')->first();
        $data['tot_pinjam'] = \DB::table('tot_pinjam')->first();
        $ids = $data['pinjaman']->pluck('id');
        $data['cicilan'] = $this->getCurrentCicilanPerPinjaman($ids);
        $data['sisa_angsuran'] = Angsuran::whereIn('pinjaman_id', $ids)->where('status', '1')->selectRaw('pinjaman_id, count(*) as sisa')->groupBy('pinjaman_id')->pluck('sisa', 'pinjaman_id');
        // Total cicilan/bulan yang kita terima (dari semua pinjaman aktif, pakai cicilan terkini setelah relaksasi)
        $ids_aktif = Pinjaman::where('status', '1')->pluck('id');
        $currentCicilan = $this->getCurrentCicilanPerPinjaman($ids_aktif);
        $data['total_cicilan_bulan'] = $currentCicilan->sum();
        return view('Pinjaman.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('Pinjaman.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $skema=$request->skema;
        $angsuran=$request->angsuran;
        $total=$request->total;
        $persen=$request->persen;
        $no_rekening=$request->no_rekening;
        $nasabah = \DB::table('nasabahs')->where('no_rekening','=',$no_rekening)->first();
        $nasabah_stat = $nasabah->status_pinjaman;
        $nasabah_id = $nasabah->id;
        $user_id = \Auth::user()->id;
        $sisa_kas = \DB::table('sisa_kas')->first();
        if ($nasabah_stat=='0'){
            $data = array(
                'no_rekening'=>$no_rekening,
                'nama_lengkap'=>$request->nama_lengkap,
                'total'=>$total,
                'angsuran'=>$angsuran,
                'persen'=>$persen,
                'skema'=>$skema,
                'ket'=>$request->ket
            );
           $loan = Pinjaman::create($data);
           $pinjaman_id = $loan->id;
           if ($sisa_kas<$total)
           {
            $pesan = "Maaf Kas masih belum memiliki dana!!";
           }
           elseif ($skema=='flat')
           {
               $j_cicil=((($persen/100)*$total)+$total)/$angsuran;
               $angsur=array(
                    'pinjaman_id'=>$pinjaman_id,
                    'jumlah_cicilan'=>$j_cicil
               );
               for($i=1;$i<=$angsuran;$i++){
                   Angsuran::create($angsur);
               }
               $trans = \DB::table('transaksis')->insertGetId([
                        'nasabah_id' => $nasabah_id,
                        'total' => $total,
                        'jenis_transaksi' => 'pinjaman',
                        'user_id' => $user_id,
                        'created_at' => now()
                        ]);
               \DB::table('nasabahs')->where('no_rekening',$no_rekening)->update(['status_pinjaman'=>'1']);
               \DB::table('pinjamans')->where('id',$pinjaman_id)->update(['transaksi_id'=>$trans]);
           }else{      
               $termin=$angsuran;
               $tot_trans=$total;      
               for($i=1;$i<=$termin;$i++){
                   $j_cicil=((($persen/100)*$total)+$total)/$angsuran;
                   $angsur=array(
                    'pinjaman_id'=>$loan->id,
                    'jumlah_cicilan'=>$j_cicil
                   );
                   Angsuran::create($angsur);
                   $total=$total-$j_cicil;
                   $angsuran--;
               }
               $trans = \DB::table('transaksis')->insertGetId([
                'nasabah_id' => $nasabah_id,
                'total' => $tot_trans,
                'jenis_transaksi' => 'pinjaman',
                'user_id' => $user_id,
                'created_at' => now()
                ]);
               \DB::table('nasabahs')->where('no_rekening',$no_rekening)->update(['status_pinjaman'=>'1']);
               \DB::table('pinjamans')->where('id',$pinjaman_id)->update(['transaksi_id'=>$trans]);
           }
        }else{
            $pesan = "Maaf Nasabah masih memiliki pinjaman!!";
        }
        if (isset($pesan)){
            Session::flash('pesan',$pesan);
        }
        return redirect('pinjaman');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
         $data['angsuran'] = \DB::table('angsurans')
         ->join('pinjamans','pinjamans.id','=','angsurans.pinjaman_id')
         ->where('angsurans.pinjaman_id','=',$id)
         ->where('angsurans.status','=','1')
         ->select('angsurans.id','angsurans.pinjaman_id','angsurans.jumlah_cicilan','pinjamans.nama_lengkap','pinjamans.no_rekening','angsurans.created_at','pinjamans.skema')
         ->get();
         if ($data['angsuran']->isEmpty()){
            return redirect('pinjaman');
         }else{    
            return view('Pinjaman.show',$data);
         }
    }

    public function search(Request $request)
    {
        $search = $request['keyword'];
        $data['pinjaman'] = Pinjaman::where('nama_lengkap','LIKE',"%{$search}%")->where('status','1')->paginate(5);
        $data['kas'] = \DB::table('sisa_kas')->first();
        $data['tot_pinjam'] = \DB::table('tot_pinjam')->first();
        $ids = $data['pinjaman']->pluck('id');
        $data['cicilan'] = $ids->isNotEmpty() ? $this->getCurrentCicilanPerPinjaman($ids) : collect();
        $data['sisa_angsuran'] = $ids->isNotEmpty() ? Angsuran::whereIn('pinjaman_id', $ids)->where('status', '1')->selectRaw('pinjaman_id, count(*) as sisa')->groupBy('pinjaman_id')->pluck('sisa', 'pinjaman_id') : collect();
        $ids_aktif = Pinjaman::where('status', '1')->pluck('id');
        $currentCicilan = $this->getCurrentCicilanPerPinjaman($ids_aktif);
        $data['total_cicilan_bulan'] = $currentCicilan->sum();
        return view('Pinjaman.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        \DB::table('angsurans')->where('id',$request->angsuran_id)->update(['status'=>'0']);
        $nasabah = \DB::table('nasabahs')
                    ->where('nama_lengkap','=',$request->nama_lengkap)
                    ->where('no_rekening','=',$request->no_rekening)
                    ->first();
        $user_id = \Auth::user()->id;
        \DB::table('transaksis')->insert([
            'nasabah_id' => $nasabah->id,
            'total' => $request->jumlah_cicilan,
            'jenis_transaksi' => 'pengembalian',
            'user_id' => $user_id,
            'created_at' => now()
            ]);
        \DB::table('pengembalians')->insert([
            'pinjaman_id' => $request->pinjaman_id,
            'jumlah_cicilan' => $request->jumlah_cicilan,
            'created_at' => now()
            ]);
            
        // Check if all installments are paid
        $remaining_installments = \DB::table('angsurans')
            ->where('pinjaman_id', $request->pinjaman_id)
            ->where('status', '1')
            ->count();
            
        if ($remaining_installments === 0) {
            // If no active installments remain, update nasabah status
            \DB::table('nasabahs')->where('no_rekening', $request->no_rekening)->update(['status_pinjaman' => '0']);
            \DB::table('pinjamans')->where('id', $request->pinjaman_id)->update(['status' => '0']);
        }

        return redirect("pinjaman/$request->pinjaman_id");
    }

    /**
     * Proses satu angsuran untuk semua peminjam aktif (bulk). Tiap pinjaman yang punya angsuran tertunggak diproses 1 angsuran.
     */
    public function proses_angsuran_bulk()
    {
        Session::put('last_bulk_angsuran_run', now()->toDateTimeString());

        $pinjamans = Pinjaman::where('status', '1')->get();
        $user_id = \Auth::id();
        $processed = 0;
        $lunas = 0;

        foreach ($pinjamans as $pinjaman) {
            $angsuran = \DB::table('angsurans')
                ->where('pinjaman_id', $pinjaman->id)
                ->where('status', '1')
                ->orderBy('id')
                ->first();

            if (!$angsuran) {
                continue;
            }

            $nasabah = \DB::table('nasabahs')->where('no_rekening', $pinjaman->no_rekening)->first();
            if (!$nasabah) {
                continue;
            }

            $jumlah_cicilan = (float) $angsuran->jumlah_cicilan;

            \DB::table('angsurans')->where('id', $angsuran->id)->update(['status' => '0']);
            \DB::table('transaksis')->insert([
                'nasabah_id' => $nasabah->id,
                'total' => $jumlah_cicilan,
                'jenis_transaksi' => 'pengembalian',
                'user_id' => $user_id,
                'created_at' => now(),
            ]);
            \DB::table('pengembalians')->insert([
                'pinjaman_id' => $pinjaman->id,
                'jumlah_cicilan' => $jumlah_cicilan,
                'created_at' => now(),
            ]);

            $remaining = \DB::table('angsurans')->where('pinjaman_id', $pinjaman->id)->where('status', '1')->count();
            if ($remaining === 0) {
                \DB::table('nasabahs')->where('no_rekening', $pinjaman->no_rekening)->update(['status_pinjaman' => '0']);
                \DB::table('pinjamans')->where('id', $pinjaman->id)->update(['status' => '0']);
                $lunas++;
            }
            $processed++;
        }

        if ($processed === 0) {
            Session::flash('pesan', 'Tidak ada angsuran tertunggak. Semua peminjam sudah tidak ada tagihan angsuran.');
        } else {
            Session::flash('pesan', 'Proses angsuran selesai: ' . $processed . ' peminjam diproses.' . ($lunas > 0 ? ' ' . $lunas . ' pinjaman lunas.' : ''));
        }

        return redirect('pinjaman');
    }

    /**
     * Tagihkan ulang (per peminjam): batalkan 1 angsuran terakhir untuk pinjaman ini.
     * Juga menangani kasus "orphan": transaksi pengembalian masuk 2x tapi insert pengembalians gagal (error out of range),
     * jadi tidak ada record di pengembalians tapi ada angsuran status=0 dan transaksi pengembalian.
     */
    public function tagihkan_ulang($id)
    {
        $pinjaman_id = (int) $id;
        $pinjaman = \DB::table('pinjamans')->where('id', $pinjaman_id)->first();
        if (!$pinjaman) {
            Session::flash('pesan', 'Pinjaman tidak ditemukan.');
            return redirect('pinjaman');
        }

        $nasabah = \DB::table('nasabahs')->where('no_rekening', $pinjaman->no_rekening)->first();
        if (!$nasabah) {
            Session::flash('pesan', 'Data nasabah tidak ditemukan.');
            return redirect('pinjaman');
        }

        $angsuran = \DB::table('angsurans')
            ->where('pinjaman_id', $pinjaman_id)
            ->where('status', '0')
            ->orderBy('id', 'desc')
            ->first();

        if (!$angsuran) {
            Session::flash('pesan', 'Tidak ada angsuran yang sudah dibayar untuk pinjaman ini.');
            return redirect('pinjaman');
        }

        $jumlah_cicilan = (float) $angsuran->jumlah_cicilan;
        $p = \DB::table('pengembalians')
            ->where('pinjaman_id', $pinjaman_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($p) {
            $transaksi = \DB::table('transaksis')
                ->where('nasabah_id', $nasabah->id)
                ->where('total', $p->jumlah_cicilan)
                ->where('jenis_transaksi', 'pengembalian')
                ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($p->created_at) - 5))
                ->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($p->created_at) + 5))
                ->first();
            if ($transaksi) {
                \DB::table('transaksis')->where('id', $transaksi->id)->delete();
            }
            \DB::table('pengembalians')->where('id', $p->id)->delete();
        } else {
            $transaksi = \DB::table('transaksis')
                ->where('nasabah_id', $nasabah->id)
                ->where('total', $jumlah_cicilan)
                ->where('jenis_transaksi', 'pengembalian')
                ->orderBy('created_at', 'desc')
                ->first();
            if ($transaksi) {
                \DB::table('transaksis')->where('id', $transaksi->id)->delete();
            }
        }

        \DB::table('angsurans')->where('id', $angsuran->id)->update(['status' => '1']);

        if ($pinjaman->status === '0') {
            \DB::table('pinjamans')->where('id', $pinjaman_id)->update(['status' => '1']);
            \DB::table('nasabahs')->where('no_rekening', $pinjaman->no_rekening)->update(['status_pinjaman' => '1']);
        }

        Session::flash('pesan', 'Tagihkan ulang berhasil: 1 angsuran dibatalkan untuk peminjam ini. Sisa angsuran bertambah.');
        return redirect('pinjaman');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Tandai pinjaman sebagai lunas (semua angsuran sudah dibayar).
     * Pinjaman hilang dari list aktif, nasabah bisa ajukan pinjaman baru.
     */
    public function mark_lunas($id)
    {
        $pinjaman = Pinjaman::findOrFail($id);
        $remaining = \DB::table('angsurans')->where('pinjaman_id', $id)->where('status', '1')->count();
        if ($remaining > 0) {
            Session::flash('pesan', 'Pinjaman belum lunas. Masih ada ' . $remaining . ' angsuran yang belum dibayar.');
            return redirect('pinjaman');
        }
        \DB::table('nasabahs')->where('no_rekening', $pinjaman->no_rekening)->update(['status_pinjaman' => '0']);
        \DB::table('pinjamans')->where('id', $id)->update(['status' => '0']);
        \DB::table('angsurans')->where('pinjaman_id', $id)->delete();
        \DB::table('pengembalians')->where('pinjaman_id', $id)->update(['status_pinjam' => '0']);
        Session::flash('pesan', 'Pinjaman ditandai lunas. Nasabah dapat mengajukan pinjaman baru.');
        return redirect('pinjaman');
    }

    /**
     * Batalkan/hapus pinjaman (hanya ketika masih ada angsuran belum bayar).
     */
    public function destroy($id)
    {
        $pinjaman = Pinjaman::findOrFail($id);
        $remaining = \DB::table('angsurans')->where('pinjaman_id', $id)->where('status', '1')->count();
        if ($remaining > 0) {
            $no_rekening = $pinjaman->no_rekening;
            $transaksi_id = $pinjaman->transaksi_id;
            \DB::table('nasabahs')->where('no_rekening', $no_rekening)->update(['status_pinjaman' => '0']);
            \DB::table('angsurans')->where('pinjaman_id', $id)->delete();
            \DB::table('transaksis')->where('id', $transaksi_id)->delete();
            \DB::table('general_ledgers')->where('transaksi_id', $transaksi_id)->delete();
            \DB::table('pinjamans')->where('id', $id)->delete();
            Session::flash('pesan', 'Pinjaman dibatalkan dan dihapus.');
        } else {
            return $this->mark_lunas($id);
        }
        return redirect('pinjaman');
    }

    public function get_name(Request $request)
    {
        $data = \DB::table('nasabahs')->where('no_rekening','=',$request->nor)->value('nama_lengkap');
        echo $data;
    }

    /**
     * Re-check active loans status: tutup pinjaman yang sudah lunas, perbaiki status nasabah.
     * Status nasabah hanya di-set "tidak ada pinjaman" jika benar-benar tidak ada pinjaman aktif tersisa.
     */
    public function recheck_active_loans()
    {
        $nasabahs = \DB::table('nasabahs')
            ->where('status_pinjaman', '1')
            ->get();

        $loansClosed = 0;
        foreach ($nasabahs as $nasabah) {
            $active_loans = \DB::table('pinjamans')
                ->where('no_rekening', $nasabah->no_rekening)
                ->where('status', '1')
                ->get();

            foreach ($active_loans as $loan) {
                $remaining = \DB::table('angsurans')
                    ->where('pinjaman_id', $loan->id)
                    ->where('status', '1')
                    ->count();

                if ($remaining === 0) {
                    \DB::table('pinjamans')->where('id', $loan->id)->update(['status' => '0']);
                    $loansClosed++;
                }
            }

            // Set nasabah status_pinjaman = 0 hanya jika tidak ada lagi pinjaman aktif
            $stillHasActiveLoan = \DB::table('pinjamans')
                ->where('no_rekening', $nasabah->no_rekening)
                ->where('status', '1')
                ->exists();

            if (!$stillHasActiveLoan) {
                \DB::table('nasabahs')->where('id', $nasabah->id)->update(['status_pinjaman' => '0']);
            }
        }

        if ($loansClosed > 0) {
            Session::flash('pesan', "Re-check selesai. {$loansClosed} pinjaman lunas telah ditutup.");
        } else {
            Session::flash('pesan', "Tidak ada pinjaman yang perlu ditutup.");
        }

        return redirect('pinjaman');
    }

    /**
     * Form relaksasi: ubah jumlah angsuran (mis. 5 bulan jadi 7), jumlah cicilan otomatis dihitung ulang.
     */
    public function relaksasi($id)
    {
        $pinjaman = Pinjaman::where('id', $id)->where('status', '1')->firstOrFail();
        $paid_count = \DB::table('angsurans')->where('pinjaman_id', $id)->where('status', '0')->count();
        $unpaid_count = \DB::table('angsurans')->where('pinjaman_id', $id)->where('status', '1')->count();
        return view('Pinjaman.relaksasi', [
            'pinjaman' => $pinjaman,
            'paid_count' => $paid_count,
            'unpaid_count' => $unpaid_count,
        ]);
    }

    /**
     * Proses relaksasi: update jumlah angsuran, hapus sisa angsuran belum bayar, buat ulang dengan cicilan baru.
     */
    public function relaksasi_update(Request $request)
    {
        $request->validate([
            'pinjaman_id' => 'required|exists:pinjamans,id',
            'new_angsuran' => 'required|integer|min:1',
        ]);

        $pinjaman_id = (int) $request->pinjaman_id;
        $new_angsuran = (int) $request->new_angsuran;
        $pinjaman = Pinjaman::where('id', $pinjaman_id)->where('status', '1')->firstOrFail();

        $total = (float) $pinjaman->total;
        $persen = (float) $pinjaman->persen;
        $skema = $pinjaman->skema ?? 'flat';
        $paid_count = \DB::table('angsurans')->where('pinjaman_id', $pinjaman_id)->where('status', '0')->count();

        if ($new_angsuran < $paid_count) {
            Session::flash('pesan', 'Jumlah angsuran baru tidak boleh lebih kecil dari angsuran yang sudah dibayar (' . $paid_count . ').');
            return redirect()->back();
        }

        $remaining_to_pay = $new_angsuran - $paid_count;
        if ($skema === 'flat') {
            $new_jumlah_cicilan = ((($persen / 100) * $total) + $total) / $new_angsuran;
        } else {
            $new_jumlah_cicilan = ((($persen / 100) * $total) + $total) / $new_angsuran;
        }

        \DB::table('pinjamans')->where('id', $pinjaman_id)->update(['angsuran' => $new_angsuran]);
        \DB::table('angsurans')->where('pinjaman_id', $pinjaman_id)->where('status', '1')->delete();

        for ($i = 0; $i < $remaining_to_pay; $i++) {
            Angsuran::create([
                'pinjaman_id' => $pinjaman_id,
                'jumlah_cicilan' => round($new_jumlah_cicilan),
                'status' => '1',
            ]);
        }

        Session::flash('pesan', "Relaksasi berhasil. Jumlah angsuran diubah menjadi {$new_angsuran} dengan cicilan Rp " . number_format(round($new_jumlah_cicilan), 0, ',', '.') . " per bulan.");
        return redirect("pinjaman/{$pinjaman_id}");
    }
}
