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
            ->with('user','detailReimbursements.jenisLogistik')
            ->latest()
            ->paginate(10, ['*'], 'pengeluaran_page');

        $totalPemasukan = DonasiUang::whereHas('donasi', function($q) {
            $q->where('status', 'Selesai');
        })->sum('nominal');

        $totalPengeluaran = Reimbursement::where('status', 'Disetujui')->sum('total');
        
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Data grafik distribusi pengeluaran per kategori
        $distribusiPengeluaran = DetailReimbursement::selectRaw('jenis_logistik_id as kategori, SUM(nominal) as total')
            ->whereHas('reimbursement', function ($q) {
                $q->where('status', 'disetujui');
            })
            ->groupBy('jenis_logistik_id')
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
        //$bulanDipilih = $bulan;


        $barangPerBulan = DetailReimbursement::selectRaw(
            'nama_kebutuhan, SUM(nominal) as total'
        )
        ->whereHas('reimbursement', function($q) use ($bulan,$tahun){

            $q->where('status','Disetujui')
            ->whereMonth('tgl_validasi',$bulan)
            ->whereYear('tgl_validasi',$tahun);

        })
        ->groupBy('nama_kebutuhan')
        ->get();

        return view('pages.admin.keuangan.index', compact(
            'pemasukans', 'pengeluarans', 'totalPemasukan', 'totalPengeluaran', 'saldo', 'distribusiPengeluaran', 'pengeluaranPerBulan','pemasukanPerBulan','barangPerBulan', 'tahun','bulan'
        ));
        
    }

    // Pemasukan only comes from Donasi forms now, so manual storePemasukan is deprecated.
    // KeuanganController only views finances or handles reimbursement approvals.
    
    // Store pengeluaran manually (can be mapped to creating a pre-approved Reimbursement if needed)
    // public function storePengeluaran(Request $request)
    // {
    //     $data = $request->validate([
    //         'tanggal'    => 'required|date',
    //         'nominal'    => 'required|integer|min:1',
    //         'jenis_logistik_id' => 'required|exists:jenis_logistiks,id',
    //         'keterangan' => 'nullable|string',
    //         'bukti_nota' => 'nullable|image|max:2048',
    //     ]);

    //     $totalPemasukan = DonasiUang::whereHas('donasi', function($q) {
    //         $q->where('status', 'Selesai');
    //     })->sum('nominal');

    //     $totalPengeluaran = Reimbursement::where('status', 'disetujui')->sum('total');

    //     $saldo = $totalPemasukan - $totalPengeluaran;

    //     if ($data['nominal'] > $saldo) {
    //         return back()->withErrors(['nominal' => 'Saldo tidak cukup'])->withInput();
    //     }

    //     if ($request->hasFile('bukti_nota')) {
    //         $data['bukti_nota'] = $request->file('bukti_nota')->store('keuangan', 'public');
    //     }

    //     // 1. buat reimbursement header
    //     $reimbursement = Reimbursement::create([
    //         'user_id' => Auth::id(),
    //         'status' => 'disetujui',
    //         'tgl_pengajuan' => $data['tanggal'],
    //         'tgl_validasi' => now(),
    //         'validated_by' => Auth::id(),
    //         'bukti_nota' => $data['bukti_nota'] ?? null,
    //         'total' => $data['nominal'],
    //         'keterangan' => $data['keterangan'] ?? null,
    //     ]);

    //     // 2. buat detail
    //     DetailReimbursement::create([
    //         'reimbursement_id' => $reimbursement->id,
    //         'nama_kebutuhan' => $data['keterangan'] ?? 'Pengeluaran manual',
    //         'nominal' => $data['nominal'],
    //         'jenis_logistik_id' => $data['jenis_logistik_id'],
    //     ]);

    //     return back()->with('success', 'Pengeluaran berhasil dicatat.');
    // }
}
