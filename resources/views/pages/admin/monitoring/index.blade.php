@extends('layout.app')

@section('title', 'Monitoring Kesehatan')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Monitoring Kesehatan</h1>
        <p class="text-sm text-gray-500 mt-0.5">Pencatatan kesehatan warga binaan</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.index', array_merge(request()->query(), ['export' => 'pdf'])) }}"
            class="border border-red-600 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm flex items-center gap-2 transition flex-1 sm:flex-none justify-center whitespace-nowrap">
            <i class="fa-solid fa-file-pdf"></i> Ekspor PDF
        </a>
        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.index', array_merge(request()->query(), ['export' => 'excel'])) }}"
            class="border border-red-600 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm flex items-center gap-2 transition flex-1 sm:flex-none justify-center whitespace-nowrap">
            <i class="fa-solid fa-file-excel"></i> Ekspor Excel
        </a>
    </div>
</div>

{{-- Tab Pill --}}
<div class="bg-gray-100 p-1 rounded-xl flex flex-col sm:inline-flex sm:flex-row gap-1 mb-4 w-full sm:w-auto">
    <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.index', array_merge(request()->except('tab', 'page'), ['tab' => 'ODGJ'])) }}"
        class="px-5 py-2 rounded-lg text-sm font-medium transition text-center
               {{ $tab === 'ODGJ' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
        Griya PMI Peduli (ODGJ)
    </a>
    <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.index', array_merge(request()->except('tab', 'page'), ['tab' => 'Lansia'])) }}"
        class="px-5 py-2 rounded-lg text-sm font-medium transition text-center
               {{ $tab === 'Lansia' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
        Griya PMI Bahagia (Lansia)
    </a>
</div>

{{-- Search --}}
<div class="mb-4">
    <form method="GET" class="flex items-center gap-3 flex-1 flex-wrap" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.index') }}" id="filterForm">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="relative w-full sm:w-auto">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input 
                type="text"
                name="search"
                id="searchInput"
                value="{{ request('search') }}"
                placeholder="Cari Nama..."
                class="pl-9 pr-4 py-2 border border-gray-200 rounded-full text-sm w-full sm:w-56 focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Foto</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Umur</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($wargaBinaans as $warga)
                <tr class="hover:bg-gray-50">
                    {{-- Foto --}}
                    <td class="px-4 py-4">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($warga->foto)
                                <img src="{{ Storage::url($warga->foto) }}" alt="{{ $warga->nama }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-user text-gray-400 text-sm"></i>
                            @endif
                        </div>
                    </td>
                    {{-- Nama --}}
                    <td class="px-4 py-4">
                        <p class="font-semibold text-gray-900">{{ $warga->nama }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $warga->nik }}</p>
                    </td>
                    {{-- Umur --}}
                    <td class="px-4 py-4 text-gray-600">
                        {{ $warga->umur }} tahun
                    </td>
                    {{-- Aksi --}}
                    <td class="px-4 py-4">
                        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.show', $warga) }}"
                            class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full px-4 py-1.5 text-xs font-semibold transition whitespace-nowrap">
                            <i class="fa-solid fa-eye text-xs"></i> Detail Monitoring
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-12 text-center">
                        <i class="fa-solid fa-heart-pulse text-4xl text-gray-200 mb-3 block"></i>
                        <p class="text-gray-400 text-sm">Belum ada data warga binaan {{ $tab }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $wargaBinaans->links() }}</div>
</div>
<script>
let timer;

document.getElementById('searchInput').addEventListener('keyup', function () {

    clearTimeout(timer);

    timer = setTimeout(function () {
        document.getElementById('filterForm').submit();
    }, 500); // delay 500ms
});

</script>

@endsection
