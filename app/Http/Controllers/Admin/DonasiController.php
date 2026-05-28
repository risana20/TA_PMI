<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\PemasukanLogistik;
use Illuminate\Http\Request;

class DonasiController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'Uang');
        $search = $request->get('search');

        $query = Donasi::with(['user', 'donasiUang', 'donasiMakanan', 'pemasukanLogistik.stokLogistik.itemLogistik'])
                    ->where('jenis', $tab)
                    ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_donatur', 'like', "%{$search}%");
                // For a more comprehensive search, we would join the tables, 
                // but keeping it simple for the main donatur name.
            });
        }

        $donasis = $query->paginate(10)->withQueryString();

        return view('pages.admin.donasi.index', compact('donasis', 'tab', 'search'));
    }

    public function show(Donasi $donasi)
    {
        $donasi->load(['user', 'donasiUang', 'donasiMakanan', 'pemasukanLogistik.stokLogistik.itemLogistik']);
        $stokLogistik = \App\Models\StokLogistik::with('itemLogistik')->get();
        return view('pages.admin.donasi.show', compact('donasi', 'stokLogistik'));
    }

    public function verify(Request $request, Donasi $donasi)
    {
        if ($donasi->jenis === 'Uang') {
            $donasi->update(['status' => 'Selesai']);
        } elseif ($donasi->jenis === 'Barang') {
            $pemasukan = $donasi->pemasukanLogistik;
            if ($pemasukan && $pemasukan->metode_penyerahan === 'Antar Sendiri') {
                $donasi->update(['status' => 'Menunggu Pengiriman']);
                $pemasukan->update(['status' => 'Menunggu Pengiriman']);
            } else {
                $donasi->update(['status' => 'Menunggu Donasi Dijemput Petugas']);
                if ($pemasukan) $pemasukan->update(['status' => 'Menunggu Donasi Dijemput Petugas']);
            }
        } else {
            // Makanan
            $donasi->update(['status' => 'Menunggu Pengiriman']); // Default for makanan without metode_penyerahan
        }
        
        return back()->with('success', 'Donasi berhasil disetujui.');
    }

    public function complete(Request $request, Donasi $donasi)
    {
        $data = ['status' => 'Selesai'];

        if ($donasi->jenis === 'Makanan') {
            if ($request->hasFile('bukti_diterima')) {
                $path = $request->file('bukti_diterima')->store('bukti_diterima', 'public');
                if ($donasi->donasiMakanan) {
                    $donasi->donasiMakanan->update(['bukti_diterima' => $path]);
                }
            }
            
            if ($request->has('masukkan_ke_stok') && $request->stok_logistik_id) {
                $existingPemasukan = \App\Models\PemasukanLogistik::where('donasi_id', $donasi->id)->first();
                if (!$existingPemasukan) {
                    // Parse integer from jumlah_makanan string
                    $jumlahStr = $donasi->donasiMakanan->jumlah_makanan ?? '1';
                    $jumlahInt = (int) filter_var($jumlahStr, FILTER_SANITIZE_NUMBER_INT);
                    $jumlahInt = $jumlahInt > 0 ? $jumlahInt : 1;

                    \App\Models\PemasukanLogistik::create([
                        'stok_logistik_id' => $request->stok_logistik_id,
                        'user_id'          => $donasi->user_id,
                        'donasi_id'        => $donasi->id,
                        'nama_barang'      => $donasi->donasiMakanan->nama_makanan,
                        'jumlah'           => $jumlahInt,
                        'tanggal'          => now()->toDateString(),
                        'kondisi'          => 'Baru',
                        'metode_penyerahan'=> $donasi->donasiMakanan?->metode_penyerahan ?? null,
                        'status'           => 'Selesai',
                        'bukti_diterima'   => $path ?? null,
                    ]);

                    $stok = \App\Models\StokLogistik::find($request->stok_logistik_id);
                    if ($stok) {
                        $stok->increment('jumlah_saat_ini', $jumlahInt);
                    }
                }
            }
        } elseif ($donasi->jenis === 'Barang') {
            if ($donasi->pemasukanLogistik) {
                $updateData = ['status' => 'Selesai'];

                if ($request->hasFile('bukti_diterima')) {
                    $path = $request->file('bukti_diterima')->store('bukti_diterima', 'public');
                    $updateData['bukti_diterima'] = $path;
                }

                if ($request->has('masukkan_ke_stok') && $request->stok_logistik_id && !$donasi->pemasukanLogistik->stok_logistik_id) {
                    $updateData['stok_logistik_id'] = $request->stok_logistik_id;
                    $stok = \App\Models\StokLogistik::find($request->stok_logistik_id);
                    if ($stok) {
                        $stok->increment('jumlah_saat_ini', $donasi->pemasukanLogistik->jumlah);
                    }
                }

                $donasi->pemasukanLogistik->update($updateData);
            }
        }

        $donasi->update($data);
        return back()->with('success', 'Donasi selesai dan bukti berhasil diupload.');
    }

    public function reject(Request $request, Donasi $donasi)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $donasi->update([
            'status' => 'Donasi Ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        if ($donasi->jenis === 'Barang' && $donasi->pemasukanLogistik) {
            $donasi->pemasukanLogistik->update(['status' => 'Donasi Ditolak']);
        }
        
        return back()->with('success', 'Donasi ditolak dan alasan berhasil disimpan.');
    }
}
