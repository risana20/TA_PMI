<aside class="w-64 bg-white shadow-md flex flex-col h-full flex-shrink-0">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
        <img src="{{ asset('assets/logo-pmi.png') }}" alt="PMI" class="h-9 w-auto object-contain">
        <div class="h-7 w-px bg-gray-200"></div>
        <div class="leading-tight">
            <p class="font-bold text-sm text-gray-900">Griya PMI</p>
            <p class="text-[10px] text-gray-400 tracking-wide">SURAKARTA</p>
        </div>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest px-3 mb-3">Menu Utama</p>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('*.dashboard') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-gauge w-4 text-center"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('*.warga-binaan.*') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-users w-4 text-center"></i>
            <span>Data Warga Binaan</span>
        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('*.monitoring.*') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-heart-pulse w-4 text-center"></i>
            <span>Monitoring Kesehatan</span>
        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'kunjungan.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('*.kunjungan.*') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-calendar-check w-4 text-center"></i>
            <span>Kunjungan</span>
        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'donasi.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('*.donasi.*') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-hand-holding-heart w-4 text-center"></i>
            <span>Donasi & Donatur</span>
        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'logistik.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('*.logistik.*') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-boxes-stacked w-4 text-center"></i>
            <span>Logistik & Inventaris</span>
        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'keuangan.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('*.keuangan.*') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-wallet w-4 text-center"></i>
            <span>Keuangan</span>
        </a>

        @hasrole('admin')
        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'reimbursement.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('*.reimbursement.*') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-receipt w-4 text-center"></i>
            <span>Ajuan Reimbursement</span>
        </a>
        @endhasrole

        @hasrole('superadmin')
        <a href="{{ route('superadmin.acc-reimbursement.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('superadmin.acc-reimbursement.*') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-check-double w-4 text-center"></i>
            <span>ACC Reimbursement</span>
        </a>
        @endhasrole

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'artikel.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('*.artikel.*') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-newspaper w-4 text-center"></i>
            <span>Artikel Kegiatan</span>
        </a>

        @hasrole('superadmin')
        <a href="{{ route('superadmin.akun-griya.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('superadmin.akun-griya.*') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-user-shield w-4 text-center"></i>
            <span>Manajemen Akun Griya</span>
        </a>
        @endhasrole

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'akun-publik.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                  {{ request()->routeIs('*.akun-publik.*') ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fa-solid fa-users-gear w-4 text-center"></i>
            <span>Manajemen Akun Publik</span>
        </a>
    </nav>

    {{-- Logout --}}
    <div class="px-3 py-4 border-t border-gray-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-3 px-3 py-2.5 w-full text-sm text-red-600 hover:bg-red-50 rounded-lg transition">
                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
