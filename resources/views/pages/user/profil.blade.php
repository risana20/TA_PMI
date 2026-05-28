@extends('layout.public')

@section('title', 'Profil Saya — Griya PMI Surakarta')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Profil Saya</h1>

    {{-- Informasi Akun Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-semibold text-gray-900">Informasi Akun</h3>
            <button id="edit-info-btn" type="button" class="flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 font-semibold focus:outline-none transition">
                <i class="fa-solid fa-pen-to-square"></i> Edit Profil
            </button>
        </div>

        {{-- Read-only Profile View --}}
        <div id="profile-view-section" class="space-y-4">
            <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-50">
                <span class="text-sm font-medium text-gray-500">Nama Lengkap</span>
                <span class="text-sm text-gray-900 col-span-2 font-semibold">{{ $user->name }}</span>
            </div>
            <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-50">
                <span class="text-sm font-medium text-gray-500">No. HP</span>
                <span class="text-sm text-gray-900 col-span-2">{{ $user->phone ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-50">
                <span class="text-sm font-medium text-gray-500">Email</span>
                <span class="text-sm text-gray-900 col-span-2">{{ $user->email }}</span>
            </div>
            <div class="grid grid-cols-3 gap-4 py-2">
                <span class="text-sm font-medium text-gray-500">Alamat</span>
                <span class="text-sm text-gray-900 col-span-2 leading-relaxed">{{ $user->address ?? '-' }}</span>
            </div>
        </div>

        {{-- Edit Form (hidden by default) --}}
        <form id="profile-edit-form" method="POST" action="{{ route('profil.update') }}" class="space-y-4 hidden">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ $user->name }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" value="{{ $user->email }}" disabled
                    class="w-full border border-gray-100 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                <input type="text" name="phone" value="{{ $user->phone }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="address" rows="2"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none">{{ $user->address }}</textarea>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-5 py-2 text-sm transition">
                    Simpan Perubahan
                </button>
                <button id="cancel-edit-btn" type="button" class="border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-lg px-5 py-2 text-sm font-semibold transition">
                    Batal
                </button>
            </div>
        </form>
    </div>

    {{-- Ubah Password Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        {{-- Button to show password form (visible by default) --}}
        <div id="password-btn-section" class="block">
            <button id="show-password-form-btn" type="button"
                class="w-full flex items-center justify-center gap-2 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl py-3 text-sm font-semibold transition">
                <i class="fa-solid fa-key text-gray-400"></i> Ganti Password
            </button>
        </div>

        {{-- Password Form (hidden by default) --}}
        <div id="password-form-section" class="hidden">
            <h3 class="font-semibold text-gray-900 mb-4">Ubah Password</h3>
            <form method="POST" action="{{ route('profil.change-password') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                    <input type="password" name="current_password" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-5 py-2 text-sm transition">
                        Ubah Password
                    </button>
                    <button id="cancel-password-btn" type="button" class="border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-lg px-5 py-2 text-sm font-semibold transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    // Elements for Profile Edit
    const editInfoBtn = document.getElementById('edit-info-btn');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const profileViewSection = document.getElementById('profile-view-section');
    const profileEditForm = document.getElementById('profile-edit-form');

    if (editInfoBtn && cancelEditBtn && profileViewSection && profileEditForm) {
        editInfoBtn.addEventListener('click', function() {
            profileViewSection.classList.add('hidden');
            profileEditForm.classList.remove('hidden');
            editInfoBtn.classList.add('hidden');
        });

        cancelEditBtn.addEventListener('click', function() {
            profileViewSection.classList.remove('hidden');
            profileEditForm.classList.add('hidden');
            editInfoBtn.classList.remove('hidden');
        });
    }

    // Elements for Password Change
    const showPasswordFormBtn = document.getElementById('show-password-form-btn');
    const cancelPasswordBtn = document.getElementById('cancel-password-btn');
    const passwordBtnSection = document.getElementById('password-btn-section');
    const passwordFormSection = document.getElementById('password-form-section');

    if (showPasswordFormBtn && cancelPasswordBtn && passwordBtnSection && passwordFormSection) {
        showPasswordFormBtn.addEventListener('click', function() {
            passwordBtnSection.classList.add('hidden');
            passwordFormSection.classList.remove('hidden');
        });

        cancelPasswordBtn.addEventListener('click', function() {
            passwordBtnSection.classList.remove('hidden');
            passwordFormSection.classList.add('hidden');
        });
    }

    // Auto-expand forms if there are validation errors on page load
    @if($errors->has('current_password') || $errors->has('password'))
        if (passwordBtnSection && passwordFormSection) {
            passwordBtnSection.classList.add('hidden');
            passwordFormSection.classList.remove('hidden');
        }
    @endif

    @if($errors->any() && !$errors->has('current_password') && !$errors->has('password'))
        if (profileViewSection && profileEditForm && editInfoBtn) {
            profileViewSection.classList.add('hidden');
            profileEditForm.classList.remove('hidden');
            editInfoBtn.classList.add('hidden');
        }
    @endif
})();
</script>
@endsection
