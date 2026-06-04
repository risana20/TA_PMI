<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonitoringKesehatan;
use App\Models\PemeriksaanRsj;
use App\Models\PengeluaranObat;
use App\Models\RiwayatPenyakit;
use App\Models\StokObat;
use App\Models\WargaBinaan;
use App\Models\SuratRujukanOdgj;
use App\Models\StokLogistik;
use App\Models\PengeluaranLogistik;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'ODGJ');
        $search = $request->get('search');

        $query = WargaBinaan::where('status', 'Aktif')->where('kategori', $tab);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $wargaBinaans = $query->paginate(10)->withQueryString();

        return view('pages.admin.monitoring.index', compact('wargaBinaans', 'search', 'tab'));
    }

    public function show(WargaBinaan $wargaBinaan)
    {
        $riwayat         = $wargaBinaan->monitoringKesehatans()->with('riwayatPenyakits')->latest()->paginate(10);
        $pemeriksaanRSJ  = $wargaBinaan->pemeriksaanRsjs()->with('suratRujukanOdgj')->latest()->get();
        $riwayatPenyakit = $wargaBinaan->penyakitPernahAktifTerkini()->get();
        $suratRujukans   = $wargaBinaan->suratRujukanOdgjs()->latest()->get();
        $stokObats       = $wargaBinaan->stokObats()->latest()->get();
        $logistikObats   = StokLogistik::whereHas('itemLogistik.jenisLogistik', function ($query) {
            $query->where('nama_jenis_logistik', 'Obat');
        })->with('itemLogistik')->get();

        return view('pages.admin.monitoring.show', compact(
            'wargaBinaan',
            'riwayat',
            'pemeriksaanRSJ',
            'riwayatPenyakit',
            'suratRujukans',
            'stokObats',
            'logistikObats'
        ));
    }

    public function storeRiwayatPenyakit(Request $request, WargaBinaan $wargaBinaan)
    {
        $data = $request->validate([
            'nama_penyakit'   => 'required|string|max:255',
            'status_penyakit' => 'required|in:Aktif,Sembuh',
        ]);

        RiwayatPenyakit::create([
            'warga_binaan_id' => $wargaBinaan->id,
            'nama_penyakit'   => $data['nama_penyakit'],
            'status'          => $data['status_penyakit'],
            'tanggal'         => now(),
        ]);

        return back()->with('success', 'Riwayat penyakit berhasil ditambahkan.');
    }

    public function updateStatusRiwayatPenyakit(Request $request, WargaBinaan $wargaBinaan, RiwayatPenyakit $riwayatPenyakit)
    {
        $data = $request->validate([
            'status_penyakit' => 'required|in:Aktif,Sembuh',
        ]);

        RiwayatPenyakit::create([
            'warga_binaan_id' => $riwayatPenyakit->warga_binaan_id,
            'nama_penyakit'   => $riwayatPenyakit->nama_penyakit,
            'status'          => $data['status_penyakit'],
            'tanggal'         => now(),
        ]);

        return back()->with('success', 'Status riwayat penyakit berhasil diperbarui.');
    }

    public function storeRSJ(Request $request, WargaBinaan $wargaBinaan)
    {
        $data = $request->validate([
            'tgl_kontrol'        => 'required|date',
            'kondisi'            => 'nullable|string',
            'gejala'             => 'nullable|string',
            'obat'               => 'nullable|string',
            'catatan'            => 'nullable|string',
            'kontrol_berikutnya' => 'nullable|date',
            'surat_rujukan_odgj_id' => 'nullable|exists:surat_rujukan_odgjs,id',
        ]);

        $data['warga_binaan_id'] = $wargaBinaan->id;

        // Auto-match referral letter if empty
        if (empty($data['surat_rujukan_odgj_id'])) {
            $matchedRujukan = SuratRujukanOdgj::where('warga_binaan_id', $wargaBinaan->id)
                ->where('tanggal_terbit', '<=', $data['tgl_kontrol'])
                ->where('tanggal_berakhir', '>=', $data['tgl_kontrol'])
                ->first();
            if ($matchedRujukan) {
                $data['surat_rujukan_odgj_id'] = $matchedRujukan->id;
            }
        }

        PemeriksaanRsj::create($data);

        return back()->with('success', 'Pemeriksaan RSJ berhasil dicatat.');
    }

    public function storePemeriksaan(Request $request, WargaBinaan $wargaBinaan)
    {
        $data = $request->validate([
            'tanggal'       => 'required|date',
            'frek_napas'    => 'nullable|string',
            'tekanan_darah' => 'nullable|string',
            'suhu_tubuh'    => 'nullable|string',
            'nadi'          => 'nullable|string',
            'spo2'          => 'nullable|string',
            'berat_badan'   => 'nullable|numeric',
            'tinggi_badan'  => 'nullable|numeric',
            'keluhan'       => 'nullable|string',
            'tindakan'      => 'nullable|string',
            'catatan'       => 'nullable|string',
            'petugas'       => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
        ]);

        $data['warga_binaan_id'] = $wargaBinaan->id;

        // 1. Buat record monitoring terlebih dahulu untuk mendapatkan ID
        $monitoring = MonitoringKesehatan::create($data);

        // 2. Update status penyakit yang sudah ada (dengan APPEND INSERT LOG BARU)
        if ($request->has('riwayat_existing')) {
            foreach ($request->riwayat_existing as $id => $item) {
                $rp = RiwayatPenyakit::find($id);
                if ($rp && $rp->warga_binaan_id == $wargaBinaan->id && isset($item['status'])) {
                    if ($rp->status !== $item['status']) {
                        RiwayatPenyakit::create([
                            'warga_binaan_id' => $wargaBinaan->id,
                            'monitoring_kesehatan_id' => $monitoring->id,
                            'nama_penyakit' => $item['nama_penyakit'],
                            'status' => $item['status'],
                            'tanggal' => now(),
                        ]);
                    }
                }
            }
        }

        // 3. Tambahkan penyakit baru
        if ($request->has('riwayat_baru')) {
            foreach ($request->riwayat_baru as $item) {
                if (!empty($item['nama_penyakit']) && !empty($item['status'])) {
                    RiwayatPenyakit::create([
                        'warga_binaan_id' => $wargaBinaan->id,
                        'monitoring_kesehatan_id' => $monitoring->id,
                        'nama_penyakit' => $item['nama_penyakit'],
                        'status' => $item['status'],
                        'tanggal' => now(),
                    ]);
                }
            }
        }

        // 4. Ambil status riwayat penyakit terkini dari database setelah semua proses di atas (SNAPSHOT)
        $riwayatPenyakitSaatIni = $wargaBinaan->penyakitPernahAktifTerkini()->get(['nama_penyakit', 'status']);
        $monitoring->update([
            'riwayat_penyakit' => $riwayatPenyakitSaatIni->isNotEmpty() ? $riwayatPenyakitSaatIni->toJson() : null
        ]);

        return back()->with('success', 'Pemeriksaan beserta pembaruan riwayat penyakit berhasil dicatat.');
    }

    public function storeRujukan(Request $request, WargaBinaan $wargaBinaan)
    {
        $data = $request->validate([
            'tanggal_terbit'  => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_terbit',
            // allow pdf as well as common image formats
            'file_surat'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('file_surat')) {
            // store on public disk so the file is accessible via /storage
            $data['file_surat'] = $request->file('file_surat')
                ->store('surat_rujukan_odgjs', 'public');
        }

        $data['warga_binaan_id'] = $wargaBinaan->id;
        SuratRujukanOdgj::create($data);

        return back()->with('success', 'Surat rujukan berhasil ditambahkan.');
    }

    public function storeObat(Request $request, WargaBinaan $wargaBinaan)
    {
        $data = $request->validate([
            'logistik_id'  => 'nullable|exists:stok_logistiks,id',
            'nama_obat'    => 'required|string|max:255',
            'aturan_minum' => 'required|string',
            'bentuk_obat'  => 'required|string|max:50',
            'jumlah_awal'  => 'required|integer|min:1',
            'tgl_mulai'    => 'required|date',
            'asal_obat'    => 'required|in:OBAT_PERIKSA,OBAT_GRIYA',
            'keterangan'   => 'nullable|string',
        ]);

        if ($data['asal_obat'] === 'OBAT_GRIYA' && isset($data['logistik_id'])) {
            $logistik = StokLogistik::with('itemLogistik')->find($data['logistik_id']);
            if ($logistik) {
                // Kurangi stok logistik
                $logistik->decrement('jumlah_saat_ini', $data['jumlah_awal']);

                // Catat riwayat pengeluaran di PengeluaranLogistik
                PengeluaranLogistik::create([
                    'stok_logistik_id' => $logistik->id,
                    'user_id' => auth()->id() ?? 1,
                    'warga_binaan_id' => $wargaBinaan->id,
                    'jumlah' => $data['jumlah_awal'],
                    'tanggal' => $data['tgl_mulai'],
                    'keterangan' => 'Pengeluaran untuk OBAT GRIYA',
                ]);

                // Pastikan nama dan bentuk obat selaras dengan data logistik
                $data['nama_obat'] = $logistik->itemLogistik->nama_item;
                $data['bentuk_obat'] = $logistik->itemLogistik->satuan ?? $data['bentuk_obat'];
            }
        }

        unset($data['logistik_id']);

        $data['warga_binaan_id'] = $wargaBinaan->id;
        $data['sisa'] = $data['jumlah_awal'];
        $data['satuan'] = $data['bentuk_obat'];
        $data['tgl_update'] = now()->toDateString();
        $data['status'] = 'AKTIF';

        StokObat::create($data);
        return back()->with('success', 'Obat berhasil ditambahkan.');
    }

    public function showObat(WargaBinaan $wargaBinaan, StokObat $stokObat)
    {
        $pengeluarans = $stokObat->pengeluaranObats()->latest()->get();

        return view('pages.admin.monitoring.detail-obat', compact(
            'wargaBinaan',
            'stokObat',
            'pengeluarans'
        ));
    }

    public function updateStatusObat(Request $request, WargaBinaan $wargaBinaan, StokObat $stokObat)
    {
        $data = $request->validate([
            'status' => 'required|in:AKTIF,HABIS,SEMBUH',
        ]);

        $stokObat->update($data);

        return back()->with('success', 'Status obat berhasil diperbarui.');
    }

    public function storePengeluaranObat(Request $request, WargaBinaan $wargaBinaan, StokObat $stokObat)
    {
        $data = $request->validate([
            'jumlah'  => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        $data['stok_obat_id'] = $stokObat->id;

        PengeluaranObat::create($data);

        $newSisa = $stokObat->sisa - $data['jumlah'];
        if ($newSisa < 0) $newSisa = 0;

        $stokObat->update([
            'sisa' => $newSisa,
            'tgl_update' => $data['tanggal'],
            'status' => $newSisa == 0 ? 'HABIS' : 'AKTIF',
        ]);

        return back()->with('success', 'Pengeluaran obat berhasil dicatat.');
    }

    public function updateSisaObat(Request $request, WargaBinaan $wargaBinaan, StokObat $stokObat)
    {
        $data = $request->validate([
            'sisa'        => 'required|integer|min:0',
            'tgl_update'  => 'required|date',
        ]);

        $data['status'] = $data['sisa'] == 0 ? 'HABIS' : 'AKTIF';
        $stokObat->update($data);

        return back()->with('success', 'Sisa obat berhasil diperbarui.');
    }

    public function updateObat(Request $request, StokObat $stokObat)
    {
        $request->validate(['stok' => 'required|integer|min:0']);
        $stokObat->update(['stok' => $request->stok]);
        return back()->with('success', 'Stok obat berhasil diperbarui.');
    }
}
