@extends('layout.app')

@section('title', 'Manajemen Akun Publik')

@section('content')

@include('sections.page-header', ['title' => 'Manajemen Akun Publik', 'subtitle' => 'Kelola akun pengguna umum '])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 w-full">
    <form method="GET" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'akun-publik.index') }}" id="filterForm" class="w-full sm:w-auto">
        <div class="relative w-full sm:w-auto">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text"
                id="searchInput"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama atau email..."
                class="pl-9 pr-4 py-2 border border-gray-200 rounded-full text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[900px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">No. HP</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Alamat</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                    <td class="px-4 py-4 text-gray-600">{{ $user->email }}</td>
                    <td class="px-4 py-4 text-gray-600">{{ $user->phone ?? '-' }}</td>
                    <td class="px-4 py-4 text-gray-600 max-w-xs truncate">{{ $user->address ?? '-' }}</td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                            {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            <i class="fa-solid {{ $user->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }} text-xs"></i>
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'akun-publik.toggle', $user) }}">
                            @csrf
                            <button type="submit"
                                class="{{ $user->is_active ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700' }} text-xs font-medium">
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada akun publik</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $users->links() }}</div>
</div>

<script>
    let timer;

    document.getElementById('searchInput').addEventListener('keyup', function () {

    clearTimeout(timer);

    timer = setTimeout(function () {
        document.getElementById('filterForm').submit();
    }, 500);

    });

</script>

@endsection
