@extends('layout.public')

@section('title', 'Kebutuhan Mendesak — Griya PMI Surakarta')

@section('content')

{{-- Hero Header --}}
<section class="bg-gradient-to-br from-red-600 to-red-700 text-white pt-14 pb-24">
    <div class="max-w-4xl mx-auto px-4 text-center">

        <div class="inline-flex items-center gap-2 bg-white/20 text-white text-xs font-semibold px-4 py-1.5 rounded-full mb-5 uppercase tracking-widest">
            <i class="fa-solid fa-hand-holding-heart"></i>
            Bantuan Dibutuhkan
        </div>

        <h1 class="text-4xl font-bold mb-4">Kebutuhan Mendesak</h1>
        <p class="text-red-100 text-base max-w-lg mx-auto leading-relaxed">
            Berikut adalah daftar kebutuhan yang sedang mendesak di Griya PMI. Bantuan Anda sangat berarti.
        </p>
    </div>
</section>

<div class="bg-gray-50 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 -mt-12 relative z-10 mb-10">

            <div class="bg-white rounded-2xl shadow-md p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 leading-none">{{ $totalSangatMendesak }}</p>
                    <p class="text-sm text-gray-500 mt-1">Sangat Mendesak</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-box text-orange-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 leading-none">{{ $totalMendesak }}</p>
                    <p class="text-sm text-gray-500 mt-1">Mendesak</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-heart text-red-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 leading-none">{{ $items->total() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Total Kebutuhan</p>
                </div>
            </div>

        </div>

        {{-- Filter --}}
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <span class="text-sm text-gray-500 flex items-center gap-1.5">
                <i class="fa-solid fa-filter text-xs"></i> Filter:
            </span>
            <a href="{{ route('kebutuhan-mendesak') }}"
               class="text-sm px-4 py-1.5 rounded-full font-medium transition
                   {{ !request('kategori') ? 'bg-red-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-red-300' }}">
                Semua
            </a>
            <a href="{{ route('kebutuhan-mendesak', ['kategori' => 'Makanan']) }}"
               class="text-sm px-4 py-1.5 rounded-full transition
                   {{ request('kategori') === 'Makanan' ? 'bg-red-600 text-white font-medium' : 'bg-white border border-gray-200 text-gray-600 hover:border-red-300' }}">
                Makanan
            </a>
            <a href="{{ route('kebutuhan-mendesak', ['kategori' => 'Barang']) }}"
               class="text-sm px-4 py-1.5 rounded-full transition
                   {{ request('kategori') === 'Barang' ? 'bg-red-600 text-white font-medium' : 'bg-white border border-gray-200 text-gray-600 hover:border-red-300' }}">
                Barang
            </a>
        </div>

        @if($items->count() > 0)

        {{-- Item Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @foreach($items as $item)
            @php
                $persen     = $item->jumlah_minimum > 0
                                ? min(100, round(($item->jumlah_saat_ini / $item->jumlah_minimum) * 100))
                                : 100;
                $isSangatMendesak = $item->status === 'Sangat Mendesak';
                $barColor   = $isSangatMendesak ? 'bg-red-500' : 'bg-orange-400';
                $badgeBg    = $isSangatMendesak ? 'bg-red-600' : 'bg-orange-500';
                $kategori   = $item->itemLogistik->jenisLogistik->nama_jenis_logistik ?? '-';
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-4 hover:shadow-md transition">

                {{-- Icon + Badge --}}
                <div class="flex items-start justify-between mb-3">
                    @if($kategori === 'Makanan')
                        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
                            <i class="fa-solid fa-utensils text-red-400"></i>
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
                            <i class="fa-solid fa-cube text-red-400"></i>
                        </div>
                    @endif
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full text-white leading-5 {{ $badgeBg }}">
                        {{ $item->status }}
                    </span>
                </div>

                {{-- Name & Category --}}
                <h3 class="font-bold text-gray-900 text-base mb-0.5">{{ $item->itemLogistik->nama_item }}</h3>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-3">{{ $kategori }}</p>

                {{-- Stock Info --}}
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-[9px] font-semibold uppercase tracking-widest text-gray-400 mb-0.5">Stok Tersisa</p>
                        <p class="font-bold text-gray-900 text-xl leading-none">{{ $item->jumlah_saat_ini }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $item->itemLogistik->satuan }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-semibold uppercase tracking-widest text-gray-400 mb-0.5">Kebutuhan</p>
                        <p class="font-bold text-gray-900 text-xl leading-none">{{ $item->jumlah_minimum }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $item->itemLogistik->satuan }}</p>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $persen }}%"></div>
                </div>
                <p class="text-[9px] uppercase tracking-wide text-gray-400 mt-1">{{ $persen }}% Terpenuhi</p>

            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center mb-4">
            {{ $items->links() }}
        </div>

        @else
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-circle-check text-green-500 text-2xl"></i>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Stok Terpenuhi</h3>
            <p class="text-gray-500 text-sm">Tidak ada kebutuhan mendesak saat ini.</p>
        </div>
        @endif

    </div>

    {{-- CTA Donasi --}}
    <div class="max-w-2xl mx-auto px-4 mt-12">
        <div class="bg-red-50 rounded-2xl px-8 py-10 text-center">

            <h2 class="text-2xl font-bold text-gray-900 mb-2">Ingin Membantu?</h2>
            <p class="text-gray-500 text-sm leading-relaxed mb-8">
                Pilih jenis donasi yang ingin Anda berikan. Setiap bantuan sangat berarti bagi warga binaan Griya PMI.
            </p>

            {{-- Pilihan Donasi --}}
            <div class="grid grid-cols-3 gap-3 mb-8">
                @auth
                    <a href="{{ route('donasi.index') }}"
                       class="bg-white rounded-xl p-4 flex flex-col items-center gap-2 hover:shadow-md transition border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fa-solid fa-money-bill-wave text-red-600"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">Donasi Uang</span>
                        <span class="text-[10px] text-gray-400">Transfer / Tunai</span>
                    </a>
                    <a href="{{ route('donasi.index') }}"
                       class="bg-white rounded-xl p-4 flex flex-col items-center gap-2 hover:shadow-md transition border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fa-solid fa-box-open text-red-600"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">Donasi Barang</span>
                        <span class="text-[10px] text-gray-400">Kebutuhan harian</span>
                    </a>
                    <a href="{{ route('donasi.index') }}"
                       class="bg-white rounded-xl p-4 flex flex-col items-center gap-2 hover:shadow-md transition border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fa-solid fa-utensils text-red-600"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">Donasi Makanan</span>
                        <span class="text-[10px] text-gray-400">Bahan makanan</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="bg-white rounded-xl p-4 flex flex-col items-center gap-2 hover:shadow-md transition border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fa-solid fa-money-bill-wave text-red-600"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">Donasi Uang</span>
                        <span class="text-[10px] text-gray-400">Transfer / Tunai</span>
                    </a>
                    <a href="{{ route('login') }}"
                       class="bg-white rounded-xl p-4 flex flex-col items-center gap-2 hover:shadow-md transition border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fa-solid fa-box-open text-red-600"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">Donasi Barang</span>
                        <span class="text-[10px] text-gray-400">Kebutuhan harian</span>
                    </a>
                    <a href="{{ route('login') }}"
                       class="bg-white rounded-xl p-4 flex flex-col items-center gap-2 hover:shadow-md transition border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fa-solid fa-utensils text-red-600"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">Donasi Makanan</span>
                        <span class="text-[10px] text-gray-400">Bahan makanan</span>
                    </a>
                @endauth
            </div>

            {{-- CTA Button --}}
            @auth
                <a href="{{ route('donasi.index') }}"
                   class="inline-flex items-center gap-2 bg-red-600 text-white font-semibold px-8 py-3 rounded-full hover:bg-red-700 transition">
                    <i class="fa-solid fa-heart"></i> Donasi Sekarang
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 bg-red-600 text-white font-semibold px-8 py-3 rounded-full hover:bg-red-700 transition">
                    <i class="fa-solid fa-heart"></i> Masuk untuk Berdonasi
                </a>
            @endauth

        </div>
    </div>

</div>

@endsection
