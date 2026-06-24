<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StokLogistik;
use App\Models\ItemLogistik;
use App\Models\JenisLogistik;
use App\Models\PemasukanLogistik;
use App\Models\PengeluaranLogistik;
use App\Models\StokObat;
use App\Models\WargaBinaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LogistikExport;
use Maatwebsite\Excel\Facades\Excel;

class LogistikController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('export')) {
            $type = $request->get('export');
            if ($type === 'pdf') {
                return $this->exportPdf($request);
            }
            if ($type === 'excel') {
                return $this->exportExcel($request);
            }
        }

        $search   = $request->get('search');
        $kategori = $request->get('kategori'); // This now refers to JenisLogistik ID or name
        $status   = $request->get('status');

        $query = StokLogistik::with(['itemLogistik.jenisLogistik']);

        if ($search) {
            $query->whereHas('itemLogistik', function($q) use ($search) {
                $q->where('nama_item', 'like', "%$search%");
            });
        }

        if ($kategori) {
            $query->whereHas('itemLogistik.jenisLogistik', function($q) use ($kategori) {
                $q->where('nama_jenis_logistik', $kategori);
            });
        }

        if ($status) {
            if ($status === 'Aman') {
                $query->whereRaw('jumlah_saat_ini > jumlah_minimum');
            } elseif ($status === 'Mendesak') {
                $query->whereRaw('jumlah_saat_ini <= jumlah_minimum AND jumlah_saat_ini >= (jumlah_minimum * 0.8)');
            } elseif ($status === 'Sangat Mendesak') {
                $query->whereRaw('jumlah_saat_ini < (jumlah_minimum * 0.8)');
            }
        }

        $logistiks = $query->paginate(10)->withQueryString();
        $jenisLogistiks = JenisLogistik::all();

        return view('pages.admin.logistik.index', compact('logistiks', 'search', 'kategori', 'status', 'jenisLogistiks'));
    }

    public function exportPdf(Request $request)
    {
        $search   = $request->get('search');
        $kategori = $request->get('kategori');
        $status   = $request->get('status');

        $query = StokLogistik::with(['itemLogistik.jenisLogistik']);

        if ($search) {
            $query->whereHas('itemLogistik', function($q) use ($search) {
                $q->where('nama_item', 'like', "%$search%");
            });
        }

        if ($kategori) {
            $query->whereHas('itemLogistik.jenisLogistik', function($q) use ($kategori) {
                $q->where('nama_jenis_logistik', $kategori);
            });
        }

        if ($status) {
            if ($status === 'Aman') {
                $query->whereRaw('jumlah_saat_ini > jumlah_minimum');
            } elseif ($status === 'Mendesak') {
                $query->whereRaw('jumlah_saat_ini <= jumlah_minimum AND jumlah_saat_ini >= (jumlah_minimum * 0.8)');
            } elseif ($status === 'Sangat Mendesak') {
                $query->whereRaw('jumlah_saat_ini < (jumlah_minimum * 0.8)');
            }
        }

        $logistiks = $query->get();

        $pdf = Pdf::loadView('pages.admin.logistik.export_pdf', compact('logistiks', 'kategori', 'status'));
        $fileName = 'Data_Logistik_Inventaris.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        $search   = $request->get('search');
        $kategori = $request->get('kategori');
        $status   = $request->get('status');

        $fileName = 'Data_Logistik_Inventaris.xlsx';
        return Excel::download(new LogistikExport($search, $kategori, $status), $fileName);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_logistik_id' => 'required|exists:jenis_logistiks,id',
            'nama_item'         => 'required|string|max:255',
            'satuan'            => 'required|string|max:50',
            'jumlah_saat_ini'   => 'required|integer|min:0',
            'jumlah_minimum'    => 'required|integer|min:0',
        ]);

        $item = ItemLogistik::create([
            'jenis_logistik_id' => $request->jenis_logistik_id,
            'nama_item'         => $request->nama_item,
            'satuan'            => $request->satuan,
        ]);

        StokLogistik::create([
            'item_logistik_id'  => $item->id,
            'jumlah_saat_ini'   => $request->jumlah_saat_ini,
            'jumlah_minimum'    => $request->jumlah_minimum,
        ]);

        return back()->with('success', 'Item logistik berhasil ditambahkan.');
    }

    public function show($id)
    {
        $logistik = StokLogistik::with(['itemLogistik.jenisLogistik'])->findOrFail($id);
        $riwayatMasuk  = PemasukanLogistik::with(['user', 'donasi'])->where('stok_logistik_id', $id)->latest('tanggal')->get();
        $riwayatKeluar = PengeluaranLogistik::with(['user', 'wargaBinaan'])->where('stok_logistik_id', $id)->latest('tanggal')->get();
        $wargaBinaans  = WargaBinaan::where('status', 'Aktif')->orderBy('nama')->get();

        return view('pages.admin.logistik.show', compact('logistik', 'riwayatMasuk', 'riwayatKeluar', 'wargaBinaans'));
    }

    public function update(Request $request, $id)
    {
        $logistik = StokLogistik::findOrFail($id);
        $item = $logistik->itemLogistik;

        $request->validate([
            'jenis_logistik_id' => 'required|exists:jenis_logistiks,id',
            'nama_item'         => 'required|string|max:255',
            'satuan'            => 'required|string|max:50',
            'jumlah_minimum'    => 'required|integer|min:0',
        ]);

        $item->update([
            'jenis_logistik_id' => $request->jenis_logistik_id,
            'nama_item'         => $request->nama_item,
            'satuan'            => $request->satuan,
        ]);

        $logistik->update([
            'jumlah_minimum'    => $request->jumlah_minimum,
        ]);

        return back()->with('success', 'Item logistik berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $logistik = StokLogistik::findOrFail($id);
        $item = $logistik->itemLogistik;
        $logistik->delete();
        $item->delete();
        
        return back()->with('success', 'Item logistik berhasil dihapus.');
    }

    public function storePemasukan(Request $request, $id)
    {
        $logistik = StokLogistik::findOrFail($id);

        $data = $request->validate([
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
            'tanggal'    => 'required|date',
            'kondisi'    => 'nullable|in:Baru,Bekas Layak',
        ]);

        PemasukanLogistik::create([
            'stok_logistik_id' => $logistik->id,
            'user_id'          => Auth::id(),
            'jumlah'           => $data['jumlah'],
            'tanggal'          => $data['tanggal'],
            'keterangan'       => $data['keterangan'] ?? null,
            'kondisi'          => $data['kondisi'] ?? null,
        ]);

        $logistik->increment('jumlah_saat_ini', $data['jumlah']);

        return back()->with('success', 'Pemasukan stok berhasil dicatat.');
    }

    public function storePengeluaran(Request $request, $id)
    {
        $logistik = StokLogistik::with('itemLogistik.jenisLogistik')->findOrFail($id);

        $rules = [
            'jumlah'          => 'required|integer|min:1',
            'tanggal'         => 'required|date',
            'keterangan'      => 'nullable|string',
        ];

        $isObat = $logistik->itemLogistik->jenisLogistik->nama_jenis_logistik === 'Obat';

        if ($isObat) {
            $rules['aturan_minum'] = 'required|string';
            $rules['warga_binaan_id'] = 'required|exists:warga_binaans,id';
        } else {
            $rules['warga_binaan_id'] = 'nullable|exists:warga_binaans,id';
        }

        $data = $request->validate($rules);

        if ($logistik->jumlah_saat_ini < $data['jumlah']) {
            return back()->withErrors(['jumlah' => 'Stok tidak mencukupi untuk pengeluaran ini.']);
        }

        PengeluaranLogistik::create([
            'stok_logistik_id' => $logistik->id,
            'user_id'          => Auth::id(),
            'warga_binaan_id'  => $data['warga_binaan_id'] ?? null,
            'jumlah'           => $data['jumlah'],
            'tanggal'          => $data['tanggal'],
            'keterangan'       => $data['keterangan'] ?? null,
        ]);

        if ($isObat) {
            StokObat::create([
                'warga_binaan_id' => $data['warga_binaan_id'],
                'nama_obat'       => $logistik->itemLogistik->nama_item,
                'aturan_minum'    => $data['aturan_minum'] ?? '',
                'satuan'          => $logistik->itemLogistik->satuan,
                'stok'            => $data['jumlah'],
                'jumlah_awal'     => $data['jumlah'],
                'sisa'            => $data['jumlah'],
                'tgl_mulai'       => $data['tanggal'],
                'tgl_update'      => $data['tanggal'],
                'status'          => 'AKTIF',
                'asal_obat'       => 'OBAT_GRIYA',
            ]);
        }

        $logistik->decrement('jumlah_saat_ini', $data['jumlah']);

        return back()->with('success', 'Pengeluaran stok berhasil dicatat.');
    }
}
