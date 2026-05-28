@extends('layout.public')

@section('title', $artikel->judul . ' — Griya PMI Surakarta')

@section('content')

<section class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
            <a href="{{ route('beranda') }}" class="hover:text-red-600">Beranda</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <a href="{{ route('artikel.index') }}" class="hover:text-red-600">Artikel</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-600 line-clamp-1">{{ $artikel->judul }}</span>
        </nav>

        {{-- Artikel --}}
        <article class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">

            {{-- Gambar --}}
            @if($artikel->gambar)
            <img src="{{ Storage::url($artikel->gambar) }}" alt="{{ $artikel->judul }}"
                class="w-full h-72 object-cover">
            @endif

            <div class="p-6 sm:p-8">

                {{-- Meta --}}
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    @if($artikel->kategori)
                    <span class="text-xs font-semibold text-red-600 bg-red-50 px-3 py-1 rounded-full uppercase">
                        {{ $artikel->kategori }}
                    </span>
                    @endif
                    <span class="text-xs text-gray-400">
                        <i class="fa-solid fa-calendar mr-1"></i>
                        {{ $artikel->tgl_terbit?->format('d M Y') }}
                    </span>
                    <span class="text-xs text-gray-400">
                        <i class="fa-solid fa-user mr-1"></i>
                        {{ $artikel->penulis->name ?? 'Admin' }}
                    </span>
                </div>

                {{-- Judul --}}
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">{{ $artikel->judul }}</h1>

                {{-- Divider --}}
                <hr class="border-gray-100 mb-6">

                {{-- Konten --}}
                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                    {!! $artikel->konten !!}
                </div>

            </div>
        </article>

        {{-- Artikel Terkait --}}
        @if($artikelTerkait->count() > 0)
        <div class="mt-10">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Artikel Lainnya</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($artikelTerkait as $terkait)
                <a href="{{ route('artikel.show', $terkait->slug) }}"
                    class="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition flex gap-4 p-4">
                    @if($terkait->gambar)
                        <img src="{{ Storage::url($terkait->gambar) }}" alt="{{ $terkait->judul }}"
                            class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                    @else
                        <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-image text-gray-300"></i>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        @if($terkait->kategori)
                        <span class="text-xs text-red-600 font-semibold uppercase">{{ $terkait->kategori }}</span>
                        @endif
                        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 mt-0.5">{{ $terkait->judul }}</h3>
                        <p class="text-xs text-gray-400 mt-1">{{ $terkait->tgl_terbit?->format('d M Y') }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Back --}}
        <div class="mt-8 text-center">
            <a href="{{ route('artikel.index') }}"
                class="inline-flex items-center gap-2 text-sm text-red-600 hover:underline font-medium">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Artikel
            </a>
        </div>

    </div>
</section>

@endsection
