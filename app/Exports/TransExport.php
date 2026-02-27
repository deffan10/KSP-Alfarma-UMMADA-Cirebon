<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $rows = collect();

        $transaksi = DB::table('transaksis')
            ->join('users', 'users.id', '=', 'transaksis.user_id')
            ->join('nasabahs', 'nasabahs.id', '=', 'transaksis.nasabah_id')
            ->select('transaksis.created_at', 'transaksis.total', 'nasabahs.no_rekening', 'nasabahs.nama_lengkap', 'users.name')
            ->orderBy('transaksis.created_at')
            ->get();

        $penyesuaian = DB::table('general_ledgers')
            ->join('users', 'users.id', '=', 'general_ledgers.user_id')
            ->whereIn('general_ledgers.jenis_transaksi', ['penyesuaian', 'penyesuaian_masuk'])
            ->where('general_ledgers.status_pembukuan', '1')
            ->select('general_ledgers.created_at', 'general_ledgers.total', 'general_ledgers.keterangan', 'general_ledgers.jenis_transaksi', 'users.name')
            ->orderBy('general_ledgers.created_at')
            ->get();

        $merged = $transaksi->map(function ($t) {
            return [
                'created_at' => $t->created_at,
                'no_rekening' => $t->no_rekening,
                'uraian' => $t->nama_lengkap,
                'total' => $t->total,
                'operator' => $t->name,
                'jenis' => 'Transaksi',
            ];
        })->concat($penyesuaian->map(function ($p) {
            $subjenis = $p->jenis_transaksi === 'penyesuaian_masuk' ? ' (Penambahan)' : ' (Pengurangan)';
            return [
                'created_at' => $p->created_at,
                'no_rekening' => '-',
                'uraian' => ($p->keterangan ?? 'Penyesuaian kas') . $subjenis,
                'total' => $p->total,
                'operator' => $p->name,
                'jenis' => 'Penyesuaian Kas',
            ];
        }))->sortBy('created_at')->values();

        $no = 1;
        return $merged->map(function ($r) use (&$no) {
            return [
                $no++,
                $r['no_rekening'],
                $r['uraian'],
                $r['created_at'] ? \Carbon\Carbon::parse($r['created_at'])->format('Y-m-d H:i') : '',
                $r['total'],
                $r['operator'],
                $r['jenis'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'No Rekening',
            'Nama / Keterangan',
            'Tanggal',
            'Jumlah',
            'Operator',
            'Jenis',
        ];
    }
}
