@extends('layout.public')

@section('title', 'Artikel & Kegiatan — Griya PMI Surakarta')

@section('content')

{{-- Header --}}
<section class="bg-[#E4000F] text-white pt-16 pb-32 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white/20 border border-white/30 rounded-full px-4 py-1.5 text-xs font-semibold uppercase tracking-wider mb-6">
            <i class="fa-regular fa-newspaper"></i> BERITA & KEGIATAN
        </div>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Artikel Kegiatan</h1>
        <p class="text-red-100 text-lg max-w-2xl mx-auto font-light">
            Ikuti perkembangan kegiatan, berita, dan pengumuman terbaru dari Griya PMI
        </p>
    </div>
</section>

{{-- Filter & Content --}}
<section class="pb-20 bg-[#F8F9FA] relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Search & Filter Container --}}
        <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-4 md:p-6 -mt-16 relative z-10 mb-12 border border-gray-100">
            <form method="GET" class="flex flex-col md:flex-row items-center gap-4">
                {{-- Search Bar --}}
                <div class="relative flex-1 w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel..."
                        class="pl-11 pr-4 py-3 bg-[#F8F9FA] border border-gray-100 rounded-xl text-sm w-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:bg-white transition-all">
                </div>
                
                {{-- Category Pills --}}
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <a href="{{ route('artikel.index', ['q' => request('q')]) }}" 
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ !request('kategori') ? 'bg-[#E4000F] text-white shadow-md shadow-red-500/20' : 'bg-white border border-gray-200 text-gray-600 hover:border-red-500 hover:text-red-600' }}">
                        Semua
                    </a>
                    @php
                        $fixedKategoris = ['Kegiatan', 'Berita', 'Pengumuman'];
                    @endphp
                    @foreach($fixedKategoris as $kat)
                    <a href="{{ route('artikel.index', ['kategori' => $kat, 'q' => request('q')]) }}" 
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request('kategori') === $kat ? 'bg-[#E4000F] text-white shadow-md shadow-red-500/20' : 'bg-white border border-gray-200 text-gray-600 hover:border-red-500 hover:text-red-600' }}">
                        {{ $kat }}
                    </a>
                    @endforeach
                </div>
            </form>
        </div>

        @if($artikels->count() > 0)
        
        {{-- Grid Artikel --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($artikels as $artikel)
            <a href="{{ route('artikel.show', $artikel->slug) }}" class="group flex flex-col bg-white rounded-[24px] overflow-hidden border border-gray-100 hover:shadow-xl hover:shadow-gray-200/50 hover:-translate-y-1 transition-all duration-300">
                
                {{-- Image Container --}}
                <div class="relative h-60 w-full overflow-hidden">
                    @if($artikel->gambar)
                        <img src="{{ Storage::url($artikel->gambar) }}" alt="{{ $artikel->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                            <i class="fa-regular fa-image text-gray-300 text-4xl"></i>
                        </div>
                    @endif
                    
                    {{-- Category Badge --}}
                    @if($artikel->kategori)
                    <div class="absolute top-4 left-4">
                        <span class="bg-white/95 backdrop-blur-sm text-gray-800 text-[10px] font-bold uppercase tracking-wider py-1.5 px-4 rounded-full shadow-sm">
                            {{ $artikel->kategori }}
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Content Container --}}
                <div class="p-6 md:p-8 flex flex-col flex-1">
                    {{-- Meta Info --}}
                    <div class="flex items-center gap-4 text-xs font-medium text-gray-400 mb-4">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-[#E4000F]"></i>
                            <span>{{ $artikel->tgl_terbit?->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-regular fa-clock text-[#E4000F]"></i>
                            <span>{{ max(1, ceil(str_word_count(strip_tags($artikel->konten)) / 200)) }} menit</span>
                        </div>
                    </div>

                    {{-- Title & Excerpt --}}
                    <h3 class="text-xl font-bold text-gray-900 leading-snug mb-3 group-hover:text-[#E4000F] transition-colors line-clamp-2">
                        {{ $artikel->judul }}
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6 line-clamp-3 flex-1">
                        {{ Str::limit(strip_tags($artikel->konten), 120) }}
                    </p>

                    {{-- Read More Link --}}
                    <div class="mt-auto pt-4 border-t border-gray-50 flex items-center gap-2 text-[#E4000F] font-bold text-sm">
                        Baca Selengkapnya
                        <i class="fa-solid fa-chevron-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $artikels->links() }}
        </div>

        @else
        <div class="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fa-regular fa-newspaper text-gray-300 text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Belum ada artikel</h3>
            <p class="text-gray-500 text-sm">Artikel dan kegiatan akan segera tersedia.</p>
        </div>
        @endif

    </div>
</section>

@endsection
