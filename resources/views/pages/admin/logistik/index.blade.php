@extends('layout.app')

@section('title', 'Logistik & Inventaris')

@section('content')

@include('sections.page-header', ['title' => 'Logistik & Inventaris', 'subtitle' => 'Kelola stok logistik dan inventaris'])

{{-- Toolbar --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        {{-- Search --}}
        <div class="relative flex-1 min-w-[200px]">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari barang..."
                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>

        {{-- Filter Jenis/Kategori --}}
        <div class="relative">
            <select name="kategori" onchange="this.form.submit()"
                class="border border-gray-200 rounded-lg px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 appearance-none bg-white">
                <option value="">Jenis</option>
                <option value="Obat" {{ ($kategori ?? '') === 'Obat' ? 'selected' : '' }}>Obat</option>
                <option value="Makanan" {{ ($kategori ?? '') === 'Makanan' ? 'selected' : '' }}>Makanan</option>
                <option value="Barang" {{ ($kategori ?? '') === 'Barang' ? 'selected' : '' }}>Barang</option>
            </select>
            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>

        {{-- Filter Status --}}
        <div class="relative">
            <select name="status" onchange="this.form.submit()"
                class="border border-gray-200 rounded-lg px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 appearance-none bg-white">
                <option value="">Status</option>
                <option value="Aman" {{ ($status ?? '') === 'Aman' ? 'selected' : '' }}>Aman</option>
                <option value="Mendesak" {{ ($status ?? '') === 'Mendesak' ? 'selected' : '' }}>Mendesak</option>
                <option value="Sangat Mendesak" {{ ($status ?? '') === 'Sangat Mendesak' ? 'selected' : '' }}>Sangat Mendesak</option>
            </select>
            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>
    </form>

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3 mt-4">
        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'logistik.index', ['export' => 'pdf'] + request()->query()) }}"
            class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm font-medium flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-download"></i> Ekspor PDF
        </a>
        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'logistik.index', ['export' => 'excel'] + request()->query()) }}"
            class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm font-medium flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-file-excel"></i> Ekspor Excel
        </a>
        <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
            class="bg-red-600 text-white rounded-lg px-5 py-2 font-semibold text-sm hover:bg-red-700 flex items-center justify-center gap-2 sm:ml-auto">
            <i class="fa-solid fa-plus"></i> Tambah Item
        </button>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[800px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Stok Minimum</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Stok Saat Ini</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logistiks as $item)
                @php $kategori = $item->itemLogistik->jenisLogistik->nama_jenis_logistik ?? '-'; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4 font-medium text-gray-900">{{ $item->itemLogistik->nama_item }}</td>
                    <td class="px-4 py-4">
                        @if($kategori === 'Obat')
                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                <i class="fa-solid fa-pills text-red-400"></i> Obat
                            </span>
                        @elseif($kategori === 'Makanan')
                            <span class="inline-flex items-center gap-1.5 bg-orange-50 text-orange-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                <i class="fa-solid fa-utensils text-orange-400"></i> Makanan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                <i class="fa-solid fa-box text-blue-400"></i> {{ $kategori }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-gray-600">{{ $item->jumlah_minimum }}</td>
                    <td class="px-4 py-4 font-semibold {{ $item->status !== 'Aman' ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $item->jumlah_saat_ini }} {{ $item->itemLogistik->satuan }}
                    </td>
                    <td class="px-4 py-4">
                        @php
                        $statusBadge = match($item->status) {
                            'Aman' => 'bg-green-100 text-green-700',
                            'Mendesak' => 'bg-orange-100 text-orange-700',
                            'Sangat Mendesak' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-600'
                        };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                            {{ strtoupper($item->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'logistik.show', $item) }}" class="text-gray-400 hover:text-gray-600" title="Detail">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada data logistik</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $logistiks->links() }}
    </div>
</div>

{{-- Modal Tambah --}}
<div id="modal-tambah" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-bold text-lg">Tambah Item Logistik & Inventaris</h3>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <p class="text-sm text-gray-400 mb-4">Isi data inventaris</p>

        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'logistik.store') }}" class="space-y-4" id="form-tambah">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang / Makanan / Obat</label>
                <input type="text" name="nama_item" placeholder="Masukkan nama barang / Makanan / Obat" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <div class="relative">
                        <select name="jenis_logistik_id" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 appearance-none bg-white">
                            <option value="">Pilih jenis...</option>
                            @foreach($jenisLogistiks as $jenis)
                                <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis_logistik }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" id="logistik_satuan" placeholder="kg, pcs, liter, ..." required
                           pattern="[^0-9]+" title="Satuan tidak boleh mengandung angka"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum</label>
                    <input type="number" name="jumlah_minimum" min="0" value="0" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Saat Ini</label>
                    <input type="number" name="jumlah_saat_ini" min="0" value="0" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                    class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const logistikSatuanInput = document.getElementById('logistik_satuan');
if (logistikSatuanInput) {
    logistikSatuanInput.addEventListener('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
    });
}
</script>
@endpush
