<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\PemasukanLogistik;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DonasiExport;
use Maatwebsite\Excel\Facades\Excel;

class DonasiController extends Controller
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

        $tab    = $request->get('tab', 'Uang');
        $search = $request->get('search');
        $statusDonasi = $request->get('status_donasi');
        $statusLogistik = $request->get('status_logistik');

        $query = Donasi::with(['user', 'donasiUang', 'donasiMakanan', 'pemasukanLogistik.stokLogistik.itemLogistik'])
                    ->where('jenis', $tab)
                    ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_donatur', 'like', "%{$search}%");
            });
        }

        if ($statusDonasi) {
            $query->where('status', $statusDonasi);
        }

        if ($statusLogistik && $tab !== 'Uang') {
            if ($tab === 'Barang') {
                if ($statusLogistik === 'Sudah') {
                    $query->whereHas('pemasukanLogistik', function ($q) {
                        $q->whereNotNull('stok_logistik_id');
                    });
                } elseif ($statusLogistik === 'Belum') {
                    $query->where(function ($q) {
                        $q->whereHas('pemasukanLogistik', function ($sub) {
                            $sub->whereNull('stok_logistik_id');
                        })->orWhereDoesntHave('pemasukanLogistik');
                    });
                }
            } elseif ($tab === 'Makanan') {
                if ($statusLogistik === 'Sudah') {
                    $query->whereHas('pemasukanLogistik');
                } elseif ($statusLogistik === 'Belum') {
                    $query->whereDoesntHave('pemasukanLogistik');
                }
            }
        }

        $donasis = $query->paginate(10)->withQueryString();

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

        return view('pages.admin.donasi.index', compact('donasis', 'tab', 'search', 'makananStok', 'barangStok'));
    }

    public function exportPdf(Request $request)
    {
        $tab    = $request->get('tab', 'Uang');
        $search = $request->get('search');
        $statusDonasi = $request->get('status_donasi');
        $statusLogistik = $request->get('status_logistik');

        $query = Donasi::with(['user', 'donasiUang', 'donasiMakanan', 'pemasukanLogistik.stokLogistik.itemLogistik'])
                    ->where('jenis', $tab)
                    ->latest();

        if ($search) {
            $query->where('nama_donatur', 'like', "%{$search}%");
        }

        if ($statusDonasi) {
            $query->where('status', $statusDonasi);
        }

        if ($statusLogistik && $tab !== 'Uang') {
            if ($tab === 'Barang') {
                if ($statusLogistik === 'Sudah') {
                    $query->whereHas('pemasukanLogistik', function ($q) {
                        $q->whereNotNull('stok_logistik_id');
                    });
                } elseif ($statusLogistik === 'Belum') {
                    $query->where(function ($q) {
                        $q->whereHas('pemasukanLogistik', function ($sub) {
                            $sub->whereNull('stok_logistik_id');
                        })->orWhereDoesntHave('pemasukanLogistik');
                    });
                }
            } elseif ($tab === 'Makanan') {
                if ($statusLogistik === 'Sudah') {
                    $query->whereHas('pemasukanLogistik');
                } elseif ($statusLogistik === 'Belum') {
                    $query->whereDoesntHave('pemasukanLogistik');
                }
            }
        }

        $donasis = $query->get();

        $pdf = Pdf::loadView('pages.admin.donasi.export_pdf', compact('donasis', 'tab'));
        $fileName = 'Laporan Data Donasi - ' . $tab . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        $tab    = $request->get('tab', 'Uang');
        $search = $request->get('search');
        $statusDonasi = $request->get('status_donasi');
        $statusLogistik = $request->get('status_logistik');

        $fileName = 'Laporan Data Donasi - ' . $tab . '.xlsx';
        return Excel::download(new DonasiExport($tab, $search, $statusDonasi, $statusLogistik), $fileName);
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
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string',
        ];

        $extraRules = match ($jenis) {
            'Uang' => [
                'nominal'       => 'required|integer|min:1',
                'bank_tujuan'   => 'required|string',
                'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
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
                'metode_penyerahan'  => 'nullable|in:Antar Sendiri,Dijemput Petugas',
                'tgl_penyerahan'     => 'nullable|date',
                'jam_penyerahan'     => 'nullable',
            ],
            default => [],
        };

        if ($request->has('metode_penyerahan') && $request->metode_penyerahan === 'Dijemput petugas') {
            $request->merge(['metode_penyerahan' => 'Dijemput Petugas']);
        }

        $data = $request->validate(array_merge($baseRules, $extraRules));

        // Create user in the background for this offline donor
        $user = \App\Models\User::create([
            'name'      => $data['nama_donatur'],
            'email'     => 'offline_' . time() . '_' . rand(1000, 9999) . '@griyapmi.id',
            'password'  => bcrypt(\Illuminate\Support\Str::random(16)),
            'phone'     => $data['phone'] ?? null,
            'address'   => $data['address'] ?? null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');

        // Create main Donasi
        $donasi = Donasi::create([
            'user_id'      => $user->id,
            'jenis'        => $jenis,
            'nama_donatur' => $data['nama_donatur'],
            'status'       => 'Tunggu Verifikasi',
        ]);

        if ($jenis === 'Uang') {
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer')) {
                $buktiPath = $request->file('bukti_transfer')->store('donasi', 'public');
            }

            \App\Models\DonasiUang::create([
                'donasi_id'      => $donasi->id,
                'nominal'        => $data['nominal'],
                'bank_tujuan'    => $data['bank_tujuan'],
                'bukti_transfer' => $buktiPath,
            ]);
        } elseif ($jenis === 'Makanan') {
            \App\Models\DonasiMakanan::create([
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
                'user_id'           => $user->id,
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

        return back()->with('success', 'Donasi berhasil ditambahkan.');
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
        if ($request->has('masukkan_ke_stok')) {
            $request->validate([
                'stok_logistik_id' => 'required|exists:stok_logistiks,id',
                'jumlah'           => 'required|integer|min:1',
            ]);
        }

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
                    $jumlahInt = (int) $request->input('jumlah');

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
                    $jumlahInt = (int) $request->input('jumlah');
                    $updateData['jumlah'] = $jumlahInt;

                    $stok = \App\Models\StokLogistik::find($request->stok_logistik_id);
                    if ($stok) {
                        $stok->increment('jumlah_saat_ini', $jumlahInt);
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
