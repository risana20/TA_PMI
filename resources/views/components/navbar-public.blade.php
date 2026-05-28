<nav class="sticky top-0 z-40 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center h-16 justify-between">

            {{-- Kiri: Brand --}}
            <div class="flex items-center shrink-0">
                <a href="{{ route('beranda') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('assets/logo-pmi.png') }}" alt="PMI" class="h-12 w-auto object-contain">
                    <div class="h-7 w-px bg-gray-200"></div>
                    <div class="leading-tight">
                        <p class="font-bold text-sm text-gray-900">Griya PMI</p>
                        <p class="text-[10px] text-gray-400 tracking-wide">SURAKARTA</p>
                    </div>
                </a>
            </div>

            {{-- Tengah: Nav Links (desktop only) --}}
            <div class="hidden md:flex flex-1 items-center justify-center gap-6">
                <a href="{{ route('beranda') }}"
                   class="text-sm whitespace-nowrap transition-colors
                          {{ request()->routeIs('beranda') ? 'text-red-600 font-semibold' : 'text-gray-600 hover:text-red-600' }}">
                    Beranda
                </a>
                <a href="{{ route('artikel.index') }}"
                   class="text-sm whitespace-nowrap transition-colors
                          {{ request()->routeIs('artikel.*') ? 'text-red-600 font-semibold' : 'text-gray-600 hover:text-red-600' }}">
                    Artikel Kegiatan
                </a>
                <a href="{{ route('kebutuhan-mendesak') }}"
                   class="text-sm whitespace-nowrap transition-colors
                          {{ request()->routeIs('kebutuhan-mendesak') ? 'text-red-600 font-semibold' : 'text-gray-600 hover:text-red-600' }}">
                    Kebutuhan Mendesak
                </a>
                <a href="{{ route('kunjungan.index') }}"
                   class="text-sm whitespace-nowrap transition-colors
                          {{ request()->routeIs('kunjungan.*') ? 'text-red-600 font-semibold' : 'text-gray-600 hover:text-red-600' }}">
                    Kunjungan
                </a>
                @auth
                <a href="{{ route('donasi.index') }}"
                   class="text-sm whitespace-nowrap transition-colors
                          {{ request()->routeIs('donasi.*') ? 'text-red-600 font-semibold' : 'text-gray-600 hover:text-red-600' }}">
                    Donasi
                </a>
                
                <a href="{{ route('cek-status.index') }}"
                   class="text-sm whitespace-nowrap transition-colors
                          {{ request()->routeIs('cek-status.*') ? 'text-red-600 font-semibold' : 'text-gray-600 hover:text-red-600' }}">
                    Cek Status
                </a>
                @endauth
            </div>

            {{-- Kanan: Auth (desktop only) --}}
            <div class="hidden md:flex shrink-0 items-center justify-end gap-2 min-w-fit">
                @auth
                    {{-- Dropdown Profile --}}
                    <div class="relative" id="profile-wrapper">
                        <button type="button" id="profile-btn"
                            class="flex items-center gap-2.5 hover:opacity-80 transition focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-user text-red-400 text-xs"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 whitespace-nowrap">
                                {{ auth()->user()->name }}
                            </span>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform" id="profile-chevron"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div id="profile-dropdown"
                            class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                            <a href="{{ route('profil.edit') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-circle-user text-gray-400 w-4 text-center"></i>
                                Kelola Profil
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                    <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('register') }}"
                       class="border border-red-600 text-red-600 font-semibold text-sm px-4 py-1.5 rounded-lg hover:bg-red-50 transition">
                        Daftar
                    </a>
                    <a href="{{ route('login') }}"
                       class="bg-red-600 text-white font-semibold text-sm px-4 py-1.5 rounded-lg hover:bg-red-700 transition">
                        Masuk
                    </a>
                @endauth
            </div>

            {{-- Hamburger Button (mobile only) --}}
            <div class="flex md:hidden items-center">
                <button type="button" id="mobile-menu-btn" class="text-gray-500 hover:text-red-600 focus:outline-none p-2 rounded-lg hover:bg-gray-50 transition">
                    <i class="fa-solid fa-bars text-xl" id="menu-icon-bars"></i>
                    <i class="fa-solid fa-xmark text-xl hidden" id="menu-icon-x"></i>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile Menu Container --}}
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 shadow-md">
        <div class="px-6 py-4 space-y-3 flex flex-col">
            <a href="{{ route('beranda') }}"
               class="text-sm py-2.5 px-3 rounded-lg transition-colors {{ request()->routeIs('beranda') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-red-600' }}">
                Beranda
            </a>
            <a href="{{ route('artikel.index') }}"
               class="text-sm py-2.5 px-3 rounded-lg transition-colors {{ request()->routeIs('artikel.*') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-red-600' }}">
                Artikel Kegiatan
            </a>
            <a href="{{ route('kebutuhan-mendesak') }}"
               class="text-sm py-2.5 px-3 rounded-lg transition-colors {{ request()->routeIs('kebutuhan-mendesak') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-red-600' }}">
                Kebutuhan Mendesak
            </a>
            <a href="{{ route('kunjungan.index') }}"
               class="text-sm py-2.5 px-3 rounded-lg transition-colors {{ request()->routeIs('kunjungan.*') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-red-600' }}">
                Kunjungan
            </a>
            @auth
            <a href="{{ route('donasi.index') }}"
               class="text-sm py-2.5 px-3 rounded-lg transition-colors {{ request()->routeIs('donasi.*') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-red-600' }}">
                Donasi
            </a>
            <a href="{{ route('cek-status.index') }}"
               class="text-sm py-2.5 px-3 rounded-lg transition-colors {{ request()->routeIs('cek-status.*') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-red-600' }}">
                Cek Status
            </a>

            <div class="border-t border-gray-100 my-2 pt-2">
                <div class="flex items-center gap-2.5 px-3 py-2">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user text-red-400 text-xs"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ auth()->user()->name }}
                    </span>
                </div>
                <a href="{{ route('profil.edit') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <i class="fa-solid fa-circle-user text-gray-400 w-4 text-center"></i>
                    Kelola Profil
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition text-left">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                        Logout
                    </button>
                </form>
            </div>
            @else
            <div class="border-t border-gray-100 my-2 pt-4 flex flex-col gap-2">
                <a href="{{ route('register') }}"
                   class="w-full text-center border border-red-600 text-red-600 font-semibold text-sm px-4 py-2.5 rounded-lg hover:bg-red-50 transition">
                    Daftar
                </a>
                <a href="{{ route('login') }}"
                   class="w-full text-center bg-red-600 text-white font-semibold text-sm px-4 py-2.5 rounded-lg hover:bg-red-700 transition">
                    Masuk
                </a>
            </div>
            @endauth
        </div>
    </div>
</nav>

<script>
(function () {
    const btn      = document.getElementById('profile-btn');
    const dropdown = document.getElementById('profile-dropdown');
    const chevron  = document.getElementById('profile-chevron');
    
    if (btn && dropdown) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const open = !dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden', open);
            if (chevron) {
                chevron.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        });

        document.addEventListener('click', function () {
            dropdown.classList.add('hidden');
            if (chevron) {
                chevron.style.transform = 'rotate(0deg)';
            }
        });
    }

    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu    = document.getElementById('mobile-menu');
    const menuIconBars  = document.getElementById('menu-icon-bars');
    const menuIconX     = document.getElementById('menu-icon-x');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function () {
            const isHidden = mobileMenu.classList.contains('hidden');
            if (isHidden) {
                mobileMenu.classList.remove('hidden');
                menuIconBars.classList.add('hidden');
                menuIconX.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
                menuIconBars.classList.remove('hidden');
                menuIconX.classList.add('hidden');
            }
        });
    }
})();
</script>
