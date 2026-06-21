@extends('layout.public')

@section('title', 'Beranda — Griya PMI Surakarta')

@section('content')

{{-- Hero Section --}}
<section class="bg-[#E4000F] text-white overflow-hidden py-24 sm:py-16">
    {{-- Decorative Background Elements --}}
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.1),transparent_50%)]"></div>
    <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-xs font-semibold tracking-wider text-red-100 uppercase mb-2">
            <i class="fa-solid fa-heart text-red-300 animate-pulse"></i> Wujud Kepedulian Sosial
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-2 drop-shadow-sm leading-tight sm:leading-none">
            Griya PMI Surakarta
        </h1>
        <p class="text-red-100 text-base sm:text-xl mb-8 max-w-3xl mx-auto leading-relaxed font-light">
            Rumah perlindungan sosial terpadu yang didedikasikan untuk memulihkan martabat, merawat kesehatan, serta memberikan kasih sayang hangat bagi ODGJ dan lansia telantar.
        </p>
        <div class="flex flex-col sm:flex-row gap-3.5 justify-center pt-2">
            @auth
            <a href="{{ route('donasi.index') }}"
                class="bg-white text-red-700 font-semibold px-8 py-3.5 rounded-full hover:shadow-lg hover:shadow-black/10 hover:bg-red-50 active:scale-95 transition-all duration-300 flex items-center justify-center gap-2">
                <i class="fa-solid fa-heart"></i> Donasi Sekarang
            </a>
            @else
            <a href="{{ route('register') }}"
                class="bg-white text-red-700 font-semibold px-8 py-3.5 rounded-full hover:shadow-lg hover:shadow-black/10 hover:bg-red-50 active:scale-95 transition-all duration-300 flex items-center justify-center gap-2">
                <i class="fa-solid fa-heart"></i> Donasi Sekarang
            </a>
            @endauth
            <a href="{{ route('kebutuhan-mendesak') }}"
                class="border-2 border-white/80 text-white font-semibold px-8 py-3.5 rounded-full hover:bg-white hover:text-red-700 hover:shadow-lg active:scale-95 transition-all duration-300">
                Kebutuhan Mendesak
            </a>
        </div>
    </div>
</section>

{{-- Overlapping Stats Card --}}
<section class="relative z-10 -mt-10 sm:-mt-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-xl border border-gray-100 p-6 sm:p-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 gap-6 text-center">
            <div class="pb-6 sm:pb-0">
                <p class="text-4xl font-extrabold text-red-600 tracking-tight">{{ $totalWarga }}</p>
                <p class="text-sm font-semibold text-gray-500 mt-2 uppercase tracking-wider">Warga Binaan Aktif</p>
            </div>
            <div class="py-6 sm:py-0 sm:px-6">
                <p class="text-4xl font-extrabold text-red-600 tracking-tight">2</p>
                <p class="text-sm font-semibold text-gray-500 mt-2 uppercase tracking-wider">Program Pelayanan</p>
            </div>
            <div class="pt-6 sm:pt-0">
                <p class="text-4xl font-extrabold text-red-600 tracking-tight">{{ date('Y') - 2011 }}+</p>
                <p class="text-sm font-semibold text-gray-500 mt-2 uppercase tracking-wider">Tahun Beroperasi</p>
            </div>
        </div>
    </div>
</section>

{{-- New Section: Mengenal Lebih Dekat Griya PMI --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                Mengenal Lebih Dekat Griya PMI
            </h2>
            <p class="text-red-600 font-semibold uppercase tracking-wider text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-hand-holding-heart"></i> Harapan dan Perlindungan Terpadu
            </p>
            <div class="w-16 h-1 bg-red-500 mx-auto rounded-full mt-4"></div>
        </div>

        {{-- Tab Switcher Header --}}
        <div class="flex justify-start sm:justify-center border-b border-gray-200 mb-12 overflow-x-auto max-w-4xl mx-auto whitespace-nowrap scrollbar-none px-4">
            <button onclick="activateTab('griya-utama')" id="btn-griya-utama" class="tab-btn pb-4 px-6 text-sm sm:text-base font-semibold border-b-2 border-red-600 text-red-600 transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-house-chimney"></i> Griya PMI Surakarta
            </button>
            <button onclick="activateTab('griya-peduli')" id="btn-griya-peduli" class="tab-btn pb-4 px-6 text-sm sm:text-base font-medium border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-hand"></i> Griya PMI Peduli
            </button>
            <button onclick="activateTab('griya-bahagia')" id="btn-griya-bahagia" class="tab-btn pb-4 px-6 text-sm sm:text-base font-medium border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-person-cane"></i> Griya PMI Bahagia
            </button>
        </div>

        {{-- sesudah --}}
<div class="max-w-6xl mx-auto rounded-3xl p-6 sm:p-10 min-h-[400px]"
     style="background: linear-gradient(135deg, #fff 0%, #fef2f2 40%, #fff5f5 100%); border: 1px solid #fecaca; box-shadow: 0 4px 24px 0 rgba(228,0,15,0.07);">
            {{-- Panel 1: Griya PMI Surakarta --}}
            <div id="panel-griya-utama" class="tab-panel animate-fade-in-up">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Text Content -->
                    <div class="lg:col-span-7 space-y-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-full uppercase tracking-wider">
                            <i class="fa-solid fa-circle-info"></i> Gambaran Umum
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">
                            Permukiman Perlindungan Kemanusiaan Terpadu
                        </h3>
                        <p class="text-gray-600 text-base sm:text-lg leading-relaxed">
                            Griya PMI merupakan salah satu wujud nyata kepedulian PMI Surakarta terhadap permasalahan sosial dalam masyarakat, khususnya terkait keberadaan orang-orang telantar. Mulai beroperasi sejak <strong>Maret 2012</strong>, unit penampungan ini menjadi shelter hangat yang aman, terawat, dan memanusiakan warga binaan.
                        </p>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Secara umum, Griya PMI menampung orang-orang yang telantar, khususnya yang berada di wilayah Kota Surakarta. Guna mengefektifkan pelayanan kemanusiaan, kompleks Griya PMI ini terbagi menjadi dua unit penanganan khusus, yaitu <strong>Griya PMI Peduli</strong> dan <strong>Griya PMI Bahagia</strong>.
                        </p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="flex items-start gap-3 p-4 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                                <div class="p-3 bg-red-100 text-red-600 rounded-xl shrink-0 mt-0.5">
                                    <i class="fa-solid fa-calendar-check text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Beroperasi Sejak</p>
                                    <p class="font-extrabold text-gray-800 text-sm mt-0.5">Maret 2012</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                                <div class="p-3 bg-red-100 text-red-600 rounded-xl shrink-0 mt-0.5">
                                    <i class="fa-solid fa-map-location-dot text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Alamat & Lokasi</p>
                                    <p class="font-extrabold text-gray-800 text-xs mt-0.5 leading-relaxed">Jl. Sumbing Raya No. 6, Mojosongo, Jebres, Surakarta</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Image Content -->
                    <div class="lg:col-span-5">
                        <div class="relative group overflow-hidden rounded-2xl shadow-lg border-4 border-white transition-all duration-300 hover:shadow-xl">
                            <img src="{{ asset('assets/beranda/griyapmi.jpg') }}" alt="Griya PMI Surakarta" class="w-full h-64 sm:h-80 object-cover transform transition duration-700 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0 opacity-90 transition-opacity"></div>
                            <div class="absolute bottom-4 left-4 right-4 text-white">
                                <p class="text-xs font-semibold uppercase tracking-wider text-red-300">Griya PMI Surakarta</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Panel 2: Griya PMI Peduli --}}
            <div id="panel-griya-peduli" class="tab-panel hidden animate-fade-in-up">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Text Content -->
                    <div class="lg:col-span-7 space-y-6">
                        
                        <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">
                            Griya PMI Peduli
                        </h3>
                        <p class="text-gray-600 text-base sm:text-lg leading-relaxed">
                            PMI Kota Surakarta membuat sebuah program mulia yang didekasikan sepenuhnya untuk mengasuh gelandangan psikotik. Griya PMI Peduli merupakan tempat khusus yang digunakan untuk menampung orang-orang telantar dengan kondisi gangguan jiwa atau psikotik, baik muda maupun tua.
                        </p>
                        
                        
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-white rounded-2xl shadow-sm border border-gray-100 text-center relative overflow-hidden group hover:shadow-md transition">
                                <p class="text-3xl sm:text-4xl font-extrabold text-green-600 mb-1">843+</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Warga Ditampung</p>
                            </div>
                            <div class="p-4 bg-white rounded-2xl shadow-sm border border-gray-100 text-center relative overflow-hidden group hover:shadow-md transition">
                                <p class="text-3xl sm:text-4xl font-extrabold text-green-600 mb-1">120</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Warga Saat Ini</p>
                            </div>
                        </div>

                        <!-- Sejarah Card -->
                        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5 space-y-2">
                            <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-scroll text-green-500"></i> Sejarah Griya PMI Peduli
                            </h4>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Terinspirasi dari Jami'in, seorang tukang batu di Jombang yang dengan jiwa kemanusiaannya rela menampung dan mengurusi lebih dari 200 penderita gangguan jiwa. PMI Surakarta merespons teladan luhur tersebut dengan niat tulus untuk ikut meringankan penderitaan sesama lewat unit penanganan ini.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Image Content -->
                    <div class="lg:col-span-5">
                        <div class="relative group overflow-hidden rounded-2xl shadow-lg border-4 border-white transition-all duration-300 hover:shadow-xl">
                            <img src="{{ asset('assets/beranda/pmipeduli.jpg') }}" alt="Griya PMI Peduli" class="w-full h-64 sm:h-80 object-cover transform transition duration-700 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0 opacity-90 transition-opacity"></div>
                            <div class="absolute bottom-4 left-4 right-4 text-white">
                                <p class="text-xs font-semibold uppercase tracking-wider text-green-300">Griya PMI Peduli</p>
                                <p class="font-bold text-sm">Layanan ODGJ Mojosongo, Surakarta</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Panel 3: Griya PMI Bahagia --}}
            <div id="panel-griya-bahagia" class="tab-panel hidden animate-fade-in-up">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Text Content -->
                    <div class="lg:col-span-7 space-y-6">
                        <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">
                            Griya PMI Bahagia
                        </h3>
                        <p class="text-gray-600 text-base sm:text-lg leading-relaxed">
                            Pada pelaksanaan penjemputan ODGJ di jalan atau saat operasi penertiban bersama Satpol PP, kerap dijumpai lansia telantar yang kondisinya sehat mental (non-ODGJ). Merespons hal tersebut, pada tahun <strong>2015</strong> PMI Kota Surakarta mengembangkan sayap pelayanannya dengan mendirikan unit khusus bernama <strong>GRIYA BAHAGIA</strong>.
                        </p>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Di unit ini, para lansia yang telantar diberikan perawatan kebersihan, kesehatan berkelanjutan, asupan bergizi, serta kehangatan keluarga agar mereka dapat melewati masa tua dengan penuh martabat, keceriaan, dan cinta kasih.
                        </p>
                        
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-white rounded-2xl shadow-sm border border-gray-100 text-center relative overflow-hidden group hover:shadow-md transition">
                                <p class="text-3xl sm:text-4xl font-extrabold text-amber-600 mb-1">180+</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Lansia Ditampung</p>
                            </div>
                            <div class="p-4 bg-white rounded-2xl shadow-sm border border-gray-100 text-center relative overflow-hidden group hover:shadow-md transition">
                                <p class="text-3xl sm:text-4xl font-extrabold text-amber-600 mb-1">24</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Lansia Saat Ini</p>
                            </div>
                        </div>

                        <div class="p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-2xl">
                            <p class="text-sm text-amber-800 leading-relaxed italic">
                                "Sampai saat ini, Griya PMI Bahagia telah mengayomi lebih dari 180 orang lansia telantar, memberikan hunian yang layak dan bahagia."
                            </p>
                        </div>
                    </div>
                    
                    <!-- Image Content -->
                    <div class="lg:col-span-5">
                        <div class="relative group overflow-hidden rounded-2xl shadow-lg border-4 border-white transition-all duration-300 hover:shadow-xl">
                            <img src="{{ asset('assets/beranda/pmilansia.jpg') }}" alt="Griya PMI Bahagia" class="w-full h-64 sm:h-80 object-cover transform transition duration-700 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0 opacity-90 transition-opacity"></div>
                            <div class="absolute bottom-4 left-4 right-4 text-white">
                                <p class="text-xs font-semibold uppercase tracking-wider text-amber-300">Griya PMI Bahagia</p>
                                <p class="font-bold text-sm">Shelter Lanjut Usia, Mojosongo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Kebutuhan Mendesak --}}
@if($kebutuhanMendesak->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-full uppercase tracking-widest">
                <i class="fa-solid fa-box text-[10px]"></i> Bantuan Dibutuhkan
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                Kebutuhan Mendesak
            </h2>
            <p class="text-gray-500 text-center max-w-2xl mx-auto text-sm leading-relaxed">
                Berikut adalah daftar kebutuhan yang sedang mendesak di Griya PMI. Bantuan Anda sangat berarti bagi warga binaan kami.
            </p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($kebutuhanMendesak as $item)
            @php
                $isSangatMendesak = $item->status === 'Sangat Mendesak';
                $badgeClass = $isSangatMendesak 
                    ? 'bg-red-50 text-red-600 border border-red-100' 
                    : 'bg-orange-50 text-orange-600 border border-orange-100';
                $kategori = $item->itemLogistik->jenisLogistik->nama_jenis_logistik ?? '-';
            @endphp
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between">
                <div>
                    {{-- Icon & Status Badge --}}
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-red-50/50 border border-red-100 flex items-center justify-center">
                            @if($kategori === 'Makanan')
                                <i class="fa-solid fa-utensils text-red-500 text-xl"></i>
                            @elseif($kategori === 'Obat')
                                <i class="fa-solid fa-pills text-red-500 text-xl"></i>
                            @else
                                <i class="fa-solid fa-box text-red-500 text-xl"></i>
                            @endif
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                            {{ $item->status }}
                        </span>
                    </div>

                    {{-- Name & Category --}}
                    <h3 class="font-extrabold text-gray-900 text-lg sm:text-xl tracking-tight mb-0.5">{{ $item->itemLogistik->nama_item }}</h3>
                    <p class="text-sm font-medium text-gray-400 mb-5">{{ $kategori }}</p>

                    {{-- Stock Info --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-[10px] uppercase font-bold tracking-wider text-gray-400 mb-1">Stok Tersisa</p>
                            <p class="text-2xl font-black {{ $isSangatMendesak ? 'text-red-600' : 'text-orange-500' }} leading-none">{{ $item->jumlah_saat_ini }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 font-bold">{{ $item->itemLogistik->satuan }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold tracking-wider text-gray-400 mb-1">Kebutuhan</p>
                            <p class="text-2xl font-black text-gray-800 leading-none">{{ $item->jumlah_minimum }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 font-bold">{{ $item->itemLogistik->satuan }}</p>
                        </div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                @php
                    $persen = $item->jumlah_minimum > 0 ? min(100, round(($item->jumlah_saat_ini / $item->jumlah_minimum) * 100)) : 100;
                    $barBg = $isSangatMendesak ? 'bg-red-500' : 'bg-orange-500';
                @endphp
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="{{ $barBg }} h-2 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-12">
            <a href="{{ route('kebutuhan-mendesak') }}" class="inline-flex items-center gap-2 bg-white border border-red-200 hover:border-red-300 text-red-600 font-semibold px-8 py-3.5 rounded-full hover:shadow-sm active:scale-95 transition-all duration-300">
                Lihat Semua Kebutuhan <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
        </div>
    </div>
</section>
@endif

@if($donaturTerverifikasi->count() > 0)
{{-- Section Donatur --}}
<section class="py-20 bg-gray-50/50 border-t border-b border-gray-100 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Donatur</h2>
        <p class="text-sm text-gray-500 mt-2 font-medium">Terima kasih kepada para donatur yang telah berkontribusi</p>
        <div class="w-12 h-1 bg-red-500 mx-auto rounded-full mt-4"></div>
    </div>

    {{-- Marquee Container --}}
<div class="marquee-wrapper relative flex w-full overflow-hidden py-2 select-none px-8 sm:px-16">
    {{-- Fade gradients — lebih lebar agar kotak muncul/menghilang halus --}}
    <div class="absolute inset-y-0 left-0 w-24 sm:w-48 bg-gradient-to-r from-gray-50/50 to-transparent z-10 pointer-events-none"></div>
    <div class="absolute inset-y-0 right-0 w-24 sm:w-48 bg-gradient-to-l from-gray-50/50 to-transparent z-10 pointer-events-none"></div>
        {{-- Row 1 --}}
        <div class="flex gap-6 pr-6 shrink-0 animate-marquee">
            @foreach($donaturTerverifikasi as $donasi)
            <div class="rounded-2xl p-5 hover:shadow-md hover:border-red-500/30 hover:-translate-y-0.5 transition-all duration-300 w-72 md:w-80 shrink-0 flex flex-col justify-between relative overflow-hidden group"
                 style="background: linear-gradient(135deg, #fff 0%, #fef2f2 40%, #fff5f5 100%); border: 1px solid #fecaca; box-shadow: 0 4px 24px 0 rgba(228,0,15,0.07);">
                {{-- Accent line on hover --}}
                <div class="absolute top-0 left-0 w-full h-[3px] bg-red-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        @if($donasi->jenis === 'Uang')
                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                            <i class="fa-solid fa-wallet text-[9px]"></i> Donasi Uang
                        </span>
                        @elseif($donasi->jenis === 'Makanan')
                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                            <i class="fa-solid fa-utensils text-[9px]"></i> Bahan Makanan
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                            <i class="fa-solid fa-box text-[9px]"></i> Donasi Barang
                        </span>
                        @endif
                    </div>
                    <h4 class="font-extrabold text-slate-800 text-sm md:text-base truncate mt-1">{{ $donasi->nama_donatur }}</h4>
                </div>
                <div class="mt-4 pt-3 border-t border-red-100/50 flex items-center justify-between">
                    <p class="text-xs text-slate-400 flex items-center gap-1.5 font-medium truncate">
                        <i class="fa-solid fa-location-dot text-slate-400/80 text-[11px]"></i>
                        <span class="truncate">{{ $donasi->user?->address ?: 'Surakarta' }}</span>
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Row 2 (Duplicate for loop) --}}
        <div class="flex gap-6 pr-6 shrink-0 animate-marquee" aria-hidden="true">
            @foreach($donaturTerverifikasi as $donasi)
            <div class="rounded-2xl p-5 hover:shadow-md hover:border-red-500/30 hover:-translate-y-0.5 transition-all duration-300 w-72 md:w-80 shrink-0 flex flex-col justify-between relative overflow-hidden group"
                 style="background: linear-gradient(135deg, #fff 0%, #fef2f2 40%, #fff5f5 100%); border: 1px solid #fecaca; box-shadow: 0 4px 24px 0 rgba(228,0,15,0.07);">
                {{-- Accent line on hover --}}
                <div class="absolute top-0 left-0 w-full h-[3px] bg-red-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        @if($donasi->jenis === 'Uang')
                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                            <i class="fa-solid fa-wallet text-[9px]"></i> Donasi Uang
                        </span>
                        @elseif($donasi->jenis === 'Makanan')
                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                            <i class="fa-solid fa-utensils text-[9px]"></i> Bahan Makanan
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                            <i class="fa-solid fa-box text-[9px]"></i> Donasi Barang
                        </span>
                        @endif
                    </div>
                    <h4 class="font-extrabold text-slate-800 text-sm md:text-base truncate mt-1">{{ $donasi->nama_donatur }}</h4>
                </div>
                <div class="mt-4 pt-3 border-t border-red-100/50 flex items-center justify-between">
                    <p class="text-xs text-slate-400 flex items-center gap-1.5 font-medium truncate">
                        <i class="fa-solid fa-location-dot text-slate-400/80 text-[11px]"></i>
                        <span class="truncate">{{ $donasi->user?->address ?: 'Surakarta' }}</span>
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Artikel Terbaru --}}
@if($artikelTerbaru->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Artikel & Kegiatan</h2>
                <p class="text-sm text-gray-500 mt-1.5">Ikuti kabar terbaru dan momen berharga bersama warga binaan</p>
            </div>
            <a href="{{ route('artikel.index') }}" class="text-sm font-bold text-red-600 hover:text-red-700 hover:underline flex items-center gap-1 transition">
                Lihat Semua Artikel <i class="fa-solid fa-circle-arrow-right"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach($artikelTerbaru as $artikel)
            <a href="{{ route('artikel.show', $artikel->slug) }}" class="group bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md hover:border-gray-200 transition-all duration-300 flex flex-col h-full">
                <div class="relative overflow-hidden aspect-video">
                    @if($artikel->gambar)
                        <img src="{{ Storage::url($artikel->gambar) }}" alt="{{ $artikel->judul }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                            <i class="fa-solid fa-image text-gray-300 text-3xl"></i>
                        </div>
                    @endif
                </div>
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div class="space-y-1">
                        @if($artikel->kategori)
                        <span class="text-[10px] font-bold text-red-600 uppercase tracking-wider">{{ $artikel->kategori }}</span>
                        @endif
                        <h3 class="font-bold text-gray-800 leading-snug group-hover:text-red-600 transition line-clamp-2">{{ $artikel->judul }}</h3>
                    </div>
                    <p class="text-xs text-gray-400 flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar-days text-[11px]"></i> {{ $artikel->tgl_terbit?->format('d M Y') }}
                    </p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@push('styles')
<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Marquee Auto-scrolling Styles */
@keyframes marquee {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-100%);
    }
}
.animate-marquee {
    animation: marquee 35s linear infinite;
}
.marquee-wrapper:hover .animate-marquee {
    animation-play-state: paused;
}
</style>
@endpush

@push('scripts')
<script>
function activateTab(tabId) {
    // Hide all panels
    document.querySelectorAll('.tab-panel').forEach(p => {
        p.classList.add('hidden');
    });

    // Remove active styles from all buttons
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-red-600', 'text-red-600');
        b.classList.add('border-transparent', 'text-gray-400', 'hover:text-gray-600');
        b.classList.remove('font-semibold');
        b.classList.add('font-medium');
    });

    // Show selected panel with animation reset
    const selectedPanel = document.getElementById('panel-' + tabId);
    if (selectedPanel) {
        selectedPanel.classList.remove('hidden');
        selectedPanel.style.animation = 'none';
        selectedPanel.offsetHeight; /* trigger reflow */
        selectedPanel.style.animation = null; 
    }

    // Add active styles to clicked button
    const activeBtn = document.getElementById('btn-' + tabId);
    if (activeBtn) {
        activeBtn.classList.add('border-red-600', 'text-red-600', 'font-semibold');
        activeBtn.classList.remove('border-transparent', 'text-gray-400', 'hover:text-gray-600', 'font-medium');
    }
}
</script>
@endpush

@endsection
