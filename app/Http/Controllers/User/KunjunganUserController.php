<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\WargaBinaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KunjunganUserController extends Controller
{
    public function searchWbp(Request $request)
    {
        $search = $request->query('q');
        if (!$search) {
            return response()->json([]);
        }

        $wbp = WargaBinaan::where('status', 'Aktif')
            ->where('nama', 'like', "%{$search}%")
            ->select('id', 'nama', 'nik')
            ->limit(10)
            ->get();

        return response()->json($wbp);
    }

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
            'tujuan'            => 'required|string',
            'instansi'          => 'required|nullable|string|max:255',
            'tgl_kunjungan'     => 'required|date|after_or_equal:today',
            'jam'               => 'required',
            'surat_pengajuan'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'warga_binaan_id'   => 'required_if:mengunjungi_wbp,1|nullable|exists:warga_binaans,id',
        ]);

        if ($request->tgl_kunjungan == now()->toDateString()) {
            if ($this->getSessionStartTime($request->jam) <= now()->format('H:i')) {
                return back()->withErrors([
                    'jam' => 'Waktu sesi kunjungan sudah terlewat untuk hari ini.'
                ])->withInput();
            }
        }

        if ($this->isSessionBooked($request->tgl_kunjungan, $request->jam)) {
            return back()->withErrors([
                'jam' => 'Sesi kunjungan pada tanggal tersebut sudah terisi.'
            ])->withInput();
        }

        if (in_array($request->tujuan, ['Penelitian', 'Kerjasama', 'Magang/PKL'])) {
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
        $data['jam'] = $this->getSessionStartTime($request->jam);
        
        if (!$request->input('mengunjungi_wbp')) {
            $data['warga_binaan_id'] = null;
        }

        Kunjungan::create($data);

        return redirect()->route('cek-status.index')
            ->with('success', 'Pengajuan kunjungan berhasil dikirim.');
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

        foreach ($visits as $v) {
            if ($this->mapStringToSession($v->jam) === $selectedSession) {
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

        // Fallback mapping untuk format jam lama (misal: "10:00:00")
        $normalized = str_replace('.', ':', $jamStr);
        if (preg_match('/(\d{2}):(\d{2})/', $normalized, $matches)) {
            $hour = (int)$matches[1];
            $min = (int)$matches[2];
            $totalMinutes = $hour * 60 + $min;

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
}
