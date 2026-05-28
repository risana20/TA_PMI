@extends('layout.app')

@section('title', 'Detail — ' . $wargaBinaan->nama)

@section('content')

{{-- Back + Header --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.index', ['tab' => $wargaBinaan->kategori]) }}"
        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-gray-700 hover:border-gray-300 transition">
        <i class="fa-solid fa-arrow-left text-sm"></i>
    </a>
    <div>
        <h1 class="text-xl font-bold text-gray-900">Detail Warga Binaan</h1>
        <p class="text-sm text-gray-400">{{ $wargaBinaan->kategori }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ① Kartu Profil --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

        {{-- Foto & Nama --}}
        <div class="text-center mb-6">
            <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center mx-auto overflow-hidden mb-4 ring-4 ring-gray-50">
                @if($wargaBinaan->foto)
                    <img src="{{ Storage::url($wargaBinaan->foto) }}" alt="{{ $wargaBinaan->nama }}" class="w-full h-full object-cover">
                @else
                    <i class="fa-solid fa-user text-gray-300 text-3xl"></i>
                @endif
            </div>
            <h2 class="font-bold text-lg text-gray-900">{{ $wargaBinaan->nama }}</h2>
            <p class="text-sm text-gray-400 mt-0.5">{{ $wargaBinaan->nik }}</p>
            <div class="mt-3 flex justify-center">
                @include('components.badge-status', ['status' => $wargaBinaan->status])
            </div>
        </div>

        {{-- Info Rows --}}
        <div class="space-y-3 text-sm border-t border-gray-100 pt-4">
            <div class="flex items-start justify-between gap-3">
                <span class="text-gray-400 shrink-0">Jenis Kelamin</span>
                <span class="font-medium text-gray-800 text-right">
                    {{ $wargaBinaan->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                </span>
            </div>
            <div class="flex items-start justify-between gap-3">
                <span class="text-gray-400 shrink-0">TTL</span>
                <span class="font-medium text-gray-800 text-right">
                    {{ $wargaBinaan->tempat_lahir }}, {{ $wargaBinaan->tgl_lahir->format('d M Y') }}
                </span>
            </div>
            <div class="flex items-start justify-between gap-3">
                <span class="text-gray-400 shrink-0">Umur</span>
                <span class="font-medium text-gray-800">{{ $wargaBinaan->umur }} tahun</span>
            </div>
            <div class="flex items-start justify-between gap-3">
                <span class="text-gray-400 shrink-0">Tgl Masuk</span>
                <span class="font-medium text-gray-800">{{ $wargaBinaan->tgl_masuk->format('d M Y') }}</span>
            </div>
            @if($wargaBinaan->no_bpjs)
            <div class="flex items-start justify-between gap-3">
                <span class="text-gray-400 shrink-0">No. BPJS</span>
                <span class="font-medium text-gray-800 text-right">{{ $wargaBinaan->no_bpjs }}</span>
            </div>
            @endif
            @if($wargaBinaan->penanggung_jawab)
            <div class="flex items-start justify-between gap-3">
                <span class="text-gray-400 shrink-0">Penanggung Jawab</span>
                <span class="font-medium text-gray-800 text-right">{{ $wargaBinaan->penanggung_jawab }}</span>
            </div>
            @endif
            @if($wargaBinaan->kontak_pj)
            <div class="flex items-start justify-between gap-3">
                <span class="text-gray-400 shrink-0">Kontak Penanggung Jawab</span>
                <a href="https://wa.me/{{ $wargaBinaan->kontak_pj }}" target="_blank"
                class="font-medium text-green-600 hover:underline text-right">
                    {{ $wargaBinaan->kontak_pj }}
                </a>
            </div>
            @endif
        </div>

        @if($wargaBinaan->alamat)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Alamat</p>
            <p class="text-sm text-gray-700 leading-relaxed">{{ $wargaBinaan->alamat }}</p>
        </div>
        @endif

        @if($wargaBinaan->catatan)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Catatan</p>
            <p class="text-sm text-gray-700 leading-relaxed">{{ $wargaBinaan->catatan }}</p>
        </div>
        @endif
    </div>

    {{-- ② Riwayat Monitoring --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <i class="fa-solid fa-heart-pulse text-red-500 text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Riwayat Monitoring Kesehatan</h3>
            </div>
            <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.show', $wargaBinaan) }}"
                class="bg-red-600 hover:bg-red-700 text-white rounded-full px-4 py-1.5 text-xs font-semibold transition flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Tambah Pemeriksaan
            </a>
        </div>

        @forelse($riwayatMonitoring as $r)
        <div class="border border-gray-100 rounded-xl p-4 mb-3 hover:border-gray-200 transition">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-red-50 flex items-center justify-center">
                        <i class="fa-solid fa-calendar text-red-400 text-xs"></i>
                    </div>
                    <p class="font-semibold text-sm text-gray-900">{{ $r->tanggal->format('d M Y') }}</p>
                </div>
                <p class="text-xs text-gray-400">Petugas: <span class="font-medium">{{ $r->petugas ?? '-' }}</span></p>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="bg-gray-50 rounded-lg px-3 py-2">
                    <p class="text-xs text-gray-400 mb-0.5">Tekanan Darah</p>
                    <p class="font-semibold text-gray-800">{{ $r->tekanan_darah ?? '-' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg px-3 py-2">
                    <p class="text-xs text-gray-400 mb-0.5">Berat Badan</p>
                    <p class="font-semibold text-gray-800">{{ $r->berat_badan ? $r->berat_badan . ' kg' : '-' }}</p>
                </div>
            </div>
            @if($r->catatan)
            <p class="text-xs text-gray-500 mt-3 leading-relaxed">{{ $r->catatan }}</p>
            @endif
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <i class="fa-solid fa-heart-pulse text-4xl mb-3 text-gray-200"></i>
            <p class="text-sm">Belum ada riwayat pemeriksaan</p>
            <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.show', $wargaBinaan) }}"
                class="mt-3 text-xs text-red-600 hover:underline">+ Tambah pemeriksaan pertama</a>
        </div>
        @endforelse
    </div>

</div>

@endsection
