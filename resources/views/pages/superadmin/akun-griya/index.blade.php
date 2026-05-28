@extends('layout.app')

@section('title', 'Manajemen Akun Griya')

@section('content')

@include('sections.page-header', ['title' => 'Manajemen Akun Griya', 'subtitle' => 'Kelola akun admin dan superadmin'])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 w-full">
    <form method="GET" action="{{ route('superadmin.akun-griya.index') }}" id="filterForm" class="w-full sm:w-auto">
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
    <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
        class="bg-red-600 text-white rounded-full px-5 py-2 font-semibold text-sm hover:bg-red-700 flex items-center justify-center gap-2 w-full sm:w-auto">
        <i class="fa-solid fa-plus"></i> Tambah Akun
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[800px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                    <td class="px-4 py-4 text-gray-600">{{ $user->email }}</td>
                    <td class="px-4 py-4">
                        @php $role = $user->getRoleNames()->first(); @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                            {{ $role === 'superadmin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ strtoupper($role) }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                            {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 flex items-center gap-2">
                        <button onclick="openEditAkun({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->getRoleNames()->first() }}')"
                            class="text-gray-400 hover:text-yellow-500"><i class="fa-solid fa-pen"></i></button>
                        <form method="POST" action="{{ route('superadmin.akun-griya.toggle', $user) }}">
                            @csrf
                            <button type="submit"
                                class="{{ $user->is_active ? 'text-red-400 hover:text-red-600' : 'text-green-400 hover:text-green-600' }} text-xs font-medium">
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada akun griya</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $users->links() }}</div>
</div>

{{-- Modal Tambah --}}
<div id="modal-tambah" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Tambah Akun Griya</h3>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('superadmin.akun-griya.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="admin">Admin</option>
                    <option value="superadmin">Superadmin</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 rounded-lg px-4 py-2 text-sm">Batal</button>
                <button type="submit" class="bg-red-600 text-white font-semibold rounded-lg px-4 py-2 text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Edit Akun</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="form-edit" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" id="edit-name" name="name" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="edit-email" name="email" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="edit-role" name="role" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="admin">Admin</option>
                    <option value="superadmin">Superadmin</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 rounded-lg px-4 py-2 text-sm">Batal</button>
                <button type="submit" class="bg-red-600 text-white font-semibold rounded-lg px-4 py-2 text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openEditAkun(id, name, email, role) {
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-role').value = role;
    document.getElementById('form-edit').action = '/superadmin/akun-griya/' + id;
    document.getElementById('modal-edit').classList.remove('hidden');
}

    let timer;

    document.getElementById('searchInput').addEventListener('keyup', function () {

    clearTimeout(timer);

    timer = setTimeout(function () {
        document.getElementById('filterForm').submit();
    }, 500);

    });

</script>
@endpush
