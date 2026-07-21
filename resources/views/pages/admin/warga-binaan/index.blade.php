@extends('layout.app')

@section('title', 'Data Warga Binaan')

@section('content')

@include('sections.page-header', ['title' => 'Data Warga Binaan', 'subtitle' => 'Kelola data ODGJ dan Lansia'])

{{-- Toolbar --}}
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">

    {{-- Filter --}}
    <form
        method="GET"
        action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.index') }}"
        id="filterForm"
        class="w-full">

        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="flex flex-col sm:flex-row gap-3">

            {{-- Search --}}
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

                <input
                    type="text"
                    name="search"
                    id="searchInput"
                    value="{{ request('search') }}"
                    placeholder="Cari Nama..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            {{-- Filter Status --}}
            <div class="relative sm:w-48">
                <select
                    name="status_filter"
                    id="statusFilter"
                    class="w-full border border-gray-200 rounded-lg pl-3 pr-8 py-2 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white text-gray-600">

                    <option value="">Semua</option>
                    <option value="Aktif" {{ request('status_filter') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Meninggal" {{ request('status_filter') == 'Meninggal' ? 'selected' : '' }}>Meninggal</option>
                    <option value="Kabur" {{ request('status_filter') == 'Kabur' ? 'selected' : '' }}>Kabur</option>
                    <option value="Selesai Pembinaan" {{ request('status_filter') == 'Selesai Pembinaan' ? 'selected' : '' }}>
                        Selesai Pembinaan
                    </option>

                </select>

                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </span>
            </div>

        </div>

    </form>

    {{-- Tombol --}}
    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.index', array_merge(request()->query(), ['export' => 'pdf'])) }}"
            class="border border-red-600 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm flex items-center justify-center gap-2 transition">

            <i class="fa-solid fa-file-pdf"></i>
            Ekspor PDF

        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.index', array_merge(request()->query(), ['export' => 'excel'])) }}"
            class="border border-red-600 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm flex items-center justify-center gap-2 transition">

            <i class="fa-solid fa-file-excel"></i>
            Ekspor Excel

        </a>

        <button
            onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
            class="bg-red-600 hover:bg-red-700 text-white rounded-lg px-5 py-2 text-sm font-semibold flex items-center justify-center gap-2 transition">

            <i class="fa-solid fa-plus"></i>
            Tambah Warga

        </button>

    </div>

</div>

{{-- Tab Pill --}}
<div class="overflow-x-auto mb-4">
    <div class="bg-gray-100 p-1 rounded-xl inline-flex gap-1 whitespace-nowrap min-w-max">

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.index', array_merge(request()->except('tab', 'page'), ['tab' => 'ODGJ'])) }}"
            class="px-5 py-2 rounded-lg text-sm font-medium transition
            {{ $tab === 'ODGJ' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
            Griya PMI Peduli (ODGJ)
        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.index', array_merge(request()->except('tab', 'page'), ['tab' => 'Lansia'])) }}"
            class="px-5 py-2 rounded-lg text-sm font-medium transition
            {{ $tab === 'Lansia' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
            Griya PMI Bahagia (Lansia)
        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.index', array_merge(request()->except('tab', 'page'), ['tab' => 'Lansia ODGJ'])) }}"
            class="px-5 py-2 rounded-lg text-sm font-medium transition
            {{ $tab === 'Lansia ODGJ' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
            Lansia ODGJ
        </a>

    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Foto</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Nama</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">TTL</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Umur</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Status</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Tgl Masuk</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($wargaBinaans as $warga)
            <tr class="hover:bg-gray-50">
                {{-- Foto --}}
                <td class="px-4 py-4">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if($warga->foto)
                            <img src="{{ Storage::url($warga->foto) }}" alt="{{ $warga->nama }}" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-user text-gray-400 text-sm"></i>
                        @endif
                    </div>
                </td>
                {{-- Nama --}}
                <td class="px-4 py-4">
                    <p class="font-semibold text-gray-900">{{ $warga->nama }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $warga->nik }}</p>
                </td>
                {{-- TTL --}}
                <td class="hidden md:table-cell px-4 py-4 text-gray-600 text-sm">
                    {{ $warga->tempat_lahir }},<br>
                    <span class="text-xs">{{ $warga->tgl_lahir->format('d/m/Y') }}</span>
                </td>
                {{-- Umur --}}
                <td class="hidden md:table-cell px-4 py-4 text-gray-900">
                    <span class="font-semibold">{{ $warga->umur }}</span>
                    <span class="text-xs text-gray-400"> thn</span>
                </td>
                {{-- Status --}}
                <td class="hidden md:table-cell px-4 py-4">
                    @include('components.badge-status', ['status' => $warga->status])
                </td>
                {{-- Tgl Masuk --}}
                <td class="hidden md:table-cell px-4 py-4 text-gray-600 text-sm">
                    {{ $warga->tgl_masuk->format('d/m/Y') }}
                </td>
                {{-- Aksi --}}
                <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                        <button type="button" title="Detail"
                            class="text-gray-400 hover:text-blue-500 transition"
                            onclick="openDetailWarga(this)"
                            data-id="{{ $warga->id }}"
                            data-nama="{{ $warga->nama }}"
                            data-nik="{{ $warga->nik }}"
                            data-kategori="{{ $warga->kategori }}"
                            data-status="{{ $warga->status }}"
                            data-jenis_kelamin="{{ $warga->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}"
                            data-ttl="{{ $warga->tempat_lahir }}, {{ $warga->tgl_lahir->format('d M Y') }}"
                            data-umur="{{ $warga->umur }} tahun"
                            data-masuk="{{ $warga->tgl_masuk->format('d M Y') }}"
                            data-bpjs="{{ $warga->no_bpjs ?: '-' }}"
                            data-alamat="{{ $warga->alamat ?: '-' }}"
                            data-catatan="{{ $warga->catatan ?: '-' }}"
                            data-pj="{{ $warga->penanggung_jawab ?: '-' }}"
                            data-kontak_pj="{{ $warga->kontak_pj ?: '-' }}"
                            data-foto="{{ $warga->foto ? Storage::url($warga->foto) : '' }}"
                            data-url="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.show', $warga) }}">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <button type="button" title="Edit"
                            class="text-gray-400 hover:text-yellow-500 transition"
                            onclick="openEditWarga(this)"
                            data-id="{{ $warga->id }}"
                            data-nik="{{ $warga->nik }}"
                            data-nama="{{ $warga->nama }}"
                            data-tempat_lahir="{{ $warga->tempat_lahir }}"
                            data-tgl_lahir="{{ $warga->tgl_lahir->format('Y-m-d') }}"
                            data-jenis_kelamin="{{ $warga->jenis_kelamin }}"
                            data-tgl_masuk="{{ $warga->tgl_masuk->format('Y-m-d') }}"
                            data-status="{{ $warga->status }}"
                            data-no_bpjs="{{ $warga->no_bpjs }}"
                            data-alamat="{{ $warga->alamat }}"
                            data-penanggung_jawab="{{ $warga->penanggung_jawab }}"
                            data-kontak_pj="{{ $warga->kontak_pj ?: '-' }}"
                            data-catatan="{{ $warga->catatan }}"
                            data-kategori="{{ $warga->kategori }}"
                            data-update_url="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.update', $warga) }}">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        {{-- Hapus: hanya Superadmin --}}
                        @hasrole('superadmin')
                        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.destroy', $warga) }}"
                            onsubmit="return confirm('Hapus data warga ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        @endhasrole
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center">
                    <i class="fa-solid fa-users text-4xl text-gray-200 mb-3 block"></i>
                    <p class="text-gray-400 text-sm">Belum ada data warga binaan {{ $tab }}</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $wargaBinaans->links() }}</div>
</div>

{{-- Modal Detail --}}
<div id="modal-detail" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
            <h3 class="font-bold text-lg text-gray-900">Detail Warga Binaan</h3>
            <button onclick="document.getElementById('modal-detail').classList.add('hidden')"
                class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-500 hover:bg-red-100 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="px-6 py-5 space-y-5">

            {{-- Foto + Nama + Badge --}}
            <div class="flex items-center gap-4">
                <div id="d-foto-wrap"
                    class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <img id="d-foto" src="" alt="" class="hidden w-full h-full object-cover">
                    <i id="d-foto-icon" class="fa-solid fa-user text-gray-300 text-2xl"></i>
                </div>
                <div>
                    <p id="d-nama" class="font-bold text-xl text-gray-900"></p>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        <span id="d-kategori"
                            class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-800 text-white"></span>
                        <span id="d-status" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                    </div>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">NIK</p>
                    <p id="d-nik" class="font-medium text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">TTL</p>
                    <p id="d-ttl" class="font-medium text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Umur</p>
                    <p id="d-umur" class="font-medium text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Tgl Masuk</p>
                    <p id="d-masuk" class="font-medium text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Jenis Kelamin</p>
                    <p id="d-jk" class="font-medium text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">No. BPJS</p>
                    <p id="d-bpjs" class="font-medium text-gray-800"></p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-0.5">Alamat</p>
                    <p id="d-alamat" class="font-medium text-gray-800 leading-relaxed"></p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-0.5">Catatan</p>
                    <p id="d-catatan" class="font-medium text-gray-800 leading-relaxed"></p>
                </div>
            </div>

            {{-- Penanggung Jawab --}}
            <div class="border-t border-gray-100 pt-4">
                <p class="font-semibold text-gray-900 mb-3">Penanggung Jawab</p>
                <div class="text-sm mb-2">
                    <p class="text-xs text-gray-400 mb-0.5">Nama PJ</p>
                    <p id="d-pj" class="font-medium text-gray-800"></p>
                </div>
                <div class="text-sm">
                    <p class="text-xs text-gray-400 mb-0.5">Kontak PJ</p>
                    <p id="d-kontak_pj" class="font-medium text-gray-800"></p>
                </div>
            </div>

            {{-- Footer tombol --}}
            <div class="flex justify-end gap-3 pt-1">
                <button onclick="document.getElementById('modal-detail').classList.add('hidden')"
                    class="border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-lg px-4 py-2 text-sm transition">
                    Tutup
                </button>
                <a id="d-link-monitoring" href="#"
                    class="bg-red-600 hover:bg-red-700 text-white rounded-lg px-4 py-2 text-sm font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-heart-pulse"></i> Lihat Monitoring
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div id="modal-tambah" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-lg text-gray-900">Tambah Warga Binaan</h3>
                <p class="text-sm text-gray-400 mt-0.5">Isi data warga binaan dengan lengkap</p>
            </div>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 mt-0.5">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'warga-binaan.store') }}" enctype="multipart/form-data"
            class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="kategori" value="{{ $tab }}">

            {{-- Upload Foto --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto</label>
                <div class="relative">
                    <input type="file" name="foto" accept="image/*"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-500
                               file:mr-3 file:py-0.5 file:px-0 file:border-0 file:bg-transparent file:text-sm file:text-gray-500
                               focus:outline-none focus:ring-2 focus:ring-red-500 cursor-pointer">
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-300">
                        <i class="fa-solid fa-upload text-sm"></i>
                    </span>
                </div>
            </div>

            {{-- NIK --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                <input type="text" name="nik" required maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            {{-- Nama Lengkap --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            {{-- Tempat & Tanggal Lahir --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <div class="relative">
                        <input type="date" name="tgl_lahir" required
                            @if($tab === 'Lansia ODGJ') max="{{ now()->subYears(60)->format('Y-m-d') }}" @endif
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
            </div>

            {{-- Jenis Kelamin --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <div class="relative">
                    <select name="jenis_kelamin" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white text-gray-700">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Alamat --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="3" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>

            {{-- Status & No. BPJS --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <div class="relative">
                        <select name="status" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white text-gray-700">
                            <option value="Aktif">Aktif</option>
                            <option value="Meninggal">Meninggal</option>
                            <option value="Kabur">Kabur</option>
                            <option value="Selesai Pembinaan">Selesai Pembinaan</option>
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. BPJS</label>
                    <input type="text" name="no_bpjs" placeholder="0001234567890"maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 placeholder-gray-300">
                </div>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea name="catatan" rows="3"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>

            {{-- Tanggal Masuk --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                <div class="relative">
                    <input type="date" name="tgl_masuk" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            {{-- Penanggung Jawab --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penanggung Jawab</label>
                    <input type="text" name="penanggung_jawab"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Penanggung Jawab</label>
                    <input type="text" name="kontak_pj" placeholder="08xxxxxxxxxx" maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 placeholder-gray-300">
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 mt-2">
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                    class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-lg text-gray-900">Edit Warga Binaan</h3>
                <p class="text-sm text-gray-400 mt-0.5">Perbarui data warga binaan</p>
            </div>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 mt-0.5">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="form-edit" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="kategori" id="edit-kategori">

            {{-- Upload Foto --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Upload Foto <span class="text-gray-400 font-normal text-xs">(kosongkan jika tidak diganti)</span>
                </label>
                <div class="relative">
                    <input type="file" name="foto" accept="image/*"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-500
                               file:mr-3 file:py-0.5 file:px-0 file:border-0 file:bg-transparent file:text-sm file:text-gray-500
                               focus:outline-none focus:ring-2 focus:ring-red-500 cursor-pointer">
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-300">
                        <i class="fa-solid fa-upload text-sm"></i>
                    </span>
                </div>
            </div>

            {{-- NIK --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                <input type="text" name="nik" id="edit-nik" required maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            {{-- Nama Lengkap --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" id="edit-nama" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            {{-- Tempat & Tanggal Lahir --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="edit-tempat_lahir" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" id="edit-tgl_lahir" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            {{-- Jenis Kelamin --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <div class="relative">
                    <select name="jenis_kelamin" id="edit-jenis_kelamin" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white text-gray-700">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Alamat --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" id="edit-alamat" rows="3" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>

            {{-- Status & No. BPJS --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <div class="relative">
                        <select name="status" id="edit-status" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white text-gray-700">
                            <option value="Aktif">Aktif</option>
                            <option value="Meninggal">Meninggal</option>
                            <option value="Kabur">Kabur</option>
                            <option value="Selesai Pembinaan">Selesai Pembinaan</option>
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. BPJS</label>
                    <input type="text" name="no_bpjs" id="edit-no_bpjs" placeholder="0001234567890" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 placeholder-gray-300">
                </div>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea name="catatan" id="edit-catatan" rows="3"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>

            {{-- Tanggal Masuk --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                <input type="date" name="tgl_masuk" id="edit-tgl_masuk" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            {{-- Penanggung Jawab --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penanggung Jawab</label>
                    <input type="text" name="penanggung_jawab" id="edit-penanggung_jawab"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Penanggung Jawab</label>
                    <input type="text" name="kontak_pj" id="edit-kontak_pj" placeholder="08xxxxxxxxxx" maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 placeholder-gray-300">
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 mt-2">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openDetailWarga(btn) {
    const d = btn.dataset;
    console.log(d.status);

    // Nama, NIK, Kategori
    document.getElementById('d-nama').textContent    = d.nama;
    document.getElementById('d-nik').textContent     = d.nik;
    document.getElementById('d-ttl').textContent     = d.ttl;
    document.getElementById('d-umur').textContent    = d.umur;
    document.getElementById('d-masuk').textContent   = d.masuk;
    document.getElementById('d-jk').textContent      = d.jenis_kelamin;
    document.getElementById('d-bpjs').textContent    = d.bpjs;
    document.getElementById('d-alamat').textContent  = d.alamat;
    document.getElementById('d-catatan').textContent = d.catatan;
    document.getElementById('d-pj').textContent      = d.pj;
    document.getElementById('d-kategori').textContent = d.kategori;
    document.getElementById('d-kontak_pj').textContent = d.kontak_pj;

    // Foto
    const img  = document.getElementById('d-foto');
    const icon = document.getElementById('d-foto-icon');
    if (d.foto) {
        img.src = d.foto;
        img.classList.remove('hidden');
        icon.classList.add('hidden');
    } else {
        img.src = '';
        img.classList.add('hidden');
        icon.classList.remove('hidden');
    }

    // Status badge — warna sesuai badge-status.blade.php
    const statusEl = document.getElementById('d-status');
    const statusMap = {
        'Aktif'             : ['bg-green-100 text-green-700',   'fa-circle-check',          'Aktif'],
        'Selesai Pembinaan' : ['bg-gray-100 text-gray-600',     'fa-circle-check',          'Selesai Pembinaan'],
        'Meninggal'         : ['bg-red-100 text-red-700',       'fa-circle-xmark',          'Meninggal'],
        'Kabur'            : ['bg-orange-100 text-orange-700', 'fa-circle-question',       'Kabur'],
    };
    const cfg = statusMap[d.status] ?? ['bg-gray-100 text-gray-600', 'fa-circle', d.status];
    statusEl.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold ' + cfg[0];
    statusEl.innerHTML = '<i class="fa-solid ' + cfg[1] + ' text-xs"></i> ' + cfg[2];

    // Link monitoring
    document.getElementById('d-link-monitoring').href = d.url;

    document.getElementById('modal-detail').classList.remove('hidden');
}

function openEditWarga(btn) {
    const d = btn.dataset;
    document.getElementById('form-edit').action = d.update_url;
    document.getElementById('edit-nik').value              = d.nik;
    document.getElementById('edit-nama').value             = d.nama;
    document.getElementById('edit-tempat_lahir').value     = d.tempat_lahir;
    
    const tglLahirInput = document.getElementById('edit-tgl_lahir');
    tglLahirInput.value = d.tgl_lahir;
    if (d.kategori === 'Lansia ODGJ') {
        const today = new Date();
        const maxDate = new Date(today.getFullYear() - 60, today.getMonth(), today.getDate());
        const maxDateString = maxDate.toISOString().split('T')[0];
        tglLahirInput.setAttribute('max', maxDateString);
    } else {
        tglLahirInput.removeAttribute('max');
    }

    document.getElementById('edit-jenis_kelamin').value    = d.jenis_kelamin;
    document.getElementById('edit-tgl_masuk').value        = d.tgl_masuk;
    document.getElementById('edit-status').value           = d.status;
    document.getElementById('edit-no_bpjs').value          = d.no_bpjs ?? '';
    document.getElementById('edit-alamat').value           = d.alamat;
    document.getElementById('edit-penanggung_jawab').value = d.penanggung_jawab ?? '';
    document.getElementById('edit-catatan').value          = d.catatan ?? '';
    document.getElementById('edit-kategori').value         = d.kategori;
    document.getElementById('edit-kontak_pj').value        = d.kontak_pj ?? '';
    document.getElementById('modal-edit').classList.remove('hidden');
}

let timer;

document.getElementById('searchInput').addEventListener('keyup', function () {

    clearTimeout(timer);

    timer = setTimeout(function () {
        document.getElementById('filterForm').submit();
    }, 500); // delay 500ms
});

document.getElementById('statusFilter').addEventListener('change', function () {
    document.getElementById('filterForm').submit();
});
</script>
@endpush
