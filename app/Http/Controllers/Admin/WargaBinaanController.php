<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WargaBinaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\WargaBinaanExport;
use Maatwebsite\Excel\Facades\Excel;

class WargaBinaanController extends Controller
{
    public function index(Request $request)
    {
        // tab pencarian dan export
        if ($request->filled('export')) {
            $type = $request->get('export');
            if ($type === 'pdf') {
                return $this->exportPdf($request);
            }
            if ($type === 'excel') {
                return $this->exportExcel($request);
            }
        }

        $tab = $request->get('tab', 'ODGJ');
        $search = $request->get('search');
        $status = $request->get('status_filter');

        $query = WargaBinaan::where('kategori', $tab);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $wargaBinaans = $query->latest()->paginate(10)->withQueryString();

        return view('pages.admin.warga-binaan.index', compact('wargaBinaans', 'tab', 'search'));
    }

    // Export PDF (download)
    public function exportPdf(Request $request)
    {
        $tab = $request->get('tab', 'ODGJ');
        $wargaBinaans = WargaBinaan::where('kategori', $tab)->latest()->get();

        $pdf = Pdf::loadView('pages.admin.warga-binaan.export_pdf', compact('wargaBinaans', 'tab'));
        $fileName = 'warga-binaan-' . strtolower($tab) . '-' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    // Export Excel (download .xlsx)
    public function exportExcel(Request $request)
    {
        $tab = $request->get('tab', 'ODGJ');
        $fileName = 'warga-binaan-' . strtolower($tab) . '-' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new WargaBinaanExport($tab), $fileName);
    }
    
    //tambah warga
    public function store(Request $request)
    {
        $data = $request->validate([
            'nik'             => 'required|string|max:16|unique:warga_binaans',
            'nama'            => 'required|string|max:255',
            'tempat_lahir'    => 'required|string',
            'tgl_lahir'       => 'required|date',
            'alamat'          => 'required|string',
            'jenis_kelamin'   => 'required|in:L,P',
            'kategori'        => 'required|in:ODGJ,Lansia',
            'status'          => 'required|in:Aktif,Selesai Pembinaan,Meninggal,Kabur',
            'tgl_masuk'       => 'required|date',
            'no_bpjs'         => 'nullable|string|max:13',
            'catatan'         => 'nullable|string',
            'penanggung_jawab' => 'nullable|string',
            'foto'            => 'nullable|image|max:2048',
            'kontak_pj'       => 'nullable|string|max:12',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('warga-binaan', 'public');
        }

        WargaBinaan::create($data);
        return back()->with('success', 'Data warga binaan berhasil ditambahkan.');
    }
    //lihat data warga
    public function show(WargaBinaan $wargaBinaan)
    {
        $riwayatMonitoring = $wargaBinaan->monitoringKesehatans()->latest()->get();
        return view('pages.admin.warga-binaan.show', compact('wargaBinaan', 'riwayatMonitoring'));
    }
    // update data
    public function update(Request $request, WargaBinaan $wargaBinaan)
    {
        $data = $request->validate([
            'nik'             => 'required|string|max:16|regex:/^[0-9]+$/|unique:warga_binaans,nik,' . $wargaBinaan->id,
            'nama'            => 'required|string|max:255',
            'tempat_lahir'    => 'required|string',
            'tgl_lahir'       => 'required|date',
            'alamat'          => 'required|string',
            'jenis_kelamin'   => 'required|in:L,P',
            'kategori'        => 'required|in:ODGJ,Lansia',
            'status'          => 'required|in:Aktif,Selesai Pembinaan,Meninggal,Kabur',
            'tgl_masuk'       => 'required|date',
            'no_bpjs'         => 'nullable|string|max:13|regex:/^[0-9]+$/',
            'catatan'         => 'nullable|string',
            'penanggung_jawab' => 'nullable|string',
            'foto'            => 'nullable|image|max:2048',
            'kontak_pj'       => 'nullable|string|regex:/^[0-9]+$/|max:12',
        ]);

        if ($request->hasFile('foto')) {
            if ($wargaBinaan->foto) {
                Storage::disk('public')->delete($wargaBinaan->foto);
            }
            $data['foto'] = $request->file('foto')->store('warga-binaan', 'public');
        }

        $wargaBinaan->update($data);
        return back()->with('success', 'Data warga binaan berhasil diperbarui.');
    }
    // hapus warga
    public function destroy(WargaBinaan $wargaBinaan)
    {
        if ($wargaBinaan->foto) {
            Storage::disk('public')->delete($wargaBinaan->foto);
        }
        $wargaBinaan->delete();
        return back()->with('success', 'Data warga binaan berhasil dihapus.');
    }
}
