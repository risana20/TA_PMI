<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonasiUang;
use App\Models\Reimbursement;
use App\Models\DetailReimbursement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        // Pemasukan: Donasi Uang yang statusnya 'Selesai'
        $pemasukans = DonasiUang::whereHas('donasi', function($q) {
            $q->where('status', 'Selesai');
        })->with('donasi.user')->orderByDesc('id')->paginate(10, ['*'], 'pemasukan_page');

        // Pengeluaran: Reimbursement yang statusnya 'Disetujui'
        $pengeluarans = Reimbursement::where('status', 'Disetujui')
            ->with('user','detailReimbursements.itemLogistik.jenisLogistik')
            ->latest()
            ->paginate(10, ['*'], 'pengeluaran_page');

        $totalPemasukan = DonasiUang::whereHas('donasi', function($q) {
            $q->where('status', 'Selesai');
        })->sum('nominal');

        $totalPengeluaran = Reimbursement::where('status', 'Disetujui')->sum('total');
        
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Data grafik distribusi pengeluaran per kategori
        $distribusiPengeluaran = DetailReimbursement::selectRaw('item_logistik_id as kategori, SUM(nominal) as total')
            ->whereHas('reimbursement', function ($q) {$q->where('status', 'Disetujui');})
            ->groupBy('item_logistik_id')
            ->get();

        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');

        // BAR CHART
        $pengeluaranPerBulan = DetailReimbursement::selectRaw(
            'MONTH(pengeluarans.created_at) as bulan, SUM(detail_reimbursements.nominal) as total'
        )
        ->join(
            'pengeluarans',
            'detail_reimbursements.reimbursement_id',
            '=',
            'pengeluarans.id'
        )
        ->where('pengeluarans.status','Disetujui')
        ->whereYear('pengeluarans.tgl_validasi',$tahun)
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get();
        
        // dd($tahun, $bulan, $pengeluaranPerBulan);

        $pemasukanPerBulan = DonasiUang::selectRaw(
            'MONTH(donasis.created_at) as bulan, SUM(donasi_uangs.nominal) as total'
        )
        ->join(
            'donasis',
            'donasi_uangs.donasi_id',
            '=',
            'donasis.id'
        )
        ->where('donasis.status','Selesai')
        ->whereYear('donasis.created_at',$tahun)
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get();
        // dd($tahun, $bulan, $pemasukanPerBulan);

        // DONUT CHART


        $barangPerBulan = DetailReimbursement::selectRaw(
            'item_logistik_id, SUM(nominal) as total'
        )
        ->whereHas('reimbursement', function($q) use ($bulan,$tahun){

            $q->where('status','Disetujui')
            ->whereMonth('tgl_validasi',$bulan)
            ->whereYear('tgl_validasi',$tahun);

        })
        ->groupBy('item_logistik_id')
        ->get();

        return view('pages.admin.keuangan.index', compact(
            'pemasukans', 'pengeluarans', 'totalPemasukan', 'totalPengeluaran', 'saldo', 'distribusiPengeluaran', 'pengeluaranPerBulan','pemasukanPerBulan','barangPerBulan', 'tahun','bulan'
        ));
        
    }


    
}
