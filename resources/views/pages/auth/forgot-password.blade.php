@extends('layout.auth')

@section('title', 'Lupa Password — Griya PMI Surakarta')

@section('content')
<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="flex flex-col items-center mb-6">
            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center mb-3">
                <i class="fa-solid fa-key text-white text-lg"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Lupa Password</h1>
            <p class="text-sm text-gray-500 mt-1 text-center">Masukkan email Anda untuk menerima link reset password</p>
        </div>

        @if(session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    placeholder="email@example.com">
            </div>
            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                Kirim Link Reset Password
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-4">
            Kembali ke
            <a href="{{ route('login') }}" class="text-red-600 font-semibold hover:underline">Login</a>
        </p>
    </div>
</div>
@endsection
