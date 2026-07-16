@extends('layout.app')

@section('title', 'Detail Logistik — ' . $logistik->itemLogistik->nama_item)

@section('content')

{{-- Back --}}
<a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'logistik.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 transition mb-5">
    <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar
</a>

{{-- Info Item --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-5">
    <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-4">
        {{-- Icon --}}
        <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0
            @if($logistik->itemLogistik->jenisLogistik->nama_jenis_logistik === 'Obat') bg-red-100 @elseif($logistik->itemLogistik->jenisLogistik->nama_jenis_logistik === 'Makanan') bg-orange-100 @else bg-blue-100 @endif">
            @if($logistik->itemLogistik->jenisLogistik->nama_jenis_logistik === 'Obat')
                <i class="fa-solid fa-pills text-red-500 text-2xl"></i>
            @elseif($logistik->itemLogistik->jenisLogistik->nama_jenis_logistik === 'Makanan')
                <i class="fa-solid fa-utensils text-orange-500 text-2xl"></i>
            @else
                <i class="fa-solid fa-box text-blue-500 text-2xl"></i>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex-1 w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-1">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                    <h2 class="text-xl font-bold text-gray-900">{{ $logistik->itemLogistik->nama_item }}</h2>

                    {{-- Status Badge --}}
                    @php
                    $statusBadge = match($logistik->status) {
                        'Aman' => 'bg-green-100 text-green-700',
                        'Mendesak' => 'bg-orange-100 text-orange-700',
                        'Sangat Mendesak' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-600'
                    };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                        {{ $logistik->status }}
                    </span>
                </div>

                {{-- Delete Button --}}
                <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'logistik.destroy', $logistik) }}"
                    onsubmit="return confirm('Hapus item ini?')" class="flex justify-center mt-1 sm:mt-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 border border-red-200 rounded-lg bg-red-50 hover:bg-red-100 transition" title="Hapus">
                        <i class="fa-solid fa-trash text-xs"></i> Hapus Item
                    </button>
                </form>
            </div>
            <p class="text-sm text-gray-500">Kategori: {{ $logistik->itemLogistik->jenisLogistik->nama_jenis_logistik }}</p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mt-5">
        <div class="bg-gray-50 rounded-xl p-3 sm:p-4 text-center">
            <p class="text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Stok Saat Ini</p>
            <p class="text-base sm:text-lg font-bold {{ $logistik->status !== 'Aman' ? 'text-red-600' : 'text-gray-800' }}">{{ $logistik->jumlah_saat_ini }}</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-3 sm:p-4 text-center relative group">
            <p class="text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Stok Minimum</p>
            <div class="flex items-center justify-center gap-1.5">
                <p class="text-base sm:text-lg font-bold text-gray-800">{{ $logistik->jumlah_minimum }}</p>
                <button onclick="document.getElementById('modal-edit-minimum').classList.remove('hidden')" 
                    class="text-gray-400 hover:text-red-500 transition" 
                    title="Edit Stok Minimum">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                </button>
            </div>
        </div>
        <div class="bg-gray-50 rounded-xl p-3 sm:p-4 text-center">
            <p class="text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Satuan</p>
            <p class="text-base sm:text-lg font-bold text-gray-800">{{ $logistik->itemLogistik->satuan }}</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-3 sm:p-4 text-center">
            <p class="text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Terakhir Update</p>
            @php
                $lastIn = $logistik->pemasukanLogistiks()->latest('tanggal')->first();
                $lastOut = $logistik->pengeluaranLogistiks()->latest('tanggal')->first();
                $lastDate = null;
                if ($lastIn && $lastOut) {
                    $lastDate = $lastIn->tanggal > $lastOut->tanggal ? $lastIn->tanggal : $lastOut->tanggal;
                } elseif ($lastIn) {
                    $lastDate = $lastIn->tanggal;
                } elseif ($lastOut) {
                    $lastDate = $lastOut->tanggal;
                }
            @endphp
            <p class="text-base sm:text-lg font-bold text-gray-800">{{ $lastDate ? \Carbon\Carbon::parse($lastDate)->format('d/m/Y') : '-' }}</p>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5">
    {{-- Tab Header --}}
    <div class="overflow-x-auto border-b border-gray-200 mb-5">
        <div class="flex min-w-max">
            <button onclick="switchLogistikTab('pemasukan')" id="tab-pemasukan-btn"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-green-600 border-b-2 border-green-600 -mb-px transition">
                <i class="fa-solid fa-arrow-trend-up text-green-500"></i> Riwayat Pemasukan
            </button>
            <button onclick="switchLogistikTab('pengeluaran')" id="tab-pengeluaran-btn"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-gray-700 border-b-2 border-transparent -mb-px transition">
                <i class="fa-solid fa-arrow-trend-down text-red-500"></i> Riwayat Pengeluaran
            </button>
        </div>
    </div>

    {{-- Tab: Riwayat Pemasukan --}}
    <div id="tab-pemasukan" class="tab-logistik-panel">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <h3 class="font-bold text-gray-900">Riwayat Pemasukan</h3>
            <button onclick="document.getElementById('modal-pemasukan').classList.remove('hidden')"
                class="bg-green-600 hover:bg-green-700 text-white rounded-full px-4 py-2 text-xs font-semibold flex items-center justify-center gap-1.5 transition w-full sm:w-auto">
                <i class="fa-solid fa-plus"></i> Tambah Pemasukan
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[500px]">
                <thead class="bg-green-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-green-700">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-green-700">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-green-700">Pengaju</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-green-700">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayatMasuk as $r)
                    <tr class="hover:bg-green-50">
                        <td class="px-4 py-3 text-gray-800 font-medium">{{ $r->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-semibold text-green-600">+{{ $r->jumlah }} <span class="font-normal text-gray-500">{{ $logistik->itemLogistik->satuan }}</span></td>
                        <td class="px-4 py-3 text-gray-600">{{ $r->pengajuUser?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $r->donasi_id ? 'donasi' : ($r->keterangan ?? '-') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center">
                            <i class="fa-solid fa-arrow-down text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">Belum ada riwayat pemasukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tab: Riwayat Pengeluaran --}}
    <div id="tab-pengeluaran" class="tab-logistik-panel hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <h3 class="font-bold text-gray-900">Riwayat Pengeluaran</h3>
            <button onclick="document.getElementById('modal-pengeluaran').classList.remove('hidden')"
                class="bg-red-600 hover:bg-red-700 text-white rounded-full px-4 py-2 text-xs font-semibold flex items-center justify-center gap-1.5 transition w-full sm:w-auto">
                <i class="fa-solid fa-plus"></i> Tambah Pengeluaran
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-red-700">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-red-700">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-red-700">Pengaju</th>
                        @if($logistik->itemLogistik->jenisLogistik->nama_jenis_logistik === 'Obat')
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-red-700">Warga Griya</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-red-700">Aturan Minum</th>
                        @else
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-red-700">Keterangan</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayatKeluar as $r)
                    <tr class="hover:bg-red-50">
                        <td class="px-4 py-3 text-gray-800 font-medium">{{ $r->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-semibold text-red-600">-{{ $r->jumlah }} <span class="font-normal text-gray-500">{{ $logistik->itemLogistik->satuan }}</span></td>
                        <td class="px-4 py-3 text-gray-600">{{ $r->pengajuUser?->name ?? '-' }}</td>
                        @if($logistik->itemLogistik->jenisLogistik->nama_jenis_logistik === 'Obat')
                        <td class="px-4 py-3 text-gray-600">{{ $r->wargaBinaan?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $r->aturan_minum ?? '-' }}</td>
                        @else
                        <td class="px-4 py-3 text-gray-600">{{ $r->keterangan ?? '-' }}</td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $logistik->itemLogistik->jenisLogistik->nama_jenis_logistik === 'Obat' ? 5 : 4 }}" class="px-4 py-12 text-center">
                            <i class="fa-solid fa-arrow-up text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">Belum ada riwayat pengeluaran</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Tambah Pemasukan --}}
<div id="modal-pemasukan" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-bold text-lg text-gray-900">Tambah Pemasukan Stok</h3>
            <button onclick="document.getElementById('modal-pemasukan').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <p class="text-sm text-gray-400 mb-5">Catat pemasukan stok baru</p>

        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'logistik.store-pemasukan', $logistik) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Tanggal Pemasukan</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Jumlah Pemasukan</label>
                <input type="number" name="jumlah" min="1" value="0" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Contoh: Pembelian, Donasi, dll..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-pemasukan').classList.add('hidden')"
                    class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition">Batal</button>
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Tambah Pengeluaran --}}
<div id="modal-pengeluaran" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-bold text-lg text-gray-900">Tambah Pengeluaran Stok</h3>
            <button onclick="document.getElementById('modal-pengeluaran').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <p class="text-sm text-gray-400 mb-5">Catat pengeluaran stok baru</p>

        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'logistik.store-pengeluaran', $logistik) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Tanggal Pengeluaran</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Jumlah Pengeluaran</label>
                <input type="number" name="jumlah" min="1" max="{{ $logistik->jumlah_saat_ini }}" value="0" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            @if($logistik->itemLogistik->jenisLogistik->nama_jenis_logistik === 'Obat')
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Warga Griya yang Menggunakan</label>
                <div class="relative">
                    <select name="warga_binaan_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 appearance-none bg-white">
                        <option value="">Cari nama warga griya</option>
                        @foreach($wargaBinaans as $wb)
                        <option value="{{ $wb->id }}">{{ $wb->nama }}</option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Aturan Minum</label>
                <input type="text" name="aturan_minum" placeholder="2 Kali Sehari 1 tablet" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            @else
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Contoh: Distribusi ke warga, dll..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>
            @endif

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-pengeluaran').classList.add('hidden')"
                    class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Edit Stok Minimum --}}
<div id="modal-edit-minimum" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-bold text-lg text-gray-900">Edit Stok Minimum</h3>
            <button onclick="document.getElementById('modal-edit-minimum').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <p class="text-sm text-gray-400 mb-5">Atur ambang batas stok minimum untuk item ini</p>

        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'logistik.update-stok', $logistik) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Stok Minimum Baru</label>
                <input type="number" name="jumlah_minimum" min="0" value="{{ $logistik->jumlah_minimum }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-minimum').classList.add('hidden')"
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
function switchLogistikTab(tab) {
    document.querySelectorAll('.tab-logistik-panel').forEach(function(p) {
        p.classList.add('hidden');
    });

    const btnPem = document.getElementById('tab-pemasukan-btn');
    const btnPen = document.getElementById('tab-pengeluaran-btn');

    btnPem.classList.remove('font-semibold', 'text-green-600', 'border-green-600');
    btnPem.classList.add('font-medium', 'text-gray-400', 'border-transparent');

    btnPen.classList.remove('font-semibold', 'text-red-600', 'border-red-600');
    btnPen.classList.add('font-medium', 'text-gray-400', 'border-transparent');

    document.getElementById('tab-' + tab).classList.remove('hidden');

    const activeBtn = document.getElementById('tab-' + tab + '-btn');
    activeBtn.classList.remove('font-medium', 'text-gray-400', 'border-transparent');
    
    if (tab === 'pemasukan') {
        activeBtn.classList.add('font-semibold', 'text-green-600', 'border-green-600');
    } else {
        activeBtn.classList.add('font-semibold', 'text-red-600', 'border-red-600');
    }
}
</script>
@endpush
