<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KunjunganUserController extends Controller
{
    public function index()
    {
        // Riwayat milik user yang login
        $riwayat = Kunjungan::where('user_id', Auth::id())
                    ->latest()
                    ->get();

        // Jadwal disetujui semua user 
        $jadwalDisetujui = Kunjungan::where('status', 'Disetujui')
                            ->whereDate('tgl_kunjungan', '>=', today())
                            ->orderBy('tgl_kunjungan')
                            ->get();

        return view('pages.user.kunjungan', compact('riwayat','jadwalDisetujui'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pengunjung'   => 'required|string|max:255',
            'no_hp'             => 'required|string|digits_between:10,12',
            // 'jenis_kunjungan'   => 'required|string',
            // 'nama_kunjungan'    => 'required|string',
            'tujuan'            => 'required|string',
            'instansi'          => 'required|nullable|string|max:255',
            'tgl_kunjungan'     => 'required|date|after_or_equal:today',
            'jam'               => 'required',
            
            // 'jumlah_pengunjung' => 'required|integer|min:1',
            'surat_pengajuan'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->tanggal == now()->toDateString() && $request->jam <= now()->format('H:i')) {
            return back()->withErrors([
                'jam' => 'Jam sudah lewat'
            ]);
        }

        if(in_array($request->tujuan, ['Penelitian','Kerjasama','Magang/PKL'])){
            $request->validate([
                'instansi' => 'required',
                'surat_pengajuan' => 'required'
            ]);
        }

        if ($request->hasFile('surat_pengajuan')) {
            $data['surat_pengajuan'] = $request->file('surat_pengajuan')->store('kunjungan', 'public');
        }

        $data['user_id'] = Auth::id();
        $data['status'] = 'Proses';
        Kunjungan::create($data);

        return redirect()->route('cek-status.index')
        ->with('success', 'Pengajuan kunjungan berhasil dikirim.');
    }
}
