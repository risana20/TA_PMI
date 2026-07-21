@extends('layout.app')

@section('title', 'Kunjungan')

@section('content')

@include('sections.page-header', ['title' => 'Kunjungan', 'subtitle' => 'Kelola pengajuan kunjungan'])

<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">

    <form method="GET"
        action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'kunjungan.index') }}"
        id="filterForm"
        class="w-full">

        <div class="flex flex-col sm:flex-row gap-3">

            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

                <input
                    type="text"
                    id="searchInput"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, no HP, instansi..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <select
                name="status"
                id="statusFilter"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">

                <option value="">Semua Status</option>
                <option value="PROSES" {{ request('status') == 'PROSES' ? 'selected' : '' }}>PROSES</option>
                <option value="DISETUJUI" {{ request('status') == 'DISETUJUI' ? 'selected' : '' }}>DISETUJUI</option>
                <option value="DITOLAK" {{ request('status') == 'DITOLAK' ? 'selected' : '' }}>DITOLAK</option>

            </select>

        </div>

    </form>

    
    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'kunjungan.export.pdf', request()->query()) }}"
            class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm font-medium flex items-center justify-center gap-2">

            <i class="fa-solid fa-download"></i>
            Ekspor PDF

        </a>

        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'kunjungan.export.excel', request()->query()) }}"
            class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm font-medium flex items-center justify-center gap-2">

            <i class="fa-solid fa-file-excel"></i>
            Ekspor Excel

        </a>

        <button
            onclick="openModal()"
            class="bg-red-600 hover:bg-red-700 text-white rounded-lg px-5 py-2 text-sm font-semibold flex items-center justify-center gap-2">

            <i class="fa-solid fa-plus"></i>
            Tambah Kunjungan

        </button>

    </div>

</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nama</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">No. HP</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Tujuan</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Instansi</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Tgl Kunjungan</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Jam</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Surat Pengajuan</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Keterangan</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Aksi</th>
                <th class="md:hidden  text-left text-xs font-semibold uppercase text-gray-400"> Detail</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @forelse($kunjungans as $kunjungan)
            <tr class="hover:bg-gray-50">

                <td class="px-4 py-4 font-medium text-gray-900">
                    {{ $kunjungan->nama_pengunjung }}
                </td>

                <td class="hidden md:table-cell px-4 py-4 text-gray-600">
                    {{ $kunjungan->no_hp }}
                </td>

                <td class="hidden md:table-cell px-4 py-4 text-gray-600">
                    <div>{{ $kunjungan->tujuan }}</div>
                    @if($kunjungan->wargaBinaan)
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-1 rounded text-xs font-semibold bg-red-50 text-red-700">
                            <i class="fa-solid fa-user text-[10px]"></i>
                            WBP: {{ $kunjungan->wargaBinaan->nama }}
                        </span>
                    @endif
                </td>

                <td class="hidden md:table-cell px-4 py-4 text-gray-600">
                    {{ $kunjungan->instansi ?? '-' }}
                </td>

                <td class="px-4 py-4 text-gray-600">
                    {{ $kunjungan->tgl_kunjungan->format('d M Y') }}
                </td>

                <td class="hidden md:table-cell px-4 py-4 text-gray-600">
                    {{ $kunjungan->jam }}
                </td>

                {{-- SURAT PENGAJUAN --}}
                <td class="hidden md:table-cell px-4 py-4 text-gray-600">

                    @if($kunjungan->surat_pengajuan)

                        <button
                            onclick="openSuratModal('{{ asset('storage/'.$kunjungan->surat_pengajuan) }}')"
                            class="text-blue-500 hover:text-blue-700">

                            <i class="fa-solid fa-eye"></i>

                        </button>

                    @else
                        <span class="text-gray-400">-</span>
                    @endif

                </td>

                <td class="px-4 py-4">
                    @include('components.badge-status', ['status' => $kunjungan->status])
                </td>

                <td class="hidden md:table-cell px-5 py-4 text-gray-600">
                    @if($kunjungan->status == 'DITOLAK')
                        {{ $kunjungan->alasan_tolak ?? '-' }}
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>

                <td class="hidden md:table-cell px-4 py-4">

                    @if($kunjungan->status === 'PROSES')

                        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'kunjungan.approve', $kunjungan) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-green-500 hover:text-green-700 text-xs font-medium mr-2">
                                Setujui
                            </button>
                        </form>

                        <button
                            onclick="openTolakModal({{ $kunjungan->id }})"
                            class="text-red-500 hover:text-red-700 text-xs font-medium">

                            Tolak

                        </button>

                    @endif

                </td>
                <td class="md:hidden px-4 py-4">
                    <button
                        onclick='openPreview(
                            @json($kunjungan->id),
                            @json($kunjungan->nama_pengunjung),
                            @json($kunjungan->no_hp),
                            @json($kunjungan->tujuan),
                            @json($kunjungan->instansi ?? "-"),
                            @json($kunjungan->tgl_kunjungan->format("d M Y")),
                            @json($kunjungan->jam),
                            @json($kunjungan->surat_pengajuan ? asset("storage/".$kunjungan->surat_pengajuan) : ""),
                            @json($kunjungan->status),
                            @json($kunjungan->alasan_tolak ?? "-"),
                            @json($kunjungan->wargaBinaan->nama ?? "-")
                        )'
                        class="text-blue-600 hover:text-blue-800">

                        <i class="fa-solid fa-eye"></i>

                    </button>
                </td>
                
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-8 text-center text-gray-400">
                        Belum ada data kunjungan
                    </td>
            </tr>

                @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $kunjungans->links() }}</div>
</div>

{{-- Modal Tolak --}}
<div id="modal-tolak" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Tolak Kunjungan</h3>
            <button onclick="document.getElementById('modal-tolak').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="form-tolak" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan</label>
                <textarea name="alasan_tolak" rows="3" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"
                    placeholder="Masukkan alasan penolakan..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-tolak').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg px-4 py-2 text-sm">Batal</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-4 py-2 text-sm">Tolak</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-surat" class="hidden fixed inset-0 bg-black/40 z-[60] flex items-center justify-center">

    <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6">

        <div class="flex justify-between items-center mb-4">

            <h3 class="font-bold text-lg">Surat Pengajuan</h3>

            <button onclick="closeSuratModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <iframe
             id="surat-frame"
            src=""
            class="w-full h-[500px] border rounded-lg">
        </iframe>

        <div class="flex justify-end gap-3 mt-4">

            <a id="download-surat"
               href=""
               download
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">

                Unduh Surat

            </a>

            <button
                onclick="closeSuratModal()"
                class="bg-gray-200 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">

                Tutup

            </button>

        </div>

    </div>

</div>

<div id="modal-tambah" class="hidden fixed inset-0 bg-black/40 z-50 flex items-start justify-center pt-28 p-4">

            <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-2xl max-h-[75vh] overflow-y-auto relative">
                <button onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                    class="absolute top-3 right-3 text-gray-400 hover:text-red-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Form Pengajuan Kunjungan</h2>
                    <p class="text-sm text-gray-400 mt-0.5">
                        Lengkapi data berikut untuk mengajukan permohonan kunjungan
                    </p>
                </div>

                <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'kunjungan.store') }}" enctype="multipart/form-data">
                    @csrf


                    <div class="space-y-5">
                        {{-- Nama Pengunjung --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pengunjung</label>
                            <input type="text"
                                name="nama_pengunjung"
                                value="{{ old('nama_pengunjung') }}"
                                placeholder="Masukkan nama pengunjung"
                                required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                        {{-- No HP --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                            <input type="text"
                                name="no_hp"
                                value="{{ old('no_hp') }}"
                                placeholder="Masukkan nomor HP"
                                required
                                maxlength="12"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
        
                        {{-- Tujuan Kunjungan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Kunjungan</label>

                            <div class="relative">
                                <select id="tujuan" name="tujuan" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">

                                    <option value="">Pilih tujuan...</option>

                                    <option value="Silaturahmi" {{ old('tujuan') === 'Silaturahmi' ? 'selected' : '' }}>
                                        Silaturahmi
                                    </option>

                                    <option value="Penelitian" {{ old('tujuan') === 'Penelitian' ? 'selected' : '' }}>
                                        Penelitian
                                    </option>

                                    <option value="Kerjasama" {{ old('tujuan') === 'Kerjasama' ? 'selected' : '' }}>
                                        Kerjasama
                                    </option>

                                    <option value="Magang/PKL" {{ old('tujuan') === 'Magang/PKL' ? 'selected' : '' }}>
                                        Magang / PKL
                                    </option>

                                    <option value="Lainnya" {{ old('tujuan') === 'Lainnya' ? 'selected' : '' }}>
                                        Lainnya
                                    </option>

                                </select>

                                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </span>
                            </div>
                        </div>

                        {{-- Instansi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Asal Instansi</label>

                            <input type="text"
                                name="instansi"
                                value="{{ old('instansi') }}"
                                placeholder="Contoh: Universitas X / Masyarakat Umum"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                        {{-- Mengunjungi WBP Checkbox --}}
                        <div class="mt-4">
                            <label class="inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" id="admin-mengunjungi-wbp-checkbox" name="mengunjungi_wbp" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-4 h-4" onchange="adminToggleWbpSearch(this.checked)" {{ old('mengunjungi_wbp') ? 'checked' : '' }}>
                                <span class="ml-2 text-sm font-semibold text-gray-700">Mengunjungi 1 orang warga binaan</span>
                            </label>
                        </div>

                        {{-- Warga Binaan Search Container --}}
                        <div id="admin-wbp-search-container" class="{{ old('mengunjungi_wbp') ? '' : 'hidden' }} mt-3 space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Nama Warga Binaan (Aktif)</label>
                            <div class="relative">
                                @php
                                    $oldAdminWbpName = '';
                                    if (old('warga_binaan_id')) {
                                        $oldAdminWbp = \App\Models\WargaBinaan::find(old('warga_binaan_id'));
                                        if ($oldAdminWbp) {
                                            $oldAdminWbpName = $oldAdminWbp->nama;
                                        }
                                    }
                                @endphp
                                <input type="text" id="admin-wbp-search-input" placeholder="Ketik nama warga binaan..." class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500" autocomplete="off" value="{{ $oldAdminWbpName }}" {{ old('warga_binaan_id') ? 'readonly' : '' }} {{ old('mengunjungi_wbp') ? 'required' : '' }}>
                                <input type="hidden" id="admin-wbp-id-input" name="warga_binaan_id" value="{{ old('warga_binaan_id') }}">
                                <button type="button" id="admin-clear-wbp-btn" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 {{ old('warga_binaan_id') ? '' : 'hidden' }}">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                {{-- Dropdown Suggestions --}}
                                <div id="admin-wbp-suggestions" class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                    {{-- Rendered via Javascript --}}
                                </div>
                            </div>
                        </div>

                        {{-- Upload Surat --}}
                        <div id="surat-field">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Upload Surat Kunjungan (Wajib Untuk tujuan Penelitian, Kerja Sama, Magang/PKL)
                            </label>

                            <label id="surat-upload" class="flex items-center justify-between border border-gray-200 rounded-lg px-3 py-2 cursor-pointer bg-white hover:border-gray-300 transition">
                                <span id="surat-label-text" class="text-sm text-gray-400 truncate">
                                    No file chosen *pdf/jpg/pnj
                                </span>
                                <i class="fa-solid fa-arrow-up-from-bracket text-gray-400 text-sm ml-2"></i>
                                <input type="file"
                                    name="surat_pengajuan"
                                    id="surat-input"
                                    accept=".pdf,image/*"
                                    class="hidden"
                                    onchange="previewSurat(this)">
                            </label>
                        </div>

                        {{-- Tanggal + Jam --}}
                        <div class="grid grid-cols-2 gap-4">

                            {{-- Tanggal --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Kunjungan
                                </label>

                                <input type="date" id="tgl-picker"
                                name="tgl_kunjungan"
                                required
                                class="w-full border rounded-lg px-3 py-2">
                            </div>

                            {{-- Jam --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Jam Kunjungan
                                </label>

                                <select name="jam" id="jam-select" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
                                    <option value="">Pilih Sesi...</option>
                                    <option value="Sesi 1: 08.00-09.30">Sesi 1: 08.00-09.30</option>
                                    <option value="Sesi 2: 09.30-11.00">Sesi 2: 09.30-11.00</option>
                                    <option value="Sesi 3: 11.00-12.30">Sesi 3: 11.00-12.30</option>
                                    <option value="Sesi 4: 13.00-14.30">Sesi 4: 13.00-14.30</option>
                                    <option value="Sesi 5: 14.30-16.00">Sesi 5: 14.30-16.00</option>
                                </select>
                            </div>

                        </div>

                        {{-- Info --}}
                        <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info text-red-500 text-sm"></i>

                            <p class="text-sm text-red-700">
                                <span class="font-semibold">Informasi:</span>
                                Jam operasional kunjungan dibagi menjadi 5 sesi per hari mulai dari <span class="font-semibold">08:00 – 16:00 WIB</span> dengan 1 sesi istirahat.
                            </p>
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-full text-sm transition flex items-center justify-center gap-2">

                            <i class="fa-solid fa-user-group"></i>
                            Tambahkan Kunjungan

                        </button>

                    </div>
                </form>
            </div>
        </div>
<div id="modal-preview" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center mb-5">
            <h3 class="text-xl font-bold text-gray-800">
                Detail Kunjungan
            </h3>

            <button onclick="closePreview()"
                class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">

            <div>
                <p class="text-gray-500">Nama Pengunjung</p>
                <p id="preview-nama" class="font-medium"></p>
            </div>

            <div>
                <p class="text-gray-500">No. HP</p>
                <p id="preview-nohp" class="font-medium"></p>
            </div>

            <div>
                <p class="text-gray-500">Tujuan</p>
                <p id="preview-tujuan" class="font-medium"></p>
            </div>

            <div>
                <p class="text-gray-500">Warga Binaan</p>
                <p id="preview-wbp" class="font-medium"></p>
            </div>

            <div>
                <p class="text-gray-500">Instansi</p>
                <p id="preview-instansi" class="font-medium"></p>
            </div>

            <div>
                <p class="text-gray-500">Tanggal</p>
                <p id="preview-tanggal" class="font-medium"></p>
            </div>

            <div>
                <p class="text-gray-500">Jam</p>
                <p id="preview-jam" class="font-medium"></p>
            </div>

            <div>
                <p class="text-gray-500">Status</p>
                <div id="preview-status"></div>
            </div>

        </div>

        <div class="mt-5">
            <p class="text-gray-500 text-sm mb-1">Surat Pengajuan</p>

            <div id="preview-surat"></div>
        </div>

        <div class="mt-5">
            <p class="text-gray-500 text-sm mb-1">Keterangan</p>

            <div id="preview-keterangan"
                class="bg-gray-50 rounded-lg p-3 text-sm">
            </div>
        </div>
        <div id="preview-actions" class="mt-6 hidden"> </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    const dbJadwalDisetujui = @json($jadwalDisetujui);
    console.log(dbJadwalDisetujui);
function openTolakModal(id) {
    let prefix = "{{ auth()->user()->hasRole('superadmin') ? 'superadmin' : 'admin' }}";
    document.getElementById('form-tolak').action =  '/' + prefix + '/kunjungan/' + id + '/reject';;
    document.getElementById('modal-tolak').classList.remove('hidden');
}
function openSuratModal(url){

    document.getElementById('surat-frame').src = url;

    document.getElementById('modal-surat').classList.remove('hidden');

}

function closeSuratModal(){

    document.getElementById('modal-surat').classList.add('hidden');

    document.getElementById('preview-surat').src = "";

}
let timer;

document.getElementById('searchInput').addEventListener('keyup', function () {

    clearTimeout(timer);

    timer = setTimeout(function () {
        document.getElementById('filterForm').submit();
    }, 500); // delay 0.5 detik supaya tidak reload tiap huruf
});

document.getElementById('statusFilter').addEventListener('change', function () {
    document.getElementById('filterForm').submit();
});

function initSurat() {
    const tujuan = document.getElementById('tujuan');
    const suratInput = document.getElementById('surat-input');
    const suratLabel = document.getElementById('surat-upload');

    if (!tujuan || !suratInput || !suratLabel) return;

    function updateSurat() {
        const value = tujuan.value;

        const aktif = (
            value === "Penelitian" ||
            value === "Kerjasama" ||
            value === "Magang/PKL"
        );

        if (aktif) {
            suratInput.disabled = false;
            suratInput.required = true;

            suratLabel.classList.remove('opacity-50');
            suratLabel.classList.remove('pointer-events-none');
        } else {
            suratInput.disabled = true;
            suratInput.required = false;
            suratInput.value = "";

            suratLabel.classList.add('opacity-50');
            suratLabel.classList.add('pointer-events-none');
        }
    }

    tujuan.addEventListener('change', updateSurat);
    updateSurat();
}

function openModal() {
    document.getElementById('modal-tambah').classList.remove('hidden');

    initSurat();
}

function previewSurat(input) {
    const label = document.getElementById('surat-label-text');

    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;

        // optional: ubah warna biar kelihatan aktif
        label.classList.remove('text-gray-400');
        label.classList.add('text-gray-700');
    }
}

// Admin WBP Autocomplete Search
let adminWbpTimeout = null;
const adminWbpSearchInput = document.getElementById('admin-wbp-search-input');
const adminWbpIdInput = document.getElementById('admin-wbp-id-input');
const adminWbpSuggestions = document.getElementById('admin-wbp-suggestions');
const adminClearWbpBtn = document.getElementById('admin-clear-wbp-btn');

function adminToggleWbpSearch(checked) {
    const container = document.getElementById('admin-wbp-search-container');
    if (checked) {
        container.classList.remove('hidden');
        adminWbpSearchInput.required = true;
    } else {
        container.classList.add('hidden');
        adminWbpSearchInput.required = false;
        adminClearWbpSelection();
    }
}

function adminClearWbpSelection() {
    adminWbpSearchInput.value = '';
    adminWbpIdInput.value = '';
    adminWbpSearchInput.readOnly = false;
    adminClearWbpBtn.classList.add('hidden');
    adminWbpSuggestions.innerHTML = '';
    adminWbpSuggestions.classList.add('hidden');
}

if (adminWbpSearchInput) {
    adminWbpSearchInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(adminWbpTimeout);
        
        if (query.length < 2) {
            adminWbpSuggestions.innerHTML = '';
            adminWbpSuggestions.classList.add('hidden');
            return;
        }
        
        adminWbpTimeout = setTimeout(() => {
            fetch(`/kunjungan/search-wbp?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    adminWbpSuggestions.innerHTML = '';
                    if (data.length === 0) {
                        adminWbpSuggestions.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Tidak ada warga binaan aktif ditemukan</div>';
                        adminWbpSuggestions.classList.remove('hidden');
                        return;
                    }
                    
                    data.forEach(wbp => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'w-full text-left px-4 py-2.5 hover:bg-red-50 text-sm text-gray-700 border-b last:border-0 border-gray-100 flex items-center justify-between transition';
                        btn.innerHTML = `
                            <span class="font-medium">${wbp.nama}</span>
                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">NIK: ${wbp.nik}</span>
                        `;
                        btn.addEventListener('click', () => {
                            adminSelectWbp(wbp.id, wbp.nama);
                        });
                        adminWbpSuggestions.appendChild(btn);
                    });
                    adminWbpSuggestions.classList.remove('hidden');
                });
        }, 300);
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!adminWbpSearchInput.contains(e.target) && !adminWbpSuggestions.contains(e.target)) {
            adminWbpSuggestions.classList.add('hidden');
        }
    });
}

function adminSelectWbp(id, name) {
    adminWbpSearchInput.value = name;
    adminWbpIdInput.value = id;
    adminWbpSearchInput.readOnly = true;
    adminWbpSuggestions.classList.add('hidden');
    adminClearWbpBtn.classList.remove('hidden');
}

if (adminClearWbpBtn) {
    adminClearWbpBtn.addEventListener('click', adminClearWbpSelection);
}

const jamSelect = document.getElementById('jam-select');
const tglPicker = document.getElementById('tgl-picker');

function formatYYYYMMDD(date){
    const y = date.getFullYear();
    const m = String(date.getMonth()+1).padStart(2,'0');
    const d = String(date.getDate()).padStart(2,'0');
    return `${y}-${m}-${d}`;
}

function mapJamToSession(jam){
    if(!jam) return null;

    if(jam.includes('08:00') || jam.includes('08.00')) return 'Sesi 1';
    if(jam.includes('09:30') || jam.includes('09.30')) return 'Sesi 2';
    if(jam.includes('11:00') || jam.includes('11.00')) return 'Sesi 3';
    if(jam.includes('13:00') || jam.includes('13.00')) return 'Sesi 4';
    if(jam.includes('14:30') || jam.includes('14.30')) return 'Sesi 5';

    if(jam.includes('Sesi 1')) return 'Sesi 1';
    if(jam.includes('Sesi 2')) return 'Sesi 2';
    if(jam.includes('Sesi 3')) return 'Sesi 3';
    if(jam.includes('Sesi 4')) return 'Sesi 4';
    if(jam.includes('Sesi 5')) return 'Sesi 5';

    return null;
}

function updateAvailableSessions(dateStr){

    if(!jamSelect || !dateStr) return;

    // reset semua option
    [...jamSelect.options].forEach(opt=>{

        if(!opt.value) return;

        opt.disabled = false;
        opt.textContent = opt.value;
    });

    const booked = [];

    dbJadwalDisetujui.forEach(item=>{
          console.log(item.tgl_kunjungan, item.jam);

        let tgl = item.tgl_kunjungan;

        if(tgl.substring)
            tgl = tgl.substring(0,10);

        if(tgl === dateStr){

            const sesi = mapJamToSession(item.jam);

            if(sesi)
                booked.push(sesi);
        }
    });

    const sessionStart = {
        'Sesi 1':480,
        'Sesi 2':570,
        'Sesi 3':660,
        'Sesi 4':780,
        'Sesi 5':870
    };

    const today = formatYYYYMMDD(new Date());

    const now = new Date();
    const currentMinute = now.getHours()*60 + now.getMinutes();

    [...jamSelect.options].forEach(opt=>{

        if(!opt.value) return;

        const sesi = mapJamToSession(opt.value);

        let disable = false;

        // jika sesi sudah dibooking
        if(booked.includes(sesi)){
            disable = true;
            opt.textContent = opt.value + ' (Terisi)';
        }

        // jika hari ini dan sesi sudah lewat
        if(dateStr === today){

            if(currentMinute >= sessionStart[sesi]){

                disable = true;
                opt.textContent = opt.value + ' (Terlewat)';
            }
        }

        opt.disabled = disable;
    });
}

if(tglPicker){

    tglPicker.addEventListener('change',function(){

        updateAvailableSessions(this.value);

    });
}
function openPreview(
    id,
    nama,
    nohp,
    tujuan,
    instansi,
    tanggal,
    jam,
    surat,
    status,
    alasan,
    wbp
) {

    document.getElementById('preview-nama').innerText = nama;
    document.getElementById('preview-nohp').innerText = nohp;
    document.getElementById('preview-tujuan').innerText = tujuan;
    document.getElementById('preview-wbp').innerText = wbp;
    document.getElementById('preview-instansi').innerText = instansi;
    document.getElementById('preview-tanggal').innerText = tanggal;
    document.getElementById('preview-jam').innerText = jam;

    document.getElementById('preview-keterangan').innerText =
        status === 'DITOLAK' ? alasan : '-';

    document.getElementById('preview-status').innerHTML =
        `<span class="px-2 py-1 rounded text-xs">${status}</span>`;

    if (surat) {
        document.getElementById('preview-surat').innerHTML =
            `<button onclick="openSuratModal('${surat}')"
                class="text-blue-600 hover:underline">
                Lihat Surat
            </button>`;
    } else {
        document.getElementById('preview-surat').innerHTML = '-';
    }

    const actionContainer = document.getElementById('preview-actions');

    if (status === 'PROSES') {

        actionContainer.classList.remove('hidden');

        actionContainer.innerHTML = `
            <div class="flex gap-3">

                <form method="POST"
                    action="/{{ auth()->user()->hasRole('superadmin') ? 'superadmin' : 'admin' }}/kunjungan/${id}/approve"
                    class="flex-1">

                    @csrf

                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg">
                        Setujui
                    </button>

                </form>

                <button
                    onclick="closePreview(); openTolakModal(${id})"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">

                    Tolak

                </button>

            </div>
        `;

    } else {

        actionContainer.classList.add('hidden');
        actionContainer.innerHTML = '';

    }

    document.getElementById('modal-preview').classList.remove('hidden');
}

function closePreview(){
    document.getElementById('modal-preview').classList.add('hidden');
}


</script>
@endpush
