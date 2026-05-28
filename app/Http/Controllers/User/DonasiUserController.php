<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\DonasiUang;
use App\Models\DonasiMakanan;
use App\Models\PemasukanLogistik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonasiUserController extends Controller
{
    public function index()
    {
        $riwayat = Donasi::with(['donasiUang', 'donasiMakanan', 'pemasukanLogistik'])
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->get();
        
        $stokLogistik = \App\Models\StokLogistik::with('itemLogistik.jenisLogistik')->get();

        $makananStok = \App\Models\StokLogistik::with('itemLogistik.jenisLogistik')
            ->whereHas('itemLogistik.jenisLogistik', function($q) {
                $q->where('nama_jenis_logistik', 'Makanan');
            })
            ->get();

        $barangStok = \App\Models\StokLogistik::with('itemLogistik.jenisLogistik')
            ->whereHas('itemLogistik.jenisLogistik', function($q) {
                $q->where('nama_jenis_logistik', 'Barang');
            })
            ->get();

        return view('pages.user.donasi', compact('riwayat', 'stokLogistik', 'makananStok', 'barangStok'));
    }

    public function store(Request $request)
    {
        $isLainnyaMakanan = false;
        if ($request->jenis === 'Makanan') {
            if ($request->has('jumlah_makanan_value') && $request->has('jumlah_makanan_satuan')) {
                $combinedJumlah = trim($request->input('jumlah_makanan_value') . ' ' . $request->input('jumlah_makanan_satuan'));
                $request->merge(['jumlah_makanan' => $combinedJumlah]);
            }

            if ($request->input('nama_makanan') === 'Lainnya') {
                $isLainnyaMakanan = true;
                if ($request->has('nama_makanan_custom')) {
                    $request->merge(['nama_makanan' => $request->input('nama_makanan_custom')]);
                }
            }
        }

        if ($request->jenis === 'Barang') {
            if ($request->input('nama_barang') === 'Lainnya') {
                if ($request->has('nama_barang_custom')) {
                    $request->merge(['nama_barang' => $request->input('nama_barang_custom')]);
                }
            }
        }

        $jenis = $request->jenis;

        $baseRules = [
            'jenis'        => 'required|in:Uang,Barang,Makanan',
            'nama_donatur' => 'required|string|max:255',
        ];

        $extraRules = match ($jenis) {
            'Uang' => [
                'nominal'       => 'required|integer|min:1',
                'bank_tujuan'   => 'required|string',
                'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            ],
            'Barang' => [
                'nama_barang'        => 'required|string',
                'jumlah_barang'      => 'required|integer|min:1',
                'satuan'             => 'nullable|string',
                'kondisi'            => 'required|in:Baru,Bekas Layak',
                'metode_penyerahan'  => 'required|in:Antar Sendiri,Dijemput Petugas',
                'tgl_penyerahan'     => 'required|date',
                'jam_penyerahan'     => 'required',
            ],
            'Makanan' => [
                'nama_makanan'       => 'required|string',
                'jenis_makanan'      => $isLainnyaMakanan ? 'required|in:Bahan Mentah,Siap Saji' : 'nullable|in:Bahan Mentah,Siap Saji',
                'jumlah_makanan'     => 'required|string',
                'metode_penyerahan'  => 'nullable|in:Antar Sendiri,Dijemput Petugas', // Added nullable for UI compatibility
                'tgl_penyerahan'     => 'nullable|date',
                'jam_penyerahan'     => 'nullable',
            ],
            default => [],
        };

        // For backward compatibility with 'Dijemput petugas'
        if ($request->has('metode_penyerahan') && $request->metode_penyerahan === 'Dijemput petugas') {
            $request->merge(['metode_penyerahan' => 'Dijemput Petugas']);
        }

        $data = $request->validate(array_merge($baseRules, $extraRules));

        // Create main Donasi
        $donasi = Donasi::create([
            'user_id'      => Auth::id(),
            'jenis'        => $jenis,
            'nama_donatur' => $data['nama_donatur'],
            'status'       => 'Tunggu Verifikasi',
        ]);

        if ($jenis === 'Uang') {
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer')) {
                $buktiPath = $request->file('bukti_transfer')->store('donasi', 'public');
            }

            DonasiUang::create([
                'donasi_id'      => $donasi->id,
                'nominal'        => $data['nominal'],
                'bank_tujuan'    => $data['bank_tujuan'],
                'bukti_transfer' => $buktiPath,
            ]);
        } elseif ($jenis === 'Makanan') {
            DonasiMakanan::create([
                'donasi_id'      => $donasi->id,
                'nama_makanan'   => $data['nama_makanan'],
                'jenis_makanan'  => $data['jenis_makanan'] ?? null,
                'jumlah_makanan' => $data['jumlah_makanan'],
                'metode_penyerahan' => $data['metode_penyerahan'] ?? null,
                'tgl_penyerahan'    => $data['tgl_penyerahan'] ?? null,
                'jam_penyerahan'    => $data['jam_penyerahan'] ?? null,
            ]);
        } elseif ($jenis === 'Barang') {
            PemasukanLogistik::create([
                'nama_barang'       => $data['nama_barang'],
                'user_id'           => Auth::id(),
                'donasi_id'         => $donasi->id,
                'jumlah'            => $data['jumlah_barang'],
                'satuan'            => $data['satuan'] ?? null,
                'tanggal'           => $data['tgl_penyerahan'],
                'kondisi'           => $data['kondisi'],
                'metode_penyerahan' => $data['metode_penyerahan'],
                'tgl_penyerahan'    => $data['tgl_penyerahan'],
                'jam_penyerahan'    => $data['jam_penyerahan'],
                'status'            => 'Tunggu Verifikasi',
            ]);
        }

        return back()->with('success', 'Donasi berhasil dikirim. Menunggu verifikasi admin.');
    }
}
