@extends('layout.app')

@section('title', 'Donasi & Donatur')

@section('content')

@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-6 shadow-sm">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

@include('sections.page-header', ['title' => 'Donasi & Donatur', 'subtitle' => 'Kelola dan verifikasi donasi'])

{{-- Tab --}}
<div class="overflow-x-auto border-b border-gray-200 mb-4">
    <div class="flex min-w-max">
        @foreach(['Uang', 'Barang', 'Makanan'] as $t)
        <a href="{{ route(request()->segment(1) . '.' . 'donasi.index', ['tab' => $t]) }}"
            class="pb-2 px-4 text-sm {{ $tab === $t ? 'font-semibold text-gray-900 border-b-2 border-gray-900' : 'text-gray-400 hover:text-gray-600' }}">
            @if($t === 'Uang') <i class="fa-solid fa-money-bill-wave mr-1 text-green-500"></i>
            @elseif($t === 'Barang') <i class="fa-solid fa-box mr-1 text-blue-500"></i>
            @else <i class="fa-solid fa-utensils mr-1 text-orange-500"></i>
            @endif
            Donasi {{ $t }}
        </a>
        @endforeach
    </div>
</div>

{{-- Toolbar --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-4 w-full">
    <form method="GET" action="{{ route(request()->segment(1) . '.' . 'donasi.index') }}" id="filterForm" class="flex flex-wrap items-center gap-3 flex-1 w-full sm:w-auto">
        <input type="hidden" name="tab" value="{{ $tab }}">
        
        {{-- Search --}}
        <div class="relative w-full sm:w-64">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama donatur..."
                class="pl-9 pr-4 py-2 border border-gray-200 rounded-full text-sm w-full focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>

        {{-- Filter Status Donasi --}}
        <div class="relative w-full sm:w-auto">
            <select 
                name="status_donasi"
                id="statusDonasiFilter"
                class="border border-gray-200 rounded-lg pl-3 pr-8 py-2 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white text-gray-600 w-full sm:w-auto">
                <option value="">Semua Status Donasi</option>
                <option value="Tunggu Verifikasi" {{ request('status_donasi') == 'Tunggu Verifikasi' ? 'selected' : '' }}>Tunggu Verifikasi</option>
                @if($tab !== 'Uang')
                <option value="Menunggu Pengiriman" {{ request('status_donasi') == 'Menunggu Pengiriman' ? 'selected' : '' }}>Menunggu Pengiriman</option>
                <option value="Menunggu Donasi Dijemput Petugas" {{ request('status_donasi') == 'Menunggu Donasi Dijemput Petugas' ? 'selected' : '' }}>Menunggu Jemputan</option>
                @endif
                <option value="Selesai" {{ request('status_donasi') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="Donasi Ditolak" {{ request('status_donasi') == 'Donasi Ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <span class="absolute inset-y-0 right-2.5 flex items-center pointer-events-none text-gray-400">
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </span>
        </div>

        {{-- Filter Status Logistik --}}
        @if($tab !== 'Uang')
        <div class="relative w-full sm:w-auto">
            <select 
                name="status_logistik"
                id="statusLogistikFilter"
                class="border border-gray-200 rounded-lg pl-3 pr-8 py-2 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white text-gray-600 w-full sm:w-auto">
                <option value="">Semua Status Logistik</option>
                <option value="Sudah" {{ request('status_logistik') == 'Sudah' ? 'selected' : '' }}>Sudah Ditambahkan</option>
                <option value="Belum" {{ request('status_logistik') == 'Belum' ? 'selected' : '' }}>Belum Ditambahkan</option>
            </select>
            <span class="absolute inset-y-0 right-2.5 flex items-center pointer-events-none text-gray-400">
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </span>
        </div>
        @endif
    </form>
    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3 sm:ml-auto w-full sm:w-auto">
        <a href="{{ route(request()->segment(1) . '.' . 'donasi.index', ['export' => 'pdf'] + request()->query()) }}"
            class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm font-medium flex items-center justify-center gap-2 transition shadow-sm">
            <i class="fa-solid fa-download"></i> Ekspor PDF
        </a>
        <a href="{{ route(request()->segment(1) . '.' . 'donasi.index', ['export' => 'excel'] + request()->query()) }}"
            class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm font-medium flex items-center justify-center gap-2 transition shadow-sm">
            <i class="fa-solid fa-file-excel"></i> Ekspor Excel
        </a>
        <button type="button" onclick="openAddDonasiModal()"
            class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-4 py-2 text-sm flex items-center justify-center gap-2 transition shadow-sm">
            <i class="fa-solid fa-plus"></i> Tambah Donasi
        </button>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[1000px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Donatur</th>
                    @if($tab === 'Uang')
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nominal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Bank Tujuan</th>
                    @elseif($tab === 'Barang')
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nama Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Kondisi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">No HP</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Alamat</th>
                    @else
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nama Makanan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Jenis</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">No HP</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Alamat</th>
                    @endif
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Tanggal @if($tab !== 'Uang') & Jam @endif</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
                    @if($tab !== 'Uang')
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status Logistik</th>
                    @endif
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($donasis as $donasi)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4 font-medium text-gray-900">{{ $donasi->nama_donatur }}</td>
                    @if($tab === 'Uang')
                    <td class="px-4 py-4 font-semibold text-green-600">Rp {{ number_format($donasi->donasiUang->nominal ?? 0, 0, ',', '.') }}</td>
                    <td class="px-4 py-4 text-gray-600">{{ $donasi->donasiUang->bank_tujuan ?? '-' }}</td>
                    @elseif($tab === 'Barang')
                    <td class="px-4 py-4 text-gray-600">
                        {{ $donasi->pemasukanLogistik->nama_barang ?? $donasi->pemasukanLogistik->stokLogistik->itemLogistik->nama_item ?? '-' }} 
                        ({{ $donasi->pemasukanLogistik->jumlah ?? 0 }})
                    </td>
                    <td class="px-4 py-4 text-gray-600">{{ $donasi->pemasukanLogistik->kondisi ?? '-' }}</td>
                    <td class="px-4 py-4 text-gray-600">{{ $donasi->user->phone ?? '-' }}</td>
                    <td class="px-4 py-4 text-gray-600 border-x border-gray-100 max-w-[200px] truncate" title="{{ $donasi->user->address ?? '-' }}">{{ $donasi->user->address ?? '-' }}</td>
                    @else
                    <td class="px-4 py-4 text-gray-600">{{ $donasi->donasiMakanan->nama_makanan ?? '-' }} ({{ $donasi->donasiMakanan->jumlah_makanan ?? '-' }})</td>
                    <td class="px-4 py-4 text-gray-600">{{ $donasi->donasiMakanan->jenis_makanan ?? '-' }}</td>
                    <td class="px-4 py-4 text-gray-600">{{ $donasi->user->phone ?? '-' }}</td>
                    <td class="px-4 py-4 text-gray-600 border-x border-gray-100 max-w-[200px] truncate" title="{{ $donasi->user->address ?? '-' }}">{{ $donasi->user->address ?? '-' }}</td>
                    @endif
                    <td class="px-4 py-4 text-gray-600">
                        {{ $donasi->created_at->format('d M Y') }}
                        @if($tab !== 'Uang' && $donasi->jam_penyerahan)
                        <br><span class="text-xs text-gray-400"><i class="fa-regular fa-clock text-[10px]"></i> {{ \Carbon\Carbon::parse($donasi->jam_penyerahan)->format('H:i') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">@include('components.badge-status', ['status' => $donasi->status])</td>
                    @if($tab !== 'Uang')
                    <td class="px-4 py-4">
                        @php
                            $hasStockMap = ($donasi->jenis === 'Barang' && $donasi->pemasukanLogistik?->stok_logistik_id !== null) || 
                                           ($donasi->jenis === 'Makanan' && $donasi->pemasukanLogistik !== null);
                        @endphp
                        @if($hasStockMap)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <i class="fa-solid fa-circle-check mr-1.5 text-emerald-500"></i> Sudah Ditambahkan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-50 text-gray-600 border border-gray-100">
                                <i class="fa-solid fa-clock mr-1.5 text-gray-400"></i> Belum Ditambahkan
                            </span>
                        @endif
                    </td>
                    @endif
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2.5">
                            <a href="{{ route(request()->segment(1) . '.' . 'donasi.show', $donasi) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:bg-blue-50 hover:text-blue-600 border border-gray-100 hover:border-blue-200 transition" 
                               title="Lihat Detail">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                            @if($donasi->status === 'Tunggu Verifikasi')
                                <form method="POST" action="{{ route(request()->segment(1) . '.' . 'donasi.verify', $donasi) }}" class="inline-flex">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 text-xs font-semibold border border-emerald-100 transition">
                                        Verifikasi
                                    </button>
                                </form>
                                <button type="button" onclick="openRejectModal({{ $donasi->id }})" 
                                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 text-xs font-semibold border border-red-100 transition">
                                    Tolak
                                </button>
                            @elseif(in_array($donasi->status, ['Menunggu Pengiriman', 'Menunggu Donasi Dijemput Petugas']))
                                <button type="button" onclick="openCompleteModal({{ $donasi->id }})"
                                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 text-xs font-semibold border border-blue-100 transition">
                                    Selesai
                                </button>
                            @elseif($donasi->status === 'Selesai' && $tab !== 'Uang')
                                @php
                                    $hasBukti = ($donasi->jenis === 'Barang' && $donasi->pemasukanLogistik?->bukti_diterima) || 
                                                ($donasi->jenis === 'Makanan' && $donasi->donasiMakanan?->bukti_diterima);
                                @endphp
                                @if(!$hasBukti)
                                <button type="button" onclick="openCompleteModal({{ $donasi->id }})"
                                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 text-xs font-semibold border border-amber-100 transition">
                                    Upload Bukti
                                </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $tab === 'Uang' ? 6 : 9 }}" class="px-4 py-8 text-center text-gray-400">Belum ada donasi {{ $tab }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $donasis->links() }}</div>
</div>


{{-- Modal Selesai (untuk Barang/Makanan) --}}
<div id="modal-complete" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Selesaikan Donasi</h3>
            <button onclick="document.getElementById('modal-complete').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="form-complete" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <p class="text-sm text-gray-600 mb-4">Silakan upload bukti foto donasi telah diterima untuk menyelesaikan proses.</p>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Bukti</label>
                <input type="file" name="bukti_diterima" accept="image/*" required
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-complete').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg px-4 py-2 text-sm">Batal</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-4 py-2 text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tolak Donasi --}}
<div id="modal-reject" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg text-gray-900">Penolakan Donasi</h3>
            <button onclick="document.getElementById('modal-reject').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="form-reject" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="alasan_penolakan" rows="3" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan alasan mengapa donasi ditolak..."></textarea>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-reject').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg px-4 py-2 text-sm">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-4 py-2 text-sm">Tolak Donasi</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Donasi --}}
<div id="modal-add-donasi" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-5 sm:p-6 overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <h3 class="font-bold text-lg text-gray-900">Tambah Donasi</h3>
            <button type="button" onclick="closeAddDonasiModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route(request()->segment(1) . '.' . 'donasi.store') }}" method="POST" enctype="multipart/form-data" id="form-add-donasi" class="space-y-4">
            @csrf
            
            {{-- Pilih Jenis --}}
            <div>
                <span class="block text-sm font-semibold text-gray-700 mb-2">Kategori Donasi</span>
                <div class="grid grid-cols-3 gap-3 items-stretch">
                    @foreach([
                        'Uang'    => ['fa-credit-card', 'Uang'],
                        'Barang'  => ['fa-box',         'Barang'],
                        'Makanan' => ['fa-utensils',    'Makanan'],
                    ] as $jenis => $info)
                    <label class="cursor-pointer flex flex-col">
                        <input type="radio" name="jenis" value="{{ $jenis }}"
                               class="sr-only add-jenis-radio" {{ $jenis === $tab ? 'checked' : '' }}>
                        <div class="add-jenis-card flex-1 flex flex-col items-center justify-center border-2 rounded-xl p-3 transition
                                {{ $jenis === $tab ? 'border-red-500 bg-red-50' : 'border-gray-200 bg-white' }}"
                             data-jenis="{{ $jenis }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center mb-1.5
                                    {{ $jenis === $tab ? 'bg-red-600' : 'bg-gray-100' }}">
                                <i class="fa-solid {{ $info[0] }} text-xs
                                        {{ $jenis === $tab ? 'text-white' : 'text-gray-400' }}"></i>
                            </div>
                            <p class="font-semibold text-xs text-gray-900">
                                {{ $info[1] }}
                            </p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Data Donatur --}}
            <div class="bg-gray-50 p-4 rounded-xl space-y-3">
                <h4 class="font-bold text-xs uppercase tracking-wider text-gray-500">Data Donatur</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Donatur <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_donatur" required placeholder="Contoh: Budi Santoso"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">No. HP / WhatsApp</label>
                        <input type="text" name="phone" placeholder="Contoh: 08123456789"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat</label>
                    <textarea name="address" rows="2" placeholder="Alamat lengkap donatur..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                </div>
            </div>

            {{-- Detail Uang --}}
            <div id="add-detail-Uang" class="add-detail-donasi space-y-4">
                <h4 class="font-bold text-xs uppercase tracking-wider text-gray-500">Detail Donasi Uang</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="nominal" min="1" placeholder="100000"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Bank Tujuan <span class="text-red-500">*</span></label>
                        <select name="bank_tujuan"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                            <option value="">Pilih bank...</option>
                            <option value="Bank Syariah Indonesia 703 962 1597">Bank Syariah Indonesia 703 962 1597</option>
                            <option value="Bank Jateng Syariah 5022 040 518">Bank Jateng Syariah 5022 040 518</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Bukti Transfer (Opsional)</label>
                    <input type="file" name="bukti_transfer" accept="image/png,image/jpeg"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                </div>
            </div>

            {{-- Detail Barang --}}
            <div id="add-detail-Barang" class="add-detail-donasi hidden space-y-4">
                <h4 class="font-bold text-xs uppercase tracking-wider text-gray-500">Detail Donasi Barang</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Nama Barang <span class="text-red-500">*</span></label>
                        <select name="nama_barang" id="add_barang_select"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                            <option value="">Pilih Barang...</option>
                            @foreach($barangStok as $stok)
                                @if($stok->itemLogistik)
                                <option value="{{ $stok->itemLogistik->nama_item }}" data-satuan="{{ $stok->itemLogistik->satuan }}">
                                    {{ $stok->itemLogistik->nama_item }}
                                </option>
                                @endif
                            @endforeach
                            <option value="Lainnya">Lainnya (Barang baru)</option>
                        </select>
                    </div>
                    <div id="add_custom_barang_wrapper" class="hidden">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Barang Lainnya <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_barang_custom" id="add_nama_barang_custom" placeholder="Contoh: Selimut, Pakaian, dll" disabled
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah <span class="text-red-500">*</span></label>
                        <input type="number" min="1" name="jumlah_barang" placeholder="Contoh: 2"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" name="satuan" id="add_satuan_barang" placeholder="Satuan" readonly pattern="[^0-9]+" title="Satuan tidak boleh mengandung angka"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-gray-50 text-gray-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Kondisi <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                <input type="radio" name="kondisi" value="Baru" checked class="accent-red-600 w-3.5 h-3.5"> Baru
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                <input type="radio" name="kondisi" value="Bekas Layak" class="accent-red-600 w-3.5 h-3.5"> Bekas Layak
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Metode Penyerahan <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                <input type="radio" name="metode_penyerahan" value="Antar Sendiri" checked class="accent-red-600 w-3.5 h-3.5"> Antar Sendiri
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                <input type="radio" name="metode_penyerahan" value="Dijemput petugas" class="accent-red-600 w-3.5 h-3.5"> Dijemput petugas
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Penyerahan <span class="text-red-500">*</span></label>
                        <input type="date" name="tgl_penyerahan"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Penyerahan <span class="text-red-500">*</span></label>
                        <input type="time" name="jam_penyerahan"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
            </div>

            {{-- Detail Makanan --}}
            <div id="add-detail-Makanan" class="add-detail-donasi hidden space-y-4">
                <h4 class="font-bold text-xs uppercase tracking-wider text-gray-500">Detail Donasi Makanan</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Nama Makanan <span class="text-red-500">*</span></label>
                        <select name="nama_makanan" id="add_makanan_select"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                            <option value="">Pilih Makanan...</option>
                            @foreach($makananStok as $stok)
                                @if($stok->itemLogistik)
                                <option value="{{ $stok->itemLogistik->nama_item }}" data-satuan="{{ $stok->itemLogistik->satuan }}">
                                    {{ $stok->itemLogistik->nama_item }}
                                </option>
                                @endif
                            @endforeach
                            <option value="Lainnya">Lainnya (Makanan baru)</option>
                        </select>
                    </div>
                    <div id="add_custom_makanan_wrapper" class="hidden">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Makanan Lainnya <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_makanan_custom" id="add_nama_makanan_custom" placeholder="Contoh: Biskuit, Susu, dll" disabled
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah <span class="text-red-500">*</span></label>
                        <input type="number" min="1" name="jumlah_makanan_value" id="add_jumlah_makanan_value" placeholder="Contoh: 10"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" name="jumlah_makanan_satuan" id="add_jumlah_makanan_satuan" placeholder="Satuan" readonly pattern="[^0-9]+" title="Satuan tidak boleh mengandung angka"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-gray-50 text-gray-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div id="add_jenis_makanan_wrapper" class="hidden">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Jenis Makanan <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                <input type="radio" name="jenis_makanan" value="Bahan Mentah" checked disabled class="accent-red-600 w-3.5 h-3.5"> Bahan Mentah
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                <input type="radio" name="jenis_makanan" value="Siap Saji" disabled class="accent-red-600 w-3.5 h-3.5"> Siap Saji
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Metode Penyerahan</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                <input type="radio" name="metode_penyerahan" value="Antar Sendiri" checked class="accent-red-600 w-3.5 h-3.5"> Antar Sendiri
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                <input type="radio" name="metode_penyerahan" value="Dijemput petugas" class="accent-red-600 w-3.5 h-3.5"> Dijemput petugas
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Penyerahan</label>
                        <input type="date" name="tgl_penyerahan"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Penyerahan</label>
                        <input type="time" name="jam_penyerahan"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeAddDonasiModal()"
                    class="border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg px-4 py-2 text-sm font-semibold transition">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-4 py-2 text-sm transition shadow-sm">Kirim Donasi</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    var prefix = '{{ request()->segment(1) }}';

function openCompleteModal(id) {
    document.getElementById('form-complete').action = '/' + prefix + '/donasi/' + id + '/complete';
    document.getElementById('modal-complete').classList.remove('hidden');
}

function openRejectModal(id) {
    document.getElementById('form-reject').action = '/' + prefix + '/donasi/' + id + '/reject';
    document.getElementById('modal-reject').classList.remove('hidden');
}

let timer;

const searchInput = document.querySelector('input[name="search"]');

if (searchInput) {
    searchInput.addEventListener('keyup', function () {
        clearTimeout(timer);

        timer = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
}

const statusDonasiFilter = document.getElementById('statusDonasiFilter');
const statusLogistikFilter = document.getElementById('statusLogistikFilter');

if (statusDonasiFilter) {
    statusDonasiFilter.addEventListener('change', function () {
        this.form.submit();
    });
}

if (statusLogistikFilter) {
    statusLogistikFilter.addEventListener('change', function () {
        this.form.submit();
    });
}

// Modal Tambah Donasi Functions
const addRadios  = document.querySelectorAll('.add-jenis-radio');
const addCards   = document.querySelectorAll('.add-jenis-card');
const addDetails = document.querySelectorAll('.add-detail-donasi');

addRadios.forEach(radio => {
    radio.addEventListener('change', function () {
        const jenis = this.value;
        updateAddFormState(jenis);
    });
});

function openAddDonasiModal() {
    document.getElementById('modal-add-donasi').classList.remove('hidden');
    // Set check state based on current active tab
    const activeJenis = '{{ $tab }}';
    const activeRadio = document.querySelector(`.add-jenis-radio[value="${activeJenis}"]`);
    if (activeRadio) {
        activeRadio.click();
    }
}

function closeAddDonasiModal() {
    document.getElementById('modal-add-donasi').classList.add('hidden');
}

function updateAddFormState(jenis) {
    addCards.forEach(card => {
        const active = card.dataset.jenis === jenis;
        card.classList.toggle('border-red-500', active);
        card.classList.toggle('bg-red-50',      active);
        card.classList.toggle('border-gray-200', !active);
        card.classList.toggle('bg-white',        !active);

        const iconWrap = card.querySelector('div');
        const icon     = card.querySelector('i');
        iconWrap.classList.toggle('bg-red-600',  active);
        iconWrap.classList.toggle('bg-gray-100', !active);
        icon.classList.toggle('text-white',      active);
        icon.classList.toggle('text-gray-400',   !active);
    });

    addDetails.forEach(d => {
        const isActive = d.id === 'add-detail-' + jenis;
        d.classList.toggle('hidden', !isActive);

        // Disable all inputs in hidden sections, enable in active section
        const inputs = d.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            // Keep customized custom field states
            if (input.id === 'add_nama_barang_custom' && !isActive) {
                input.disabled = true;
            } else if (input.id === 'add_nama_makanan_custom' && !isActive) {
                input.disabled = true;
            } else {
                input.disabled = !isActive;
            }
        });
    });

    // Call custom Makanan/Barang state updates
    if (jenis === 'Makanan') {
        updateAddMakananInputsState();
    } else if (jenis === 'Barang') {
        updateAddBarangInputsState();
    }
}

const addMakananSelect = document.getElementById('add_makanan_select');
const addCustomMakananWrapper = document.getElementById('add_custom_makanan_wrapper');
const addNamaMakananCustom = document.getElementById('add_nama_makanan_custom');
const addJumlahMakananSatuan = document.getElementById('add_jumlah_makanan_satuan');
const addJenisMakananWrapper = document.getElementById('add_jenis_makanan_wrapper');

function updateAddMakananInputsState() {
    if (!addMakananSelect) return;
    const isMakananActive = !document.getElementById('add-detail-Makanan').classList.contains('hidden');
    if (!isMakananActive) return;

    const isLainnya = addMakananSelect.value === 'Lainnya';
    if (isLainnya) {
        addCustomMakananWrapper.classList.remove('hidden');
        addNamaMakananCustom.required = true;
        addNamaMakananCustom.disabled = false;
        
        addJumlahMakananSatuan.removeAttribute('readonly');
        addJumlahMakananSatuan.classList.remove('bg-gray-50', 'text-gray-500');
        addJumlahMakananSatuan.classList.add('bg-white', 'text-gray-900');
        addJumlahMakananSatuan.placeholder = 'Satuan (kg, box, dll)';

        if (addJenisMakananWrapper) {
            addJenisMakananWrapper.classList.remove('hidden');
            const jenisRadios = addJenisMakananWrapper.querySelectorAll('input[type="radio"]');
            jenisRadios.forEach(radio => radio.disabled = false);
        }
    } else {
        addCustomMakananWrapper.classList.add('hidden');
        addNamaMakananCustom.required = false;
        addNamaMakananCustom.disabled = true;
        addNamaMakananCustom.value = '';
        
        addJumlahMakananSatuan.setAttribute('readonly', 'true');
        addJumlahMakananSatuan.classList.remove('bg-white', 'text-gray-900');
        addJumlahMakananSatuan.classList.add('bg-gray-50', 'text-gray-500');
        
        const selectedOption = addMakananSelect.options[addMakananSelect.selectedIndex];
        const satuan = selectedOption ? selectedOption.getAttribute('data-satuan') : '';
        addJumlahMakananSatuan.value = satuan || '';
        addJumlahMakananSatuan.placeholder = 'Satuan';

        if (addJenisMakananWrapper) {
            addJenisMakananWrapper.classList.add('hidden');
            const jenisRadios = addJenisMakananWrapper.querySelectorAll('input[type="radio"]');
            jenisRadios.forEach(radio => radio.disabled = true);
        }
    }
}

const addBarangSelect = document.getElementById('add_barang_select');
const addCustomBarangWrapper = document.getElementById('add_custom_barang_wrapper');
const addNamaBarangCustom = document.getElementById('add_nama_barang_custom');
const addSatuanBarang = document.getElementById('add_satuan_barang');

function updateAddBarangInputsState() {
    if (!addBarangSelect) return;
    const isBarangActive = !document.getElementById('add-detail-Barang').classList.contains('hidden');
    if (!isBarangActive) return;

    const isLainnya = addBarangSelect.value === 'Lainnya';
    if (isLainnya) {
        addCustomBarangWrapper.classList.remove('hidden');
        addNamaBarangCustom.required = true;
        addNamaBarangCustom.disabled = false;
        
        addSatuanBarang.removeAttribute('readonly');
        addSatuanBarang.classList.remove('bg-gray-50', 'text-gray-500');
        addSatuanBarang.classList.add('bg-white', 'text-gray-900');
        addSatuanBarang.placeholder = 'Satuan (pcs, unit, dll)';
    } else {
        addCustomBarangWrapper.classList.add('hidden');
        addNamaBarangCustom.required = false;
        addNamaBarangCustom.disabled = true;
        addNamaBarangCustom.value = '';
        
        addSatuanBarang.setAttribute('readonly', 'true');
        addSatuanBarang.classList.remove('bg-white', 'text-gray-900');
        addSatuanBarang.classList.add('bg-gray-50', 'text-gray-500');
        
        const selectedOption = addBarangSelect.options[addBarangSelect.selectedIndex];
        const satuan = selectedOption ? selectedOption.getAttribute('data-satuan') : '';
        addSatuanBarang.value = satuan || '';
        addSatuanBarang.placeholder = 'Satuan';
    }
}

if (addMakananSelect) {
    addMakananSelect.addEventListener('change', updateAddMakananInputsState);
}

if (addBarangSelect) {
    addBarangSelect.addEventListener('change', updateAddBarangInputsState);
}

addCards.forEach(card => {
    card.addEventListener('click', function () {
        document.querySelector(`.add-jenis-radio[value="${this.dataset.jenis}"]`).click();
    });
});

const blockNumbers = (el) => {
    if (el) {
        el.addEventListener('input', function() {
            this.value = this.value.replace(/[0-9]/g, '');
        });
    }
};
blockNumbers(document.getElementById('add_satuan_barang'));
blockNumbers(document.getElementById('add_jumlah_makanan_satuan'));
</script>
@endpush
@endsection
