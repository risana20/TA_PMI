<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KunjunganExport;



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
        $jadwalDisetujui = Kunjungan::where('status', 'DISETUJUI')
            ->select('tgl_kunjungan', 'jam')
            ->get();

        return view('pages.admin.kunjungan.index', compact('kunjungans', 'search', 'status', 'jadwalDisetujui'));
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
            'tgl_kunjungan'   => 'required|date|after_or_equal:today',
            'jam'             => 'required',
            'surat_pengajuan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'warga_binaan_id' => 'required_if:mengunjungi_wbp,1|nullable|exists:warga_binaans,id',
        ]);

        // Cek jika memilih hari ini, apakah sesi sudah lewat
        if ($request->tgl_kunjungan == now()->toDateString()) {

            if ($this->getSessionStartTime($request->jam) <= now()->format('H:i')) {

                return back()->withErrors([
                    'jam' => 'Waktu sesi kunjungan sudah terlewat untuk hari ini.'
                ])->withInput();

            }
        }

        // Cek apakah sesi sudah dipakai
        if ($this->isSessionBooked($request->tgl_kunjungan, $request->jam)) {

            return back()->withErrors([
                'jam' => 'Sesi kunjungan pada tanggal tersebut sudah terisi.'
            ])->withInput();

        }

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
            'jam'             => $this->getSessionStartTime($request->jam),
            'surat_pengajuan' => $path,
            'status'          => 'DISETUJUI',
            'warga_binaan_id' => $request->mengunjungi_wbp
                                    ? $request->warga_binaan_id
                                    : null,
        ]);

        return back()->with('success', 'Kunjungan berhasil ditambahkan');
    }
    private function isSessionBooked($date, $selectedJam)
    {
        $visits = Kunjungan::whereDate('tgl_kunjungan', $date)
            ->where('status', 'DISETUJUI')
            ->get();

        $selectedSession = $this->mapStringToSession($selectedJam);

        if (!$selectedSession) {
            return false;
        }

        foreach ($visits as $visit) {

            if ($this->mapStringToSession($visit->jam) === $selectedSession) {
                return true;
            }

        }

        return false;
    }
    private function mapStringToSession($jamStr)
    {
        if (!$jamStr) return null;

        $jamStr = trim($jamStr);

        if (stripos($jamStr, 'Sesi 1') !== false) return 'Sesi 1';
        if (stripos($jamStr, 'Sesi 2') !== false) return 'Sesi 2';
        if (stripos($jamStr, 'Sesi 3') !== false) return 'Sesi 3';
        if (stripos($jamStr, 'Sesi 4') !== false) return 'Sesi 4';
        if (stripos($jamStr, 'Sesi 5') !== false) return 'Sesi 5';

        $normalized = str_replace('.', ':', $jamStr);

        if (preg_match('/(\d{2}):(\d{2})/', $normalized, $matches)) {

            $hour = (int) $matches[1];
            $min = (int) $matches[2];

            $totalMinutes = ($hour * 60) + $min;

            if ($totalMinutes >= 480 && $totalMinutes < 570) return 'Sesi 1';
            if ($totalMinutes >= 570 && $totalMinutes < 660) return 'Sesi 2';
            if ($totalMinutes >= 660 && $totalMinutes < 780) return 'Sesi 3';
            if ($totalMinutes >= 780 && $totalMinutes < 870) return 'Sesi 4';
            if ($totalMinutes >= 870 && $totalMinutes <= 990) return 'Sesi 5';
        }

        return null;
    }
    private function getSessionStartTime($jamStr)
    {
        if (!$jamStr) return '00:00';

        $jamStr = trim($jamStr);

        if (stripos($jamStr, 'Sesi 1') !== false) return '08:00';
        if (stripos($jamStr, 'Sesi 2') !== false) return '09:30';
        if (stripos($jamStr, 'Sesi 3') !== false) return '11:00';
        if (stripos($jamStr, 'Sesi 4') !== false) return '13:00';
        if (stripos($jamStr, 'Sesi 5') !== false) return '14:30';

        $normalized = str_replace('.', ':', $jamStr);

        if (preg_match('/(\d{2}):(\d{2})/', $normalized, $matches)) {
            return $matches[1] . ':' . $matches[2];
        }

        return '00:00';
    }
   public function exportPdf(Request $request)
    {
        $query = Kunjungan::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pengunjung', 'like', '%' . $request->search . '%')
                ->orWhere('instansi', 'like', '%' . $request->search . '%')
                ->orWhere('tujuan', 'like', '%' . $request->search . '%');
            });
        }

        $kunjungans = $query->orderBy('tgl_kunjungan')->get();

        $pdf = Pdf::loadView('pages.admin.kunjungan.export_pdf', compact('kunjungans'));

        $user = auth()->user();
        $role = $user && $user->role ? $user->role->name : 'user';
        $nama = str_replace(' ', '_', $user->name);

        $fileName = 'Laporan_Data_Kunjungan_' .
                    date('Ymd') . '_' .
                    $role . '_' .
                    $nama . '.pdf';

        return $pdf->download($fileName);
    }

    // Export Excel (download .xlsx)

    public function exportExcel(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $user = auth()->user();
        $role = $user && $user->role ? $user->role->name : 'user';
        $nama = str_replace(' ', '_', $user->name);

        $fileName = 'Laporan_Data_Kunjungan_' .
                    date('Ymd') . '_' .
                    $role . '_' .
                    $nama . '.xlsx';

        return Excel::download(
            new KunjunganExport($status, $search),
            $fileName
        );
    }

}
