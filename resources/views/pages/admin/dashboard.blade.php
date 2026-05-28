@extends('layout.app')

@section('title', 'Dashboard — Griya PMI Surakarta')

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">Selamat datang, {{ auth()->user()->name }}</p>
    </div>
    <div class="hidden sm:block text-right">
        <p class="text-sm font-medium text-gray-700">{{ now()->translatedFormat('l') }}</p>
        <p class="text-xs text-gray-400">{{ now()->translatedFormat('d F Y') }}</p>
    </div>
</div>

{{-- Statistik Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Warga Binaan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Warga Binaan</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalWarga }}</p>
                <p class="text-xs text-gray-500 mt-1">Aktif saat ini</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-users text-blue-500 text-base"></i>
            </div>
        </div>
    </div>

    {{-- Saldo Keuangan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Saldo Keuangan</p>
                <p class="text-xl font-bold text-gray-900 mt-2 leading-tight">
                    Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Total saldo berjalan</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-wallet text-green-500 text-base"></i>
            </div>
        </div>
    </div>

    {{-- Kunjungan Bulan Ini --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Kunjungan Bulan Ini</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $kunjunganBulanIni }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-yellow-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-calendar-check text-yellow-500 text-base"></i>
            </div>
        </div>
    </div>

    {{-- Kebutuhan Mendesak --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Kebutuhan Mendesak</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $kebutuhanMendesak }}</p>
                <p class="text-xs text-gray-500 mt-1">Item stok kritis</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-red-500 text-base"></i>
            </div>
        </div>
    </div>

</div>

{{-- Aktivitas Terbaru --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

    {{-- Donasi Terbaru --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <i class="fa-solid fa-hand-holding-heart text-red-500 text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-sm">Donasi Terbaru</h3>
            </div>
            <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'donasi.index') }}"
               class="text-xs text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                Lihat semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($aktivitasDonasi as $donasi)
            <div class="flex items-center justify-between px-5 py-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user text-gray-400 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $donasi->nama_donatur }}</p>
                        <p class="text-xs text-gray-400">{{ $donasi->jenis }} · {{ $donasi->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @include('components.badge-donasi', ['status' => $donasi->status])
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                <i class="fa-solid fa-inbox text-3xl mb-2 text-gray-200"></i>
                <p class="text-sm">Belum ada donasi</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Kunjungan Terbaru --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-blue-500 text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-sm">Kunjungan Terbaru</h3>
            </div>
            <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'kunjungan.index') }}"
               class="text-xs text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                Lihat semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($aktivitasKunjungan as $kunjungan)
            <div class="flex items-center justify-between px-5 py-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user text-gray-400 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $kunjungan->nama_pengunjung }}</p>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($kunjungan->tgl_kunjungan)->format('d M Y') }} · {{ $kunjungan->jam }}
                        </p>
                    </div>
                </div>
                @include('components.badge-kunjungan', ['status' => $kunjungan->status])
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                <i class="fa-solid fa-inbox text-3xl mb-2 text-gray-200"></i>
                <p class="text-sm">Belum ada kunjungan</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Aksi Cepat --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h3 class="font-semibold text-gray-900 text-sm mb-4">Aksi Cepat</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.index') }}"
           class="flex flex-col items-center gap-2.5 p-4 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50 transition group text-center">
            <div class="w-10 h-10 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center transition">
                <i class="fa-solid fa-users text-blue-500 text-base"></i>
            </div>
            <span class="text-xs font-medium text-gray-700 group-hover:text-blue-700 transition">Warga Binaan</span>
        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'logistik.index') }}"
           class="flex flex-col items-center gap-2.5 p-4 rounded-xl border border-gray-100 hover:border-orange-200 hover:bg-orange-50 transition group text-center">
            <div class="w-10 h-10 rounded-xl bg-orange-50 group-hover:bg-orange-100 flex items-center justify-center transition">
                <i class="fa-solid fa-boxes-stacked text-orange-500 text-base"></i>
            </div>
            <span class="text-xs font-medium text-gray-700 group-hover:text-orange-700 transition">Logistik</span>
        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'donasi.index') }}"
           class="flex flex-col items-center gap-2.5 p-4 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50 transition group text-center">
            <div class="w-10 h-10 rounded-xl bg-red-50 group-hover:bg-red-100 flex items-center justify-center transition">
                <i class="fa-solid fa-hand-holding-heart text-red-500 text-base"></i>
            </div>
            <span class="text-xs font-medium text-gray-700 group-hover:text-red-700 transition">Donasi</span>
        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'keuangan.index') }}"
           class="flex flex-col items-center gap-2.5 p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition group text-center">
            <div class="w-10 h-10 rounded-xl bg-green-50 group-hover:bg-green-100 flex items-center justify-center transition">
                <i class="fa-solid fa-wallet text-green-500 text-base"></i>
            </div>
            <span class="text-xs font-medium text-gray-700 group-hover:text-green-700 transition">Keuangan</span>
        </a>

    </div>
</div>

@endsection
