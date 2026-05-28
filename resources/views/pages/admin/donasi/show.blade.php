@extends('layout.app')

@section('title', 'Detail Donasi')

@section('content')

<div class="flex items-center gap-2 mb-6">
    <a href="{{ route(request()->segment(1) . '.' . 'donasi.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 w-full max-w-2xl">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <h2 class="font-bold text-lg">Detail Donasi — {{ $donasi->jenis }}</h2>
        <div>
            @include('components.badge-status', ['status' => $donasi->status])
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div>
            <p class="text-gray-500 mb-1">Nama Donatur</p>
            <p class="font-semibold">{{ $donasi->nama_donatur }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">Jenis Donasi</p>
            <p class="font-semibold">{{ $donasi->jenis }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">No HP Donatur</p>
            <p class="font-semibold">{{ $donasi->user->phone ?? '-' }}</p>
        </div>
        <div class="sm:col-span-2">
            <p class="text-gray-500 mb-1">Alamat Donatur</p>
            <p class="font-semibold">{{ $donasi->user->address ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">Tanggal Pengajuan</p>
            <p class="font-semibold">{{ $donasi->created_at->format('d M Y H:i') }}</p>
        </div>

        @if($donasi->jenis === 'Uang')
        <div>
            <p class="text-gray-500 mb-1">Nominal</p>
            <p class="font-semibold text-green-600">Rp {{ number_format($donasi->donasiUang->nominal ?? 0, 0, ',', '.') }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">Bank Tujuan</p>
            <p class="font-semibold">{{ $donasi->donasiUang->bank_tujuan ?? '-' }}</p>
        </div>
        @if($donasi->donasiUang && $donasi->donasiUang->bukti_transfer)
        <div class="sm:col-span-2">
            <p class="text-gray-500 mb-2">Bukti Transfer</p>
            <img src="{{ Storage::url($donasi->donasiUang->bukti_transfer) }}" alt="Bukti Transfer"
                class="max-w-full sm:max-w-xs rounded-lg border border-gray-200">
        </div>
        @endif

        @elseif($donasi->jenis === 'Barang')
        <div>
            <p class="text-gray-500 mb-1">Nama Barang</p>
            <p class="font-semibold">{{ $donasi->pemasukanLogistik->nama_barang ?? $donasi->pemasukanLogistik->stokLogistik->itemLogistik->nama_item ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">Jumlah</p>
            <p class="font-semibold">{{ $donasi->pemasukanLogistik->jumlah ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">Kondisi</p>
            <p class="font-semibold">{{ $donasi->pemasukanLogistik->kondisi ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">Metode Penyerahan</p>
            <p class="font-semibold">{{ $donasi->pemasukanLogistik->metode_penyerahan ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">Waktu Penyerahan</p>
            <p class="font-semibold">
                {{ $donasi->pemasukanLogistik?->tgl_penyerahan ? \Carbon\Carbon::parse($donasi->pemasukanLogistik->tgl_penyerahan)->format('d M Y') : '-' }}
                @if($donasi->pemasukanLogistik?->jam_penyerahan)
                pukul {{ \Carbon\Carbon::parse($donasi->pemasukanLogistik->jam_penyerahan)->format('H:i') }}
                @endif
            </p>
        </div>

        @else
        <div>
            <p class="text-gray-500 mb-1">Nama Makanan</p>
            <p class="font-semibold">{{ $donasi->donasiMakanan->nama_makanan ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">Jenis Makanan</p>
            <p class="font-semibold">{{ $donasi->donasiMakanan->jenis_makanan ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">Jumlah</p>
            <p class="font-semibold">{{ $donasi->donasiMakanan->jumlah_makanan ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">Metode Penyerahan</p>
            <p class="font-semibold">{{ $donasi->donasiMakanan->metode_penyerahan ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-1">Waktu Penyerahan</p>
            <p class="font-semibold">
                {{ $donasi->donasiMakanan?->tgl_penyerahan ? \Carbon\Carbon::parse($donasi->donasiMakanan->tgl_penyerahan)->format('d M Y') : '-' }}
                @if($donasi->donasiMakanan?->jam_penyerahan)
                pukul {{ \Carbon\Carbon::parse($donasi->donasiMakanan->jam_penyerahan)->format('H:i') }}
                @endif
            </p>
        </div>
        @endif
    </div>

    @if($donasi->jenis === 'Barang' && $donasi->pemasukanLogistik && $donasi->pemasukanLogistik->bukti_diterima)
    <div class="mt-6 pt-4 border-t border-gray-100">
        <p class="text-gray-500 text-sm mb-2">Foto Bukti Diterima</p>
        <img src="{{ Storage::url($donasi->pemasukanLogistik->bukti_diterima) }}" alt="Bukti Diterima"
            class="max-w-full sm:max-w-xs rounded-lg border border-gray-200">
    </div>
    @elseif($donasi->jenis === 'Makanan' && $donasi->donasiMakanan && $donasi->donasiMakanan->bukti_diterima)
    <div class="mt-6 pt-4 border-t border-gray-100">
        <p class="text-gray-500 text-sm mb-2">Foto Bukti Diterima</p>
        <img src="{{ Storage::url($donasi->donasiMakanan->bukti_diterima) }}" alt="Bukti Diterima"
            class="max-w-full sm:max-w-xs rounded-lg border border-gray-200">
    </div>
    @endif

    @if($donasi->status === 'Tunggu Verifikasi')
    <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-4 border-t border-gray-100">
        <form method="POST" action="{{ route(request()->segment(1) . '.' . 'donasi.verify', $donasi) }}" class="w-full sm:w-auto">
            @csrf
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg px-4 py-2 text-sm justify-center flex items-center">
                <i class="fa-solid fa-check mr-1"></i> Verifikasi
            </button>
        </form>
        <button type="button" onclick="openRejectModal({{ $donasi->id }})" class="w-full sm:w-auto border border-red-600 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm justify-center flex items-center">
            <i class="fa-solid fa-xmark mr-1"></i> Tolak
        </button>
    </div>
    @elseif(in_array($donasi->status, ['Menunggu Pengiriman', 'Menunggu Donasi Dijemput Petugas']))
    <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-4 border-t border-gray-100">
        <button type="button" onclick="document.getElementById('modal-complete').classList.remove('hidden')"
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-4 py-2 text-sm justify-center flex items-center">
            <i class="fa-solid fa-check-double mr-1"></i> Selesaikan Donasi
        </button>
    </div>
    @elseif($donasi->status === 'Selesai' && $donasi->jenis !== 'Uang')
        @php
            $hasBukti = ($donasi->jenis === 'Barang' && $donasi->pemasukanLogistik?->bukti_diterima) || 
                        ($donasi->jenis === 'Makanan' && $donasi->donasiMakanan?->bukti_diterima);
            $hasStockMap = ($donasi->jenis === 'Barang' && $donasi->pemasukanLogistik?->stok_logistik_id) || 
                           ($donasi->jenis === 'Makanan' && \App\Models\PemasukanLogistik::where('donasi_id', $donasi->id)->exists());
        @endphp
        <div class="flex flex-col sm:flex-row flex-wrap gap-3 mt-6 pt-4 border-t border-gray-100">
            <button type="button" onclick="document.getElementById('modal-complete').classList.remove('hidden')"
                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-4 py-2 text-sm justify-center flex items-center">
                <i class="fa-solid {{ $hasBukti ? 'fa-pen-to-square' : 'fa-upload' }} mr-1"></i> {{ $hasBukti ? 'Ubah Bukti Donasi' : 'Upload Bukti' }}
            </button>

            @if($hasStockMap)
                <span class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg">
                    <i class="fa-solid fa-check-circle mr-2"></i> Telah ditambahkan ke stok logistik
                </span>
            @else
                <button type="button" onclick="document.getElementById('modal-map-stock').classList.remove('hidden')"
                    class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg px-4 py-2 text-sm justify-center flex items-center">
                    <i class="fa-solid fa-box mr-1"></i> Masukkan ke Stok Logistik
                </button>
            @endif
        </div>
    @endif
</div>

{{-- Modal Selesai (untuk Barang/Makanan) --}}
<div id="modal-complete" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">
                @if($donasi->status === 'Selesai' && (($donasi->jenis === 'Barang' && $donasi->pemasukanLogistik?->bukti_diterima) || ($donasi->jenis === 'Makanan' && $donasi->donasiMakanan?->bukti_diterima)))
                    Ubah Bukti Donasi
                @else
                    Selesaikan Donasi
                @endif
            </h3>
            <button onclick="document.getElementById('modal-complete').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route(request()->segment(1) . '.' . 'donasi.complete', $donasi) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <p class="text-sm text-gray-600 mb-4">Silakan upload bukti foto donasi telah diterima untuk menyelesaikan proses.</p>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Bukti {{ isset($hasBukti) && $hasBukti ? '(Opsional jika tidak ingin diubah)' : '' }}</label>
                <input type="file" name="bukti_diterima" accept="image/*" {{ isset($hasBukti) && $hasBukti ? '' : 'required' }}
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

{{-- Modal Map Stock --}}
<div id="modal-map-stock" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Masukkan ke Stok Logistik Master</h3>
            <button onclick="document.getElementById('modal-map-stock').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route(request()->segment(1) . '.' . 'donasi.complete', $donasi) }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="masukkan_ke_stok" value="1">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Item Logistik Tujuan</label>
                <select name="stok_logistik_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none bg-white">
                    <option value="">Pilih item logistik...</option>
                    @foreach($stokLogistik as $stok)
                        <option value="{{ $stok->id }}">
                            {{ $stok->itemLogistik->nama_item }} ({{ $stok->jumlah_saat_ini }} {{ $stok->itemLogistik->satuan }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-2">Donasi ini akan secara otomatis menambah stok item logistik master yang dipilih.</p>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-map-stock').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg px-4 py-2 text-sm">Batal</button>
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg px-4 py-2 text-sm">Tambahkan Stok</button>
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
        <form id="form-reject" action="{{ route(request()->segment(1) . '.' . 'donasi.reject', $donasi) }}" method="POST" class="space-y-4">
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

@endsection

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
</script>
@endpush
