@extends('layout.auth')

@section('title', 'Verifikasi Email — Griya PMI Surakarta')

@section('content')
<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="flex flex-col items-center mb-6">
            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center mb-3">
                <i class="fa-solid fa-envelope text-white text-lg"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Verifikasi Email Anda</h1>
            <p class="text-sm text-gray-500 text-center mt-2 leading-relaxed">
                Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan ke email Anda.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4 text-center">
                Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat registrasi.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
            @csrf
            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('beranda') }}" class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Beranda
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-red-600 font-semibold hover:underline bg-transparent border-none cursor-pointer">
                    Keluar / Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
