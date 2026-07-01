@extends('layout.public')

@section('title', 'Cek Status — Griya PMI Surakarta')

@push('styles')
<style>
    .tab-main.active   { background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.08); color: #111827; font-weight: 600; }
    .tab-main.inactive { background: transparent; color: #9CA3AF; }
    .tab-sub.active    { background: #DC2626; color: #fff; font-weight: 600; }
    .tab-sub.inactive  { background: transparent; color: #6B7280; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-red-600 to-red-700 text-white pt-12 pb-24">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="inline-flex items-center gap-2 bg-white/20 text-white text-xs font-semibold px-4 py-1.5 rounded-full mb-5 uppercase tracking-widest">
            <i class="fa-solid fa-magnifying-glass"></i>
            Cek Status Pengajuan
        </div>
        <h1 class="text-4xl font-bold mb-4">Cek Status</h1>
        <p class="text-red-100 text-base max-w-lg mx-auto leading-relaxed">
            Pantau status pengajuan donasi dan kunjungan Anda
        </p>
    </div>
</section>

{{-- Content --}}
<div class="bg-gray-50 pb-16">
    <div class="max-w-5xl mx-auto px-4 -mt-12 relative z-10 space-y-6">

        {{-- ① Tab Utama: Donasi | Kunjungan --}}
        <div class="bg-white rounded-2xl shadow-lg p-2 flex gap-2">
            <button id="tab-donasi" onclick="switchMain('donasi')"
                class="tab-main active flex-1 flex items-center justify-center gap-2 py-3 rounded-xl text-sm transition">
                <i class="fa-solid fa-heart text-red-500"></i>
                Donasi
            </button>
            <button id="tab-kunjungan" onclick="switchMain('kunjungan')"
                class="tab-main inactive flex-1 flex items-center justify-center gap-2 py-3 rounded-xl text-sm transition">
                <i class="fa-solid fa-calendar-days text-gray-400"></i>
                Kunjungan
            </button>
        </div>

        {{-- ② Panel Donasi --}}
        <div id="panel-donasi">

            {{-- Sub-tab: Uang | Barang | Makanan --}}
            <div class="flex gap-2 mb-4 bg-gray-100 p-1 rounded-xl w-fit">
                <button id="sub-uang" onclick="switchSub('uang')"
                    class="tab-sub active px-5 py-2 rounded-lg text-sm transition">
                    Donasi Uang
                </button>
                <button id="sub-barang" onclick="switchSub('barang')"
                    class="tab-sub inactive px-5 py-2 rounded-lg text-sm transition">
                    Donasi Barang
                </button>
                <button id="sub-makanan" onclick="switchSub('makanan')"
                    class="tab-sub inactive px-5 py-2 rounded-lg text-sm transition">
                    Donasi Makanan
                </button>
            </div>

            {{-- Tabel Donasi Uang --}}
            <div id="panel-uang" class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="font-bold text-gray-900">Riwayat Donasi Uang</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Status pengajuan donasi uang Anda</p>
                    </div>
                    <a href="{{ route('donasi.index') }}"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-full flex items-center gap-2 transition">
                        <i class="fa-solid fa-plus"></i> Ajukan Donasi
                    </a>
                </div>
                @if($donasiUang->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            <tr>
                                <th class="px-5 py-3 text-left">No</th>
                                <th class="px-5 py-3 text-left">Jumlah Donasi</th>
                                <th class="px-5 py-3 text-left">Transfer Bank Tujuan</th>
                                <th class="px-5 py-3 text-left">Bukti Transfer</th>
                                <th class="px-5 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($donasiUang as $i => $d)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-semibold text-gray-900">
                                    Rp {{ number_format($d->donasiUang->nominal ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-gray-600">{{ $d->donasiUang->bank_tujuan ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    @if($d->donasiUang && $d->donasiUang->bukti_transfer)
                                    <a href="{{ asset('storage/' . $d->donasiUang->bukti_transfer) }}" target="_blank"
                                        class="text-blue-500 hover:underline text-sm flex items-center gap-1">
                                        <i class="fa-solid fa-file-image text-xs"></i> Lihat Bukti
                                    </a>
                                    @else
                                    <span class="text-gray-400 text-xs">Belum upload</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @include('components.badge-donasi', ['status' => $d->status])
                                    @if($d->status === 'Donasi Ditolak' && $d->alasan_penolakan)
                                    <div class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded border border-red-100">
                                        <span class="font-semibold">Alasan:</span> {{ $d->alasan_penolakan }}
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-14 text-gray-400">
                    <i class="fa-solid fa-heart text-4xl mb-3 text-gray-200"></i>
                    <p class="text-sm">Belum ada riwayat donasi uang</p>
                </div>
                @endif
            </div>

            {{-- Tabel Donasi Barang --}}
            <div id="panel-barang" class="hidden bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="font-bold text-gray-900">Riwayat Donasi Barang</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Status pengajuan donasi barang Anda</p>
                    </div>
                    <button onclick="openModal()"
                        class="inline-flex items-center bg-white text-red-600 rounded-full px-5 py-2 font-semibold text-sm hover:bg-red-400 hover:text-white gap-2 transition ">
                        <i class="fa-solid fa-arrow-right"></i> Ajukan Kunjungan
                    </button>
                </div>
                @if($donasiBarang->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            <tr>
                                <th class="px-5 py-3 text-left">No</th>
                                <th class="px-5 py-3 text-left">Nama Barang</th>
                                <th class="px-5 py-3 text-left">Jumlah</th>
                                <th class="px-5 py-3 text-left">Kondisi</th>
                                <th class="px-5 py-3 text-left">Metode</th>
                                <th class="px-5 py-3 text-left">Waktu Penyerahan</th>
                                <th class="px-5 py-3 text-left">Status</th>
                                <th class="px-5 py-3 text-left">Bukti Diterima</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($donasiBarang as $i => $d)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-semibold text-gray-900">{{ $d->pemasukanLogistik->nama_barang ?? ($d->pemasukanLogistik->stokLogistik->itemLogistik->nama_item ?? '-') }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ $d->pemasukanLogistik->jumlah ?? 0 }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border border-gray-200 text-gray-600">
                                        {{ $d->pemasukanLogistik->kondisi ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        {{ $d->pemasukanLogistik->metode_penyerahan ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-gray-600 text-xs">
                                    <i class="fa-regular fa-calendar mr-1"></i> {{ $d->pemasukanLogistik && $d->pemasukanLogistik->tgl_penyerahan ? \Carbon\Carbon::parse($d->pemasukanLogistik->tgl_penyerahan)->format('d/m/Y') : '-' }}<br>
                                    <i class="fa-regular fa-clock mr-1"></i> {{ $d->pemasukanLogistik && $d->pemasukanLogistik->jam_penyerahan ? \Carbon\Carbon::parse($d->pemasukanLogistik->jam_penyerahan)->format('H:i') : '-' }}
                                </td>
                                <td class="px-5 py-4">
                                    @include('components.badge-donasi', ['status' => $d->status])
                                    @if($d->status === 'Donasi Ditolak' && $d->alasan_penolakan)
                                    <div class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded border border-red-100">
                                        <span class="font-semibold">Alasan:</span> {{ $d->alasan_penolakan }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if($d->pemasukanLogistik && $d->pemasukanLogistik->bukti_diterima)
                                    <a href="{{ asset('storage/' . $d->pemasukanLogistik->bukti_diterima) }}" target="_blank"
                                        class="text-blue-500 hover:underline text-sm flex items-center gap-1">
                                        <i class="fa-solid fa-file-image text-xs"></i> Lihat Bukti
                                    </a>
                                    @else
                                    <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-14 text-gray-400">
                    <i class="fa-solid fa-box text-4xl mb-3 text-gray-200"></i>
                    <p class="text-sm">Belum ada riwayat donasi barang</p>
                </div>
                @endif
            </div>

            {{-- Tabel Donasi Makanan --}}
            <div id="panel-makanan" class="hidden bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="font-bold text-gray-900">Riwayat Donasi Makanan</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Status pengajuan donasi makanan Anda</p>
                    </div>
                    <a href="{{ route('donasi.index') }}"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-full flex items-center gap-2 transition">
                        <i class="fa-solid fa-plus"></i> Ajukan Donasi
                    </a>
                </div>
                @if($donasiMakanan->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            <tr>
                                <th class="px-5 py-3 text-left">No</th>
                                <th class="px-5 py-3 text-left">Nama Makanan</th>
                                <th class="px-5 py-3 text-left">Jumlah</th>
                                <th class="px-5 py-3 text-left">Jenis</th>
                                <th class="px-5 py-3 text-left">Metode</th>
                                <th class="px-5 py-3 text-left">Waktu Penyerahan</th>
                                <th class="px-5 py-3 text-left">Status</th>
                                <th class="px-5 py-3 text-left">Bukti Diterima</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($donasiMakanan as $i => $d)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-semibold text-gray-900">{{ $d->donasiMakanan->nama_makanan ?? '-' }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ $d->donasiMakanan->jumlah_makanan ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-50 text-orange-600 border border-orange-100">
                                        {{ $d->donasiMakanan->jenis_makanan ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        {{ $d->donasiMakanan->metode_penyerahan ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-gray-600 text-xs">
                                    <i class="fa-regular fa-calendar mr-1"></i> {{ $d->donasiMakanan && $d->donasiMakanan->tgl_penyerahan ? \Carbon\Carbon::parse($d->donasiMakanan->tgl_penyerahan)->format('d/m/Y') : '-' }}<br>
                                    <i class="fa-regular fa-clock mr-1"></i> {{ $d->donasiMakanan && $d->donasiMakanan->jam_penyerahan ? \Carbon\Carbon::parse($d->donasiMakanan->jam_penyerahan)->format('H:i') : '-' }}
                                </td>
                                <td class="px-5 py-4">
                                    @include('components.badge-donasi', ['status' => $d->status])
                                    @if($d->status === 'Donasi Ditolak' && $d->alasan_penolakan)
                                    <div class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded border border-red-100">
                                        <span class="font-semibold">Alasan:</span> {{ $d->alasan_penolakan }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if($d->donasiMakanan && $d->donasiMakanan->bukti_diterima)
                                    <a href="{{ asset('storage/' . $d->donasiMakanan->bukti_diterima) }}" target="_blank"
                                        class="text-blue-500 hover:underline text-sm flex items-center gap-1">
                                        <i class="fa-solid fa-file-image text-xs"></i> Lihat Bukti
                                    </a>
                                    @else
                                    <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-14 text-gray-400">
                    <i class="fa-solid fa-utensils text-4xl mb-3 text-gray-200"></i>
                    <p class="text-sm">Belum ada riwayat donasi makanan</p>
                </div>
                @endif
            </div>

        </div>

        {{-- ③ Panel Kunjungan --}}
        <div id="panel-kunjungan" class="hidden">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-calendar-days text-red-600 text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900">Riwayat Pengajuan Kunjungan</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Status pengajuan kunjungan Anda</p>
                        </div>
                    </div>
                    <button onclick="openModal()"
                        class="inline-flex items-center bg-white text-red-600 rounded-full px-5 py-2 font-semibold text-sm hover:bg-red-400 hover:text-white gap-2 transition ">
                        <i class="fa-solid fa-arrow-right"></i> Ajukan Kunjungan
                    </button>
                </div>

                @if($kunjungan->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            <tr>
                                <th class="px-5 py-3 text-left">No</th>
                                <th class="px-5 py-3 text-left">Tujuan Kunjungan</th>
                                <th class="px-5 py-3 text-left">Surat Kunjungan</th>
                                <th class="px-5 py-3 text-left">Tanggal</th>
                                <th class="px-5 py-3 text-left">Jam</th>
                                <th class="px-5 py-3 text-left">Status</th>
                                <th class="px-5 py-3 text-left">Alasan Penolakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($kunjungan as $i => $k)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 text-gray-700">{{ $k->tujuan }}</td>
                                {{-- <td class="px-5 py-4 font-semibold text-gray-900">{{ $k->nama_kunjungan }}</td> --}}
                                <td class="px-5 py-4">
                                    @if($k->surat_pengajuan)
                                    <a href="{{ asset('storage/' . $k->surat_pengajuan) }}" target="_blank"
                                        class="text-blue-500 hover:underline text-sm flex items-center gap-1">
                                        <i class="fa-solid fa-file text-xs"></i> Lihat Surat
                                    </a>
                                    @else
                                    <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-600">
                                    {{ \Carbon\Carbon::parse($k->tgl_kunjungan)->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-gray-600">{{ $k->formatted_jam }}</td>
                                <td class="px-5 py-4">
                                    @include('components.badge-kunjungan', ['status' => $k->status])
                                </td>
                                <td class="px-5 py-4 text-gray-600">
                                    @if($k->status == 'DITOLAK')
                                        {{ $k->alasan_tolak ?? '-' }}
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-14 text-gray-400">
                    <i class="fa-solid fa-calendar-days text-4xl mb-3 text-gray-200"></i>
                    <p class="text-sm">Belum ada riwayat pengajuan kunjungan</p>
                </div>
                @endif
            </div>
        </div>

    </div>
    <div id="modal-tambah" class="hidden fixed inset-0 bg-black/40 z-50 flex items-start justify-center pt-28 p-4">

        <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-2xl max-h-[75vh] overflow-y-auto relative">
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                    class="absolute top-3 right-3 text-gray-400 hover:text-red-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900">Form Pengajuan Kunjungan</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                        Lengkapi data berikut untuk mengajukan permohonan kunjungan
                </p>
            </div>

            <form method="POST" action="{{ route('kunjungan.store') }}" enctype="multipart/form-data">
                 @csrf

                <input type="hidden" name="nama_pengunjung" value="{{ auth()->user()->name }}">
                <input type="hidden" name="no_hp" value="{{ auth()->user()->phone }}">

                <div class="space-y-5">
                {{-- Nama Pengunjung --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pengunjung</label>
                    <input type="text"
                        value="{{ auth()->user()->name }}"readonly
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100">
                </div>
                {{-- No HP --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" 
                        value="{{ auth()->user()->phone }}" readonly
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100">
                </div>
        
                {{-- Tujuan Kunjungan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Kunjungan</label>

                    <div class="relative">
                        <select id="tujuan" name="tujuan" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
                                <option value="">Pilih tujuan...</option>
                                <option value="Silaturahmi" {{ old('tujuan') === 'Silaturahmi' ? 'selected' : '' }}>Silaturahmi</option>
                                <option value="Penelitian" {{ old('tujuan') === 'Penelitian' ? 'selected' : '' }}>Penelitian</option>
                                <option value="Kerjasama" {{ old('tujuan') === 'Kerjasama' ? 'selected' : '' }}>Kerjasama</option>
                                <option value="Magang/PKL" {{ old('tujuan') === 'Magang/PKL' ? 'selected' : '' }}>Magang / PKL</option>
                                <option value="Lainnya" {{ old('tujuan') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>

                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </span>
                    </div>
                </div>

                {{-- Instansi --}}
                <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Asal Instansi</label>

                            <input type="text"
                                name="instansi"
                                required
                                value="{{ old('instansi') }}"
                                placeholder="Contoh: Universitas X / Masyarakat Umum"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                        {{-- Upload Surat --}}
                        <div id="surat-field">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Upload Surat Kunjungan (Wajib Untuk tujuan Penelitian, Kegiatan, Magang/PKL)
                            </label>

                            <label class="flex items-center justify-between border border-gray-200 rounded-lg px-3 py-2 cursor-pointer bg-white hover:border-gray-300 transition">
                                <span id="surat-label-text" class="text-sm text-gray-400 truncate">
                                    No file chosen *pdf/jpg/pnj
                                </span>
                                <i class="fa-solid fa-arrow-up-from-bracket text-gray-400 text-sm ml-2"></i>
                                <input type="file"
                                    name="surat_pengajuan"
                                    id="surat-input"
                                    accept=".pdf,image/*"
                                    class="hidden"
                                    onchange="previewSurat(this)">
                            </label>
                        </div>

                        {{-- Tanggal + Jam --}}
                        <div class="grid grid-cols-2 gap-4">

                            {{-- Tanggal --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Kunjungan
                                </label>

                                <input type="date"
                                    id="tgl-picker"
                                    name="tgl_kunjungan"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 bg-white"
                                    min="{{ date('Y-m-d') }}"
                                    required>
                            </div>

                            {{-- Jam --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Sesi Kunjungan
                                </label>

                                <select name="jam" id="jam-select" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
                                    <option value="">Pilih Sesi...</option>
                                    <option value="Sesi 1: 08.00-09.30">Sesi 1: 08.00-09.30</option>
                                    <option value="Sesi 2: 09.30-11.00">Sesi 2: 09.30-11.00</option>
                                    <option value="Sesi 3: 11.00-12.30">Sesi 3: 11.00-12.30</option>
                                    <option value="Sesi 4: 13.00-14.30">Sesi 4: 13.00-14.30</option>
                                    <option value="Sesi 5: 14.30-16.00">Sesi 5: 14.30-16.00</option>
                                </select>
                            </div>

                        </div>

                        {{-- Info --}}
                        <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info text-red-500 text-sm"></i>

                            <p class="text-sm text-red-700">
                                <span class="font-semibold">Informasi:</span>
                                Jam operasional kunjungan dibagi menjadi 5 sesi per hari mulai dari <span class="font-semibold">08:00 – 16:00 WIB</span> dengan 1 sesi istirahat.
                            </p>
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-full text-sm transition flex items-center justify-center gap-2">

                            <i class="fa-solid fa-user-group"></i>
                            Ajukan Kunjungan

                        </button>

                    </div>
                </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const dbJadwalDisetujui = @json($jadwalDisetujui);

function switchMain(tab) {
    const panels = ['donasi', 'kunjungan'];
    panels.forEach(p => {
        document.getElementById('panel-' + p).classList.toggle('hidden', p !== tab);
        const btn = document.getElementById('tab-' + p);
        btn.classList.toggle('active',   p === tab);
        btn.classList.toggle('inactive', p !== tab);

        // Sync icon color
        const icon = btn.querySelector('i');
        if (p === tab) {
            icon.classList.replace('text-gray-400', 'text-red-500');
        } else {
            icon.classList.remove('text-red-500');
            icon.classList.add('text-gray-400');
        }
    });
}

function switchSub(sub) {
    const subs = ['uang', 'barang', 'makanan'];
    subs.forEach(s => {
        document.getElementById('panel-' + s).classList.toggle('hidden', s !== sub);
        const btn = document.getElementById('sub-' + s);
        btn.classList.toggle('active',   s === sub);
        btn.classList.toggle('inactive', s !== sub);
    });
}

function formatYYYYMMDD(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

function getVisitDateString(visit) {
    if (!visit.tgl_kunjungan) return "";
    return visit.tgl_kunjungan.split('T')[0].split(' ')[0];
}

function mapJamToSession(jamStr) {
    if (!jamStr) return null;
    jamStr = jamStr.trim();
    if (jamStr.toLowerCase().startsWith('sesi 1')) return 'Sesi 1';
    if (jamStr.toLowerCase().startsWith('sesi 2')) return 'Sesi 2';
    if (jamStr.toLowerCase().startsWith('sesi 3')) return 'Sesi 3';
    if (jamStr.toLowerCase().startsWith('sesi 4')) return 'Sesi 4';
    if (jamStr.toLowerCase().startsWith('sesi 5')) return 'Sesi 5';

    let timePart = jamStr.replace('.', ':');
    let match = timePart.match(/(\d{2}):(\d{2})/);
    if (!match) return null;
    let hour = parseInt(match[1]);
    let min = parseInt(match[2]);
    let totalMinutes = hour * 60 + min;

    if (totalMinutes >= 480 && totalMinutes < 570) return 'Sesi 1';
    if (totalMinutes >= 570 && totalMinutes < 660) return 'Sesi 2';
    if (totalMinutes >= 660 && totalMinutes < 780) return 'Sesi 3';
    if (totalMinutes >= 780 && totalMinutes < 870) return 'Sesi 4';
    if (totalMinutes >= 870 && totalMinutes <= 990) return 'Sesi 5';

    return null;
}

function updateAvailableSessions(dateStr) {
    const jamSelect = document.getElementById('jam-select');
    if (!jamSelect) return;

    Array.from(jamSelect.options).forEach(opt => {
        if (!opt.value) return;
        opt.disabled = false;
        opt.textContent = opt.value;
    });

    if (!dateStr) return;

    const bookedSessions = [];
    dbJadwalDisetujui.forEach(v => {
        const vDate = getVisitDateString(v);
        if (vDate === dateStr) {
            const sess = mapJamToSession(v.jam);
            if (sess) bookedSessions.push(sess);
        }
    });

    const todayStr = formatYYYYMMDD(new Date());
    const isToday = (dateStr === todayStr);

    const nowTime = new Date();
    const currentHour = nowTime.getHours();
    const currentMin = nowTime.getMinutes();
    const currentTotalMinutes = currentHour * 60 + currentMin;

    const sessionStartTimes = {
        'Sesi 1: 08.00-09.30': 480,
        'Sesi 2: 09.30-11.00': 570,
        'Sesi 3: 11.00-12.30': 660,
        'Sesi 4: 13.00-14.30': 780,
        'Sesi 5: 14.30-16.00': 870,
    };

    Array.from(jamSelect.options).forEach(opt => {
        if (!opt.value) return;
        const optSess = mapJamToSession(opt.value);
        const startTime = sessionStartTimes[opt.value];
        const isPassed = isToday && (currentTotalMinutes >= startTime);

        if (bookedSessions.includes(optSess)) {
            opt.disabled = true;
            opt.textContent = opt.value + ' (Terisi)';
        } else if (isPassed) {
            opt.disabled = true;
            opt.textContent = opt.value + ' (Sudah Terlewat)';
        }
    });
}

document.getElementById('tgl-picker').addEventListener('change', function() {
    updateAvailableSessions(this.value);
});

function initSurat() {
    const tujuan = document.getElementById('tujuan');
    const suratInput = document.getElementById('surat-input');
    const suratLabel = document.getElementById('surat-upload');

    if (!tujuan || !suratInput || !suratLabel) return;

    function updateSurat() {
        const value = tujuan.value;
        const aktif = (
            value === "Penelitian" ||
            value === "Kerjasama" ||
            value === "Magang/PKL"
        );

        if (aktif) {
            suratInput.disabled = false;
            suratInput.required = true;
            suratLabel.classList.remove('opacity-50', 'pointer-events-none');
        } else {
            suratInput.disabled = true;
            suratInput.required = false;
            suratInput.value = "";
            suratLabel.classList.add('opacity-50', 'pointer-events-none');
        }
    }

    tujuan.addEventListener('change', updateSurat);
    updateSurat();
}

function openModal() {
    const modal = document.getElementById('modal-tambah');
    if (!modal) return;
    modal.classList.remove('hidden');

    const tglPicker = document.getElementById('tgl-picker');
    if (tglPicker) {
        if (!tglPicker.value) {
            tglPicker.value = formatYYYYMMDD(new Date());
        }
        updateAvailableSessions(tglPicker.value);
    }
    initSurat();
}

function previewSurat(input) {
    const lbl = document.getElementById('surat-label-text');
    if (input.files && input.files[0]) {
        lbl.textContent = input.files[0].name;
        lbl.classList.remove('text-gray-400');
        lbl.classList.add('text-gray-700');
    }
}
</script>
@endpush
