@extends('layout.app')

@section('title', 'Detail Obat — ' . $stokObat->nama_obat)

@section('content')

{{-- Header --}}
<h1 class="text-2xl font-bold text-gray-900 mb-1">Detail Monitoring</h1>
<p class="text-gray-400 text-sm mb-5">{{ $wargaBinaan->nama }}</p>

{{-- Back --}}
<a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.show', $wargaBinaan) }}"
    class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 transition mb-5">
    <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar
</a>

{{-- Profile Card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
    <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-5">

        {{-- Foto --}}
        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0 ring-4 ring-gray-50">
            @if($wargaBinaan->foto)
            <img src="{{ Storage::url($wargaBinaan->foto) }}" alt="{{ $wargaBinaan->nama }}" class="w-full h-full object-cover">
            @else
            <i class="fa-solid fa-user text-gray-300 text-3xl"></i>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0 w-full">
            <p class="font-bold text-xl text-gray-900">{{ $wargaBinaan->nama }}</p>
            <div class="flex items-center justify-center sm:justify-start gap-2 mt-1.5 mb-4 flex-wrap">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-600 text-white">
                    {{ $wargaBinaan->kategori }}
                </span>
                <span class="text-sm text-gray-500">{{ $wargaBinaan->umur }} Tahun</span>
                @include('components.badge-status', ['status' => $wargaBinaan->status])
            </div>

            {{-- Riwayat Penyakit --}}
            @php $aktifPenyakit = $wargaBinaan->riwayatPenyakits->where('status', 'Aktif'); @endphp
            @if($aktifPenyakit->isNotEmpty())
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2 mt-4">Riwayat Penyakit</p>
            <div class="flex flex-wrap gap-2 items-center justify-center sm:justify-start">
                @foreach($aktifPenyakit as $rp)
                <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                    {{ $rp->nama_penyakit }}
                    <span class="text-red-400 text-[10px] ml-0.5">Aktif</span>
                </span>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Tabs Navigation --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
    <div class="overflow-x-auto border-b border-gray-200 mb-4">
        <div class="flex min-w-max">
            <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.show', $wargaBinaan) }}"
                class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-gray-700 border-b-2 border-transparent -mb-px transition whitespace-nowrap">
                <i class="fa-solid fa-wave-square mr-1.5"></i> Pemeriksaan Kesehatan
            </a>
            <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.show', $wargaBinaan) }}"
                class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-gray-700 border-b-2 border-transparent -mb-px transition whitespace-nowrap">
                <i class="fa-solid fa-brain mr-1.5"></i> Pemeriksaan RSJ
            </a>
            <span class="px-4 py-2.5 text-sm font-semibold text-red-600 border-b-2 border-red-600 -mb-px whitespace-nowrap">
                <i class="fa-solid fa-pills mr-1.5"></i> Pencatatan Obat
            </span>
        </div>
    </div>

    {{-- Back to Medicine List --}}
    <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.show', $wargaBinaan) }}?tab=obat"
        class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 transition mb-5">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar Obat
    </a>

    {{-- Medicine Detail Card --}}
    <div class="bg-gray-50 rounded-xl p-5 mb-6">
        <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-pills text-red-600 text-xl"></i>
            </div>
            <div class="flex-1 w-full">
                <div class="flex items-center justify-center sm:justify-start gap-3 flex-wrap mb-2">
                    <h2 class="font-bold text-xl text-gray-900">{{ $stokObat->nama_obat }}</h2>

                    {{-- Status Badge --}}
                    @php
                    $statusBadge = match($stokObat->status) {
                        'AKTIF' => 'bg-green-100 text-green-700',
                        'HABIS' => 'bg-orange-100 text-orange-700',
                        'SEMBUH' => 'bg-gray-100 text-gray-700 border border-gray-300',
                        default => 'bg-gray-100 text-gray-600'
                    };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                        {{ $stokObat->status }}
                        <button type="button" onclick="document.getElementById('modal-update-status').classList.remove('hidden')" class="ml-1 text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-pen text-[9px]"></i>
                        </button>
                    </span>

                    {{-- Asal Obat Badge --}}
                    @php
                    $asalBadge = ($stokObat->asal_obat ?? 'OBAT_PERIKSA') === 'OBAT_GRIYA'
                        ? 'bg-red-600 text-white'
                        : 'bg-white text-red-600 border border-red-600';
                    $asalText = ($stokObat->asal_obat ?? 'OBAT_PERIKSA') === 'OBAT_GRIYA' ? 'OBAT GRIYA' : 'OBAT PERIKSA';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $asalBadge }}">
                        {{ $asalText }}
                    </span>
                </div>

                {{-- Status Dropdown (hidden by default, shown when clicked) --}}
                <div id="status-dropdown" class="hidden mb-3">
                    <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.updateStatusObat', [$wargaBinaan, $stokObat]) }}" class="flex items-center justify-center sm:justify-start gap-2">
                        @csrf
                        @method('PUT')
                        <select name="status" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="AKTIF" {{ $stokObat->status === 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                            <option value="SEMBUH" {{ $stokObat->status === 'SEMBUH' ? 'selected' : '' }}>Sembuh</option>
                        </select>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">Simpan</button>
                    </form>
                </div>

                {{-- Medicine Info Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Jenis</p>
                        <p class="text-gray-800 font-medium">{{ $stokObat->bentuk_obat ?? $stokObat->satuan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Jumlah Awal</p>
                        <p class="text-gray-800 font-medium">{{ $stokObat->jumlah_awal }} {{ $stokObat->bentuk_obat ?? $stokObat->satuan ?? '' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Sisa Obat</p>
                        <p class="text-gray-800 font-medium">{{ $stokObat->sisa }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Terakhir Update</p>
                        <p class="text-gray-800 font-medium">{{ $stokObat->tgl_update ? $stokObat->tgl_update->format('d/m/Y') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Catat Minum Obat Section --}}
    <div class="bg-white rounded-xl border border-gray-100">
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Catat Minum Obat</h3>
            <button onclick="document.getElementById('modal-pengeluaran').classList.remove('hidden')"
                class="bg-red-600 hover:bg-red-700 text-white rounded-full px-4 py-2 text-xs font-semibold flex items-center gap-1.5 transition">
                <i class="fa-solid fa-plus"></i> Tambah Pengeluaran
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengeluarans as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800 font-medium">{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-red-600 font-medium">-{{ $p->jumlah }} {{ $stokObat->bentuk_obat ?? $stokObat->satuan ?? '' }}</td>
                        <td class="px-4 py-3 text-right">
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-12 text-center">
                            <i class="fa-solid fa-clipboard-list text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">Belum ada catatan pengeluaran obat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Update Status --}}
<div id="modal-update-status" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-lg text-gray-900">Ubah Status Obat</h3>
            <button onclick="document.getElementById('modal-update-status').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.updateStatusObat', [$wargaBinaan, $stokObat]) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                <div class="relative">
                    <select name="status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
                        <option value="AKTIF" {{ $stokObat->status === 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                        <option value="SEMBUH" {{ $stokObat->status === 'SEMBUH' ? 'selected' : '' }}>Sembuh</option>
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-update-status').classList.add('hidden')"
                    class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Catat Minum Obat (Pengeluaran) --}}
<div id="modal-pengeluaran" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-lg text-gray-900">Catat Minum Obat</h3>
            <button onclick="document.getElementById('modal-pengeluaran').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.storePengeluaranObat', [$wargaBinaan, $stokObat]) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Minum Obat</label>
                <input type="number" name="jumlah" min="1" max="{{ $stokObat->sisa }}" value="2" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Update</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-pengeluaran').classList.add('hidden')"
                    class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection
