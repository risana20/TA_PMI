@extends('layout.app')

@section('title', 'Donasi & Donatur')

@section('content')

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
<div class="flex flex-wrap items-center gap-3 mb-4 w-full">
    <form method="GET" action="{{ route(request()->segment(1) . '.' . 'donasi.index') }}" class="flex items-center gap-3 flex-1 w-full">
    
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="relative w-full sm:w-auto">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama donatur..."
                class="pl-9 pr-4 py-2 border border-gray-200 rounded-full text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>
    </form>
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
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Bukti Diterima</th>
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
                        @if($donasi->bukti_diterima)
                        <a href="{{ asset('storage/' . $donasi->bukti_diterima) }}" target="_blank" class="text-blue-500 hover:text-blue-700">
                            <i class="fa-solid fa-image"></i> Lihat Foto
                        </a>
                        @else
                        <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    @endif
                    <td class="px-4 py-4">
                        <a href="{{ route(request()->segment(1) . '.' . 'donasi.show', $donasi) }}" class="text-gray-400 hover:text-blue-500 mr-2">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @if($donasi->status === 'Tunggu Verifikasi')
                            <form method="POST" action="{{ route(request()->segment(1) . '.' . 'donasi.verify', $donasi) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-500 hover:text-green-700 text-xs font-medium mr-1">Verifikasi</button>
                            </form>
                        <button type="button" onclick="openRejectModal({{ $donasi->id }})" class="text-red-500 hover:text-red-700 text-xs font-medium">Tolak</button>
                        @elseif(in_array($donasi->status, ['Menunggu Pengiriman', 'Menunggu Donasi Dijemput Petugas']))
                            <button type="button" onclick="openCompleteModal({{ $donasi->id }})"
                                class="text-blue-500 hover:text-blue-700 text-xs font-medium mr-1">Selesai</button>
                        @elseif($donasi->status === 'Selesai' && $tab !== 'Uang')
                            @php
                                $hasBukti = ($donasi->jenis === 'Barang' && $donasi->pemasukanLogistik?->bukti_diterima) || 
                                            ($donasi->jenis === 'Makanan' && $donasi->donasiMakanan?->bukti_diterima);
                            @endphp
                            @if(!$hasBukti)
                            <button type="button" onclick="openCompleteModal({{ $donasi->id }})"
                                class="text-blue-500 hover:text-blue-700 text-xs font-medium mr-1">Upload Bukti</button>
                            @endif
                        @endif
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
</script>
@endpush
@endsection
