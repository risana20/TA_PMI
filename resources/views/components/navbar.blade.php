<header class="bg-white shadow-sm px-6 py-3 flex items-center justify-between flex-shrink-0 relative z-20">

    {{-- Left: Hamburger (mobile) + Home button --}}
    <div class="flex items-center gap-2">
        {{-- Hamburger: hanya tampil di mobile --}}
        <button onclick="toggleSidebar()"
            class="md:hidden text-gray-500 hover:text-gray-700 w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        {{-- Tombol Beranda --}}
        <a href="{{ route('beranda') }}" target="_blank"
            class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
            <i class="fa-solid fa-house text-xs"></i>
            <span class="hidden sm:inline">Beranda</span>
        </a>
    </div>

    {{-- Right: Dropdown Profil --}}
    <div class="relative" id="profile-dropdown-wrap">
        <button id="profile-dropdown-btn" onclick="toggleProfileDropdown()"
            class="flex items-center gap-3 px-2 py-1.5 rounded-xl hover:bg-gray-50 transition cursor-pointer">
            {{-- Avatar --}}
            <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-user text-red-600 text-sm"></i>
            </div>
            {{-- Name & Role --}}
            <div class="text-left hidden sm:block">
                <p class="text-sm font-semibold text-gray-800 leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->getRoleNames()->first() }}</p>
            </div>
            <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" id="profile-chevron"></i>
        </button>

        {{-- Dropdown Menu --}}
        <div id="profile-dropdown-menu"
            class="hidden absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
            {{-- User info (mobile fallback) --}}
            <div class="px-4 py-3 border-b border-gray-100 sm:hidden">
                <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->getRoleNames()->first() }}</p>
            </div>

            {{-- Manage Profile --}}
            <a href="{{ route('profil.edit') }}"
                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                <i class="fa-solid fa-user-pen w-4 text-gray-400"></i>
                Kelola Profil
            </a>

            {{-- Beranda --}}
            <a href="{{ route('beranda') }}" target="_blank"
                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                <i class="fa-solid fa-house w-4 text-gray-400"></i>
                Lihat Beranda
            </a>

            <div class="border-t border-gray-100 my-1"></div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                    <i class="fa-solid fa-right-from-bracket w-4"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>

</header>

@push('scripts')
<script>
function toggleProfileDropdown() {
    const menu = document.getElementById('profile-dropdown-menu');
    const chevron = document.getElementById('profile-chevron');
    menu.classList.toggle('hidden');
    chevron.style.transform = menu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
}

// Close when clicking outside
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('profile-dropdown-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('profile-dropdown-menu').classList.add('hidden');
        document.getElementById('profile-chevron').style.transform = 'rotate(0deg)';
    }
});
</script>
@endpush
