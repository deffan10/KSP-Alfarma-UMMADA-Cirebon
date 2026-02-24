<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use setasign\FPDF;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransExport;
use App\Models\Nasabah;
use App\Models\Profile;

require_once base_path('vendor/setasign/fpdf/fpdf.php');


class LaporanController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('check');
        
    }

    public function lapPdf()
    {
        $fpdf = new pdf('P', 'mm', 'A4');
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetFont('Times', 'B', 10);
        $fpdf->SetLeftMargin(15);
        $fpdf->SetRightMargin(15);

        $transaksi = \DB::table('transaksis')
            ->join('users', 'users.id', '=', 'transaksis.user_id')
            ->join('nasabahs', 'nasabahs.id', '=', 'transaksis.nasabah_id')
            ->select('transaksis.created_at', 'transaksis.total', 'nasabahs.no_rekening', 'nasabahs.nama_lengkap', 'users.name')
            ->orderBy('transaksis.created_at')
            ->get();

        $penyesuaian = \DB::table('general_ledgers')
            ->join('users', 'users.id', '=', 'general_ledgers.user_id')
            ->where('general_ledgers.jenis_transaksi', 'penyesuaian')
            ->where('general_ledgers.status_pembukuan', '1')
            ->select('general_ledgers.created_at', 'general_ledgers.total', 'general_ledgers.keterangan', 'users.name')
            ->orderBy('general_ledgers.created_at')
            ->get();

        $merged = $transaksi->map(function ($t) {
            return (object)['created_at' => $t->created_at, 'no_rekening' => $t->no_rekening, 'uraian' => $t->nama_lengkap, 'total' => $t->total, 'name' => $t->name, 'jenis' => 'Transaksi'];
        })->concat($penyesuaian->map(function ($p) {
            return (object)['created_at' => $p->created_at, 'no_rekening' => '-', 'uraian' => $p->keterangan ?? 'Penyesuaian kas', 'total' => $p->total, 'name' => $p->name, 'jenis' => 'Penyesuaian Kas'];
        }))->sortBy('created_at')->values();

        $fpdf->Cell(8, 7, 'No', 1, 0, 'C');
        $fpdf->Cell(22, 7, 'Tanggal', 1, 0, 'C');
        $fpdf->Cell(25, 7, 'No. Rekening', 1, 0, 'C');
        $fpdf->Cell(50, 7, 'Nama / Keterangan', 1, 0, 'C');
        $fpdf->Cell(28, 7, 'Jumlah', 1, 0, 'C');
        $fpdf->Cell(25, 7, 'Jenis', 1, 0, 'C');
        $fpdf->Cell(22, 7, 'Operator', 1, 1, 'C');

        $no = 1;
        foreach ($merged as $r) {
            $fpdf->SetFont('Times', '', 9);
            $fpdf->Cell(8, 6, $no, 1, 0, 'C');
            $fpdf->Cell(22, 6, tgl_id($r->created_at), 1, 0, 'C');
            $fpdf->Cell(25, 6, $r->no_rekening, 1, 0, 'L');
            $fpdf->Cell(50, 6, \Illuminate\Support\Str::limit($r->uraian, 28), 1, 0, 'L');
            $fpdf->Cell(28, 6, number_format($r->total), 1, 0, 'R');
            $fpdf->Cell(25, 6, $r->jenis, 1, 0, 'C');
            $fpdf->Cell(22, 6, $r->name, 1, 1, 'L');
            $no++;
        }

        $fpdf->Output();
        exit;
    }

    public function transNas(Request $request)
    {
        $nasabah = Nasabah::find($request->nasabah_id);
        $fpdf = New pdf('P','mm','A4');
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetFont('Times','B',12);
        $fpdf->SetLeftMargin(25);
        $fpdf->Cell(10,8,'Nama: '.$nasabah->nama_lengkap,0,1,'L');
        $fpdf->Cell(10,8,'No. Rekening: '.$nasabah->no_rekening,0,1,'L');
        $fpdf->Cell(10,8,'',0,1,'C');
        $fpdf->Cell(10,8,'No',1,0,'C');
        $fpdf->Cell(30,8,'Tanggal',1,0,'C');
        $fpdf->Cell(40,8,'Kode Operator',1,0,'C');
        $fpdf->Cell(40,8,'Jenis Transaksi',1,0,'C');
        $fpdf->Cell(40,8,'Jumlah',1,1,'C');
        $transaksi = \DB::table('transaksis')
                    ->where('transaksis.nasabah_id','=',$request->nasabah_id)
                    ->select('transaksis.*')
                    ->orderBy('created_at','asc')
                    ->limit(25)
                    ->get();
        $no=1;
        foreach ($transaksi as $t)
        {
            $fpdf->SetFont('Times','',12);
            $fpdf->SetLeftMargin(25);
            $fpdf->Cell(10,8,$no,1,0,'C');
            $fpdf->Cell(30,8,tgl_id($t->created_at),1,0,'C');
            $fpdf->Cell(40,8,$t->user_id,1,0,'C');
            $fpdf->Cell(40,8,$t->jenis_transaksi,1,0,'C');
            $fpdf->Cell(40,8,number_format($t->total),1,1,'C');
            $no++;
        }
        $fpdf->Cell(120,8,'Total Saldo: ',1,0,'L');
        $fpdf->Cell(40,8,number_format($nasabah->saldo_akhir),1,1,'C');
        $fpdf->Output();
        die;
    }

    public function pinjNas(Request $request)
    {
        $pinjaman = \DB::table('angsurans')
                    ->join('pinjamans','pinjamans.id','=','angsurans.pinjaman_id')
                    ->where('angsurans.pinjaman_id','=',$request->pinjaman_id)
                    ->where('angsurans.status','=','1')
                    ->select('angsurans.id','angsurans.pinjaman_id','angsurans.jumlah_cicilan','pinjamans.nama_lengkap','pinjamans.no_rekening','angsurans.created_at','pinjamans.skema')
                    ->get();
        $fpdf = New pdf('P','mm','A4');
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetFont('Times','B',12);
        $fpdf->SetLeftMargin(25);
        $fpdf->Cell(10,8,'Nama: '.$pinjaman[0]->nama_lengkap,0,1,'L');
        $fpdf->Cell(10,8,'No. Rekening: '.$pinjaman[0]->no_rekening,0,1,'L');
        $fpdf->Cell(10,8,'',0,1,'C');
        $fpdf->Cell(10,8,'No',1,0,'C');
        $fpdf->Cell(30,8,'Tanggal',1,0,'C');
        $fpdf->Cell(40,8,'Skema',1,0,'C');
        $fpdf->Cell(80,8,'Jumlah Cicilan',1,1,'C');
        //$fpdf->Cell(40,8,'Sisa Pinjaman',1,1,'C');
        $no=1;
        foreach ($pinjaman as $p)
        {
            $fpdf->SetFont('Times','',12);
            $fpdf->SetLeftMargin(25);
            $fpdf->Cell(10,8,$no,1,0,'C');
            $fpdf->Cell(30,8,tgl_id(tglAdd($p->created_at,$no)),1,0,'C');
            $fpdf->Cell(40,8,$p->skema,1,0,'C');
            $fpdf->Cell(80,8,number_format($p->jumlah_cicilan),1,1,'C');
            $no++;
        }
        $tot_pinjam = \DB::table('angsurans')
                    ->where('pinjaman_id','=',$request->pinjaman_id)
                    ->where('angsurans.status','=','1')
                    ->sum('jumlah_cicilan');
        $fpdf->Cell(80,8,'Total Pinjaman: ',1,0,'L');
        $fpdf->Cell(80,8,number_format($tot_pinjam),1,1,'C');
        $fpdf->Output();
        die;
    }



    public function lapXls()
    {
        return Excel::download(new TransExport, 'data transaksi.xlsx');
               
        
    }


}

class pdf extends \FPDF
{
    /**
     * FPDF tidak mendukung PNG interlacing. Konversi ke non-interlaced via GD.
     */
    protected function imagePathForFpdf($path)
    {
        if (!file_exists($path) || !function_exists('imagecreatefrompng')) {
            return $path;
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'png') {
            $im = @imagecreatefrompng($path);
            if ($im === false) {
                return $path;
            }
            imagealphablending($im, false);
            imagesavealpha($im, true);
            imageinterlace($im, 0);
            $tmp = tempnam(sys_get_temp_dir(), 'fpdf_logo_') . '.png';
            imagepng($im, $tmp);
            imagedestroy($im);
            return file_exists($tmp) ? $tmp : $path;
        }
        if ($ext === 'jpg' || $ext === 'jpeg') {
            $im = @imagecreatefromjpeg($path);
            if ($im === false) {
                return $path;
            }
            $tmp = tempnam(sys_get_temp_dir(), 'fpdf_logo_') . '.jpg';
            imagejpeg($im, $tmp);
            imagedestroy($im);
            return file_exists($tmp) ? $tmp : $path;
        }
        return $path;
    }

    public function Header()
    {
        $profile = Profile::where('status', 'active')->first();
        $nama = $profile ? $profile->nama_koperasi : 'KOPERASI SIMPAN PINJAM';
        $alamat = $profile && $profile->alamat ? $profile->alamat : '';
        if ($profile && $profile->kota) {
            $alamat .= ($alamat ? ', ' : '') . $profile->kota;
        }
        if ($profile && $profile->provinsi) {
            $alamat .= ($alamat ? ', ' : '') . $profile->provinsi;
        }
        $telp = $profile && $profile->telp ? 'Telp: ' . $profile->telp : '';

        $logoPath = null;
        if ($profile && !empty($profile->file_logo) && \File::exists(public_path('storage/foto/' . $profile->file_logo))) {
            $logoPath = public_path('storage/foto/' . $profile->file_logo);
        } elseif (\File::exists(public_path('foto/member.png'))) {
            $logoPath = public_path('foto/member.png');
        }
        if ($logoPath) {
            $imagePathForFpdf = $this->imagePathForFpdf($logoPath);
            $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $isConverted = ($imagePathForFpdf !== $logoPath);
            if ($imagePathForFpdf && file_exists($imagePathForFpdf) && ($isConverted || $ext !== 'png')) {
                $this->Image($imagePathForFpdf, 10, 6, 24);
                if ($isConverted) {
                    @unlink($imagePathForFpdf);
                }
            }
        }

        $this->SetFont('Times', 'B', 14);
        $this->Cell(0, 6, 'KOPERASI SIMPAN PINJAM', 0, 1, 'C');
        $this->SetFont('Times', 'B', 12);
        $this->Cell(0, 6, $nama, 0, 1, 'C');
        if ($alamat !== '') {
            $this->SetFont('Times', '', 10);
            $this->Cell(0, 5, $alamat, 0, 1, 'C');
        }
        if ($telp !== '') {
            $this->Cell(0, 5, $telp, 0, 1, 'C');
        }
        $this->Ln(4);
        $this->SetDrawColor(0, 0, 0);
        $this->Cell(0, 0, '', 'B', 1);
        $this->Ln(6);
    }

    // Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Times','I',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
} 
    
}