<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use Illuminate\Http\Request;

class KunjunganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        $query = Kunjungan::with('wargaBinaan');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengunjung', 'like', "%$search%")
                ->orWhere('no_hp', 'like', "%$search%")
                ->orWhere('instansi', 'like', "%$search%")
                ->orWhere('tujuan', 'like', "%$search%")
                ->orWhere('tgl_kunjungan', 'like', "%$search%")
                ->orWhere('jam', 'like', "%$search%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $kunjungans = $query->orderBy('tgl_kunjungan', 'asc')->paginate(10)->withQueryString();

        return view('pages.admin.kunjungan.index', compact('kunjungans', 'search', 'status'));
    }

    public function approve(Kunjungan $kunjungan)
    {
        $kunjungan->update(['status' => 'DISETUJUI']);
        return back()->with('success', 'Kunjungan berhasil disetujui.');
    }

    public function reject(Request $request, Kunjungan $kunjungan)
    {
        $request->validate(['alasan_tolak' => 'required|string']);
        $kunjungan->update([
            'status'      => 'DITOLAK',
            'alasan_tolak' => $request->alasan_tolak,
        ]);
        return back()->with('success', 'Kunjungan ditolak.');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nama_pengunjung' => 'required|string|max:255',
            'no_hp'           => 'required|string|digits_between:10,12',
            'tujuan'          => 'required|string',
            'instansi'        => 'nullable|string|max:255',
            'tgl_kunjungan'   => 'required|date',
            'jam'             => 'required',
            'surat_pengajuan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'warga_binaan_id' => 'required_if:mengunjungi_wbp,1|nullable|exists:warga_binaans,id',
        ]);

        $path = null;
        if ($request->hasFile('surat_pengajuan')) {
            $path = $request->file('surat_pengajuan')->store('surat', 'public');
        }

        Kunjungan::create([
            'nama_pengunjung' => $request->nama_pengunjung,
            'no_hp'           => $request->no_hp,
            'tujuan'          => $request->tujuan,
            'instansi'        => $request->instansi,
            'tgl_kunjungan'   => $request->tgl_kunjungan,
            'jam'             => $request->jam,
            'surat_pengajuan' => $path,
            'status'          => 'DISETUJUI',
            'warga_binaan_id' => $request->mengunjungi_wbp ? $request->warga_binaan_id : null,
        ]);

        return back()->with('success', 'Kunjungan berhasil ditambahkan');
    }

    
    
}
