@extends('layout.app')

@section('title', 'Kunjungan')

@section('content')

@include('sections.page-header', ['title' => 'Kunjungan', 'subtitle' => 'Kelola pengajuan kunjungan'])

{{-- Toolbar --}}
<div class="flex flex-wrap items-center gap-3 mb-4">
    <form method="GET" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'kunjungan.index') }}" id="filterForm" class="flex items-center gap-3 flex-1">

    <div class="relative">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text"
            id="searchInput"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari nama, no HP, instansi, tujuan..."
            class="pl-9 pr-4 py-2 border border-gray-200 rounded-full text-sm w-64 focus:outline-none focus:ring-2 focus:ring-red-500">
    </div>

    <select name="status" id="statusFilter"
        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
        <option value="">Semua Status</option>
        <option value="PROSES" {{ request('status') == 'PROSES' ? 'selected' : '' }}>PROSES</option>
        <option value="DISETUJUI" {{ request('status') == 'DISETUJUI' ? 'selected' : '' }}>DISETUJUI</option>
        <option value="DITOLAK" {{ request('status') == 'DITOLAK' ? 'selected' : '' }}>DITOLAK</option>
    </select>

</form> 
<button onclick="openModal()"
            class="inline-flex items-center bg-red-600 text-white rounded-full px-5 py-2 font-semibold text-sm  gap-2 transition ">
        <i class="fa-solid fa-plus"></i> Tambah Kunjungan
</button>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nama</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">No. HP</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Tujuan</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Instansi</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Tgl Kunjungan</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Jam</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Surat Pengajuan</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @forelse($kunjungans as $kunjungan)
            <tr class="hover:bg-gray-50">

                <td class="px-4 py-4 font-medium text-gray-900">
                    {{ $kunjungan->nama_pengunjung }}
                </td>

                <td class="px-4 py-4 text-gray-600">
                    {{ $kunjungan->no_hp }}
                </td>

                <td class="px-4 py-4 text-gray-600">
                    {{ $kunjungan->tujuan }}
                </td>

                <td class="px-4 py-4 text-gray-600">
                    {{ $kunjungan->instansi ?? '-' }}
                </td>

                <td class="px-4 py-4 text-gray-600">
                    {{ $kunjungan->tgl_kunjungan->format('d M Y') }}
                </td>

                <td class="px-4 py-4 text-gray-600">
                    {{ $kunjungan->jam }}
                </td>

                {{-- SURAT PENGAJUAN --}}
                <td class="px-4 py-4 text-gray-600">

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

                {{-- AKSI --}}
                <td class="px-4 py-4">

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

<div id="modal-surat" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center">

    <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6">

        <div class="flex justify-between items-center mb-4">

            <h3 class="font-bold text-lg">Surat Pengajuan</h3>

            <button onclick="closeSuratModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <iframe
            id="preview-surat"
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

                    <input type="hidden" name="nama_pengunjung" value="{{ auth()->user()->name }}">
                    <input type="hidden" name="no_hp" value="{{ auth()->user()->phone }}">
                    <input type="hidden" name="tgl_kunjungan" id="tgl-hidden" value="{{ old('tgl_kunjungan') }}">

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

                                <input type="date"
                                    id="tgl-picker"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                                    onchange="document.getElementById('tgl-hidden').value=this.value">
                            </div>

                            {{-- Jam --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Jam Kunjungan
                                </label>

                                <select name="jam" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">

                                    <option value="">Pilih jam...</option>
                                    <option value="09:00">09.00 WIB</option>
                                    <option value="10:00">10.00 WIB</option>
                                    <option value="11:00">11.00 WIB</option>
                                    <option value="13:00">13.00 WIB</option>
                                    <option value="14:00">14.00 WIB</option>
                                    <option value="15:00">15.00 WIB</option>
                                    <option value="16:00">16.00 WIB</option>

                                </select>
                            </div>

                        </div>

                        {{-- Info --}}
                        <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info text-red-500 text-sm"></i>

                            <p class="text-sm text-red-700">
                                <span class="font-semibold">Informasi:</span>
                                Jam operasional kunjungan adalah
                                <span class="font-semibold">09:00 – 16:00 WIB</span>.
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

@endsection

@push('scripts')
<script>
function openTolakModal(id) {
    document.getElementById('form-tolak').action = '/admin/kunjungan/' + id + '/reject';
    document.getElementById('modal-tolak').classList.remove('hidden');
}
function openSuratModal(url){

    document.getElementById('preview-surat').src = url;

    document.getElementById('download-surat').href = url;

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
</script>
@endpush
