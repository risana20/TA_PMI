@extends('layout.auth')

@section('title', 'Login — Griya PMI Surakarta')

@section('content')
<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="flex flex-col items-center mb-6">
            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center mb-3">
                <i class="fa-solid fa-heart text-white text-lg"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Griya PMI Surakarta</h1>
            <p class="text-sm text-gray-500 mt-1">Masuk ke akun Anda</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    placeholder="email@example.com">
            </div>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-red-600 hover:underline">Lupa password?</a>
                </div>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                        class="w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="••••••••">
                    <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="rounded">
                <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
            </div>
            <div class="items-center">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
            </div>
            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-4">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-red-600 font-semibold hover:underline">Daftar sekarang</a>
        </p>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script>
@endsection

