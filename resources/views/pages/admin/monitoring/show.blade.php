@extends('layout.app')

@section('title', 'Monitoring — ' . $wargaBinaan->nama)

@section('content')

{{-- Back --}}
<a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.index') }}"
    class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 transition mb-5">
    <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar
</a>

{{-- Profile Card + Riwayat Penyakit --}}
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
            @php $aktifPenyakit = $riwayatPenyakit->where('status', 'Aktif'); @endphp
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

{{-- Tab Utama (Pill Style) --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">

    <div class="bg-gray-100 p-1 rounded-xl flex flex-col sm:flex-row gap-1 mb-5">
        <button onclick="switchMainTab('pemeriksaan')" id="main-btn-pemeriksaan"
            class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium transition cursor-pointer bg-white shadow text-gray-900">
            <i class="fa-solid fa-wave-square text-red-500"></i>
            <span class="hidden sm:inline">Pemeriksaan Kesehatan</span>
            <span class="sm:hidden">Kesehatan</span>
        </button>
        @if($wargaBinaan->kategori !== 'Lansia')
        <button onclick="switchMainTab('rsj')" id="main-btn-rsj"
            class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium transition cursor-pointer text-gray-500 hover:bg-gray-50 hover:text-gray-700">
            <i class="fa-solid fa-brain"></i>
            <span class="hidden sm:inline">Pemeriksaan RSJ</span>
            <span class="sm:hidden">RSJ</span>
        </button>
        @endif
        <button onclick="switchMainTab('obat')" id="main-btn-obat"
            class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium transition cursor-pointer text-gray-500 hover:bg-gray-50 hover:text-gray-700">
            <i class="fa-solid fa-pills"></i>
            <span class="hidden sm:inline">Pencatatan Minum Obat</span>
            <span class="sm:hidden">Minum Obat</span>
        </button>
    </div>

    {{-- ================= TAB: PEMERIKSAAN KESEHATAN ================= --}}
    <div id="main-tab-pemeriksaan">
        <div class="flex justify-end mb-4">
            <button onclick="document.getElementById('modal-periksa').classList.remove('hidden')"
                class="bg-red-600 hover:bg-red-700 text-white rounded-full px-4 py-2 text-xs font-semibold flex items-center gap-1.5 transition">
                <i class="fa-solid fa-plus"></i> Tambah Pemeriksaan
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Tanggal</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Frek. Napas</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">TD</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Suhu</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Nadi</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">SPO₂</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">BB / TB</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Riwayat Penyakit</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayat as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-3 text-gray-800 font-medium whitespace-nowrap">{{ $r->tanggal->format('d M Y') }}</td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $r->frek_napas ? $r->frek_napas . '/mnt' : '-' }}</td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $r->tekanan_darah ?? '-' }}</td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $r->suhu_tubuh ? $r->suhu_tubuh . '°C' : '-' }}</td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $r->nadi ? $r->nadi . '/mnt' : '-' }}</td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $r->spo2 ? $r->spo2 . '%' : '-' }}</td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">
                            {{ $r->berat_badan ? $r->berat_badan . ' kg' : '-' }}
                            @if($r->tinggi_badan) / {{ $r->tinggi_badan }} cm @endif
                        </td>
                        <td class="px-3 py-3 align-top min-w-[200px]">
                            @if($r->riwayatPenyakits && $r->riwayatPenyakits->isNotEmpty())
                                <div class="flex flex-col gap-1.5">
                                @foreach($r->riwayatPenyakits as $p)
                                    @if(isset($p->status) && $p->status === 'Aktif')
                                        <span class="inline-flex items-center w-max bg-red-600 text-white text-[11px] font-semibold px-2.5 py-1 rounded-full">
                                            {{ $p->nama_penyakit ?? '' }} <span class="ml-1 opacity-80">(Aktif)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center w-max bg-gray-100 text-gray-700 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-gray-200">
                                            {{ $p->nama_penyakit ?? '' }} <span class="ml-1 font-normal opacity-70">(Sembuh)</span>
                                        </span>
                                    @endif
                                @endforeach
                                </div>
                            @elseif(!empty($r->riwayat_penyakit))
                                @php
                                    $decoded = json_decode($r->riwayat_penyakit, true);
                                    $isJson = json_last_error() === JSON_ERROR_NONE;
                                @endphp
                                @if(!$isJson)
                                    <span class="text-gray-600 line-clamp-2">{{ $r->riwayat_penyakit }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center">
                            <button type="button" onclick='openDetailPeriksa(@json($r))' class="text-gray-400 hover:text-red-600 transition" title="Lihat Detail Pemeriksaan">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center">
                            <i class="fa-solid fa-heart-pulse text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">Belum ada data pemeriksaan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
        <div class="mt-3">{{ $riwayat->links() }}</div>
        @endif
    </div>

    @if($wargaBinaan->kategori !== 'Lansia')
    {{-- ================= TAB: PEMERIKSAAN RSJ ================= --}}
    <div id="main-tab-rsj" class="hidden">

        {{-- Sub-tab header --}}
        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
            <div class="flex border-b border-gray-200">
                <button onclick="switchRSJTab('bulanan')" id="rsj-btn-bulanan"
                    class="px-4 py-2.5 text-sm font-semibold text-red-600 border-b-2 border-red-600 -mb-px transition whitespace-nowrap">
                    Pemeriksaan Bulanan RSJ
                </button>
                <button onclick="switchRSJTab('rujukan')" id="rsj-btn-rujukan"
                    class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-gray-700 border-b-2 border-transparent -mb-px transition whitespace-nowrap">
                    Surat Rujukan ODGJ
                </button>
            </div>
            {{-- button left intentionally empty; individual tabs handle their own add buttons --}}
            <div></div>
        </div>

        {{-- Sub-tab: Pemeriksaan Bulanan RSJ --}}
        <div id="rsj-tab-bulanan">
            <div class="flex justify-end mb-4">
                <button onclick="document.getElementById('modal-rsj').classList.remove('hidden')"
                    class="bg-red-600 hover:bg-red-700 text-white rounded-full px-4 py-2 text-xs font-semibold flex items-center gap-1.5 transition flex-shrink-0">
                    <i class="fa-solid fa-plus"></i> Tambah Pemeriksaan Bulanan
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[700px]">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Tgl Kontrol</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Kondisi</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Gejala</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Obat</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Catatan</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Surat Rujukan</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Kontrol Berikutnya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pemeriksaanRSJ as $rsj)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-3 text-gray-800 font-medium whitespace-nowrap">{{ $rsj->tgl_kontrol->format('d M Y') }}</td>
                            <td class="px-3 py-3 text-gray-600 max-w-[150px]"><span class="line-clamp-2">{{ $rsj->kondisi ?? '-' }}</span></td>
                            <td class="px-3 py-3 text-gray-600 max-w-[150px]"><span class="line-clamp-2">{{ $rsj->gejala ?? '-' }}</span></td>
                            <td class="px-3 py-3 text-gray-600 max-w-[150px]"><span class="line-clamp-2">{{ $rsj->obat ?? '-' }}</span></td>
                            <td class="px-3 py-3 text-gray-600 max-w-[150px]"><span class="line-clamp-2">{{ $rsj->catatan ?? '-' }}</span></td>
                            <td class="px-3 py-3 text-gray-600 whitespace-nowrap">
                                @if($rsj->suratRujukanOdgj)
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-xs font-semibold text-gray-800">Rujukan Aktif</span>
                                        <span class="text-[11px] text-gray-500">
                                            {{ $rsj->suratRujukanOdgj->tanggal_terbit->format('d/m/y') }} - {{ $rsj->suratRujukanOdgj->tanggal_berakhir->format('d/m/y') }}
                                        </span>
                                        @if($rsj->suratRujukanOdgj->file_surat)
                                            <a href="{{ Storage::disk('public')->url($rsj->suratRujukanOdgj->file_surat) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1 text-[11px] mt-0.5 font-medium">
                                                <i class="fa-solid fa-file-pdf"></i> Lihat Surat
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-400 border border-gray-200">
                                        Tanpa Rujukan
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-gray-600 whitespace-nowrap">
                                {{ $rsj->kontrol_berikutnya ? $rsj->kontrol_berikutnya->format('d M Y') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <i class="fa-solid fa-brain text-4xl text-gray-200 mb-3 block"></i>
                                <p class="text-gray-400 text-sm">Belum ada data pemeriksaan RSJ</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sub-tab: Surat Rujukan ODGJ --}}
        <div id="rsj-tab-rujukan" class="hidden">
            {{-- tombol tambah --}}
            <div class="flex justify-end mb-4">
                <button onclick="document.getElementById('modal-rujukan').classList.remove('hidden')"
                    class="bg-red-600 hover:bg-red-700 text-white rounded-full px-4 py-2 text-xs font-semibold flex items-center gap-1.5 transition flex-shrink-0">
                    <i class="fa-solid fa-plus"></i> Tambah Surat Rujukan
                </button>
            </div>

            {{-- tabel surat rujukan --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[600px]">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Tanggal Terbit</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Tanggal Berakhir</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Surat Rujukan</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($suratRujukans as $sr)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-3 text-gray-800 font-medium whitespace-nowrap">{{ $sr->tanggal_terbit->format('d M Y') }}</td>
                            <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $sr->tanggal_berakhir->format('d M Y') }}</td>
                            <td class="px-3 py-3 text-gray-600 whitespace-nowrap">
                                @if($sr->file_surat)
                                <a href="{{ Storage::disk('public')->url($sr->file_surat) }}" class="text-blue-600 hover:underline" target="_blank">Lihat File</a>
                                @else
                                -
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                @php
                                $badge = $sr->status === 'Kedaluwarsa'
                                ? 'bg-red-100 text-red-700'
                                : 'bg-green-100 text-green-700';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                    {{ strtoupper($sr->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center">
                                <i class="fa-solid fa-file-medical text-4xl text-gray-200 mb-3 block"></i>
                                <p class="text-gray-400 text-sm">Belum ada surat rujukan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ================= TAB: PENCATATAN MINUM OBAT ================= --}}
    <div id="main-tab-obat" class="hidden">
        <div class="flex justify-end mb-4">
            <button onclick="document.getElementById('modal-tambah-obat').classList.remove('hidden')"
                class="bg-red-600 hover:bg-red-700 text-white rounded-full px-4 py-2 text-xs font-semibold flex items-center gap-1.5 transition flex-shrink-0">
                <i class="fa-solid fa-plus"></i> Tambah Obat
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Nama Obat</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Bentuk Obat</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Aturan Minum</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Jumlah Awal</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Tgl Input</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Status</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Asal Obat</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stokObats as $obat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-3 text-gray-800 font-medium whitespace-nowrap">{{ $obat->nama_obat }}</td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $obat->bentuk_obat ?? $obat->satuan ?? '-' }}</td>
                        <td class="px-3 py-3 text-gray-600 max-w-[150px]"><span class="line-clamp-2">{{ $obat->aturan_minum ?? '-' }}</span></td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $obat->jumlah_awal ? $obat->jumlah_awal . ' ' . ($obat->bentuk_obat ?? $obat->satuan ?? '') : '-' }}</td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $obat->tgl_mulai ? $obat->tgl_mulai->format('d/m/Y') : '-' }}</td>
                        <td class="px-3 py-3">
                            @php
                            $statusBadge = match($obat->status) {
                            'AKTIF' => 'bg-green-100 text-green-700',
                            'HABIS' => 'bg-orange-100 text-orange-700',
                            'SEMBUH' => 'bg-gray-100 text-gray-700 border border-gray-300',
                            default => 'bg-gray-100 text-gray-600'
                            };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                                {{ $obat->status }}
                            </span>
                        </td>
                        <td class="px-3 py-3">
                            @php
                            $asalBadge = ($obat->asal_obat ?? 'OBAT_PERIKSA') === 'OBAT_GRIYA'
                                ? 'bg-red-600 text-white'
                                : 'bg-white text-red-600 border border-red-600';
                            $asalText = ($obat->asal_obat ?? 'OBAT_PERIKSA') === 'OBAT_GRIYA' ? 'OBAT GRIYA' : 'OBAT PERIKSA';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $asalBadge }}">
                                {{ $asalText }}
                            </span>
                        </td>
                        <td class="px-3 py-3">
                            <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.showObat', [$wargaBinaan, $obat]) }}"
                                class="text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <i class="fa-solid fa-pills text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">Belum ada catatan minum obat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ==================== MODALS ==================== --}}





{{-- Modal: Tambah Pemeriksaan Bulanan RSJ --}}
<div id="modal-rsj" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

        {{-- Header --}}
        <div class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-lg text-gray-900">Tambah Pemeriksaan Bulanan RSJ</h3>
                <p class="text-sm text-gray-400 mt-0.5">Catat hasil kontrol ke RSJ</p>
            </div>
            <button onclick="document.getElementById('modal-rsj').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 mt-0.5">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.storeRSJ', $wargaBinaan) }}"
            class="px-6 py-5 space-y-4">
            @csrf

            {{-- Tanggal Kontrol --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kontrol</label>
                <input type="date" name="tgl_kontrol" value="{{ date('Y-m-d') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            {{-- Surat Rujukan ODGJ --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Surat Rujukan ODGJ</label>
                <select name="surat_rujukan_odgj_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
                    <option value="">Otomatis (Pencocokan berdasarkan Tanggal Kontrol)</option>
                    @foreach($suratRujukans as $sr)
                        <option value="{{ $sr->id }}">
                            Rujukan: {{ $sr->tanggal_terbit->format('d M Y') }} s/d {{ $sr->tanggal_berakhir->format('d M Y') }} ({{ $sr->status }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kondisi Saat Kontrol --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Saat Kontrol</label>
                <textarea name="kondisi" rows="2" placeholder="Contoh: Stabil, tenang, komunikatif..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>

            {{-- Gejala --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gejala</label>
                <textarea name="gejala" rows="2" placeholder="Contoh: Halusinasi berkurang, tidak ada waham..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>

            {{-- Obat & Dosis --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Obat (Pilih dari Obat Aktif)</label>
                
                @php
                    // Ambil daftar obat pasien dengan status aktif
                    $obatAktif = $stokObats->filter(function($o) {
                        return strtolower($o->status) === 'aktif';
                    });
                @endphp
                
                <div class="relative" id="dropdown-rsj-obat-container">
                    <div class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white hover:bg-gray-50 cursor-pointer flex justify-between items-center transition shadow-sm" id="rsj-obat-display" onclick="toggleObatDropdown()">
                        <span id="rsj-obat-text" class="text-gray-400 line-clamp-1">Cari dan pilih obat...</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-200" id="rsj-obat-icon"></i>
                    </div>
                    
                    <div id="rsj-obat-dropdown" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl z-[60] overflow-hidden">
                        <div class="p-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                            <input type="text" id="rsj-obat-search" placeholder="Ketik nama obat..." onkeyup="filterObatRsj()"
                                class="w-full bg-transparent border-none p-0 text-sm focus:outline-none focus:ring-0 text-gray-800 placeholder-gray-400">
                        </div>
                        <div class="max-h-56 overflow-y-auto p-2 flex flex-col gap-1" id="rsj-obat-list">
                            @forelse($obatAktif as $o)
                                <label class="flex items-start gap-3 p-2.5 hover:bg-red-50 rounded-lg cursor-pointer transition border border-transparent hover:border-red-100 rsj-obat-item group">
                                    <input type="checkbox" value="{{ $o->nama_obat }}{{ $o->aturan_minum ? ' ('.$o->aturan_minum.')' : '' }}" class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500 shadow-sm" onchange="updateSelectedObatRsj()">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-800 group-hover:text-red-700 transition obat-name">{{ $o->nama_obat }}</p>
                                        @if($o->aturan_minum)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $o->aturan_minum }}</p>
                                        @endif
                                    </div>
                                    <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-[10px] font-bold self-start mt-0.5">Aktif</span>
                                </label>
                            @empty
                                <div class="px-4 py-6 text-center">
                                    <i class="fa-solid fa-pills text-gray-200 text-3xl mb-2"></i>
                                    <p class="text-sm text-gray-500">Tidak ada pengobatan aktif yang terdata.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Input tersembunyi untuk dikirim ke kontroller -->
                <textarea name="obat" id="hidden-rsj-obat" class="hidden"></textarea>
            </div>

            {{-- Catatan Dokter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Dokter</label>
                <textarea name="catatan" rows="2" placeholder="Catatan atau rekomendasi dokter..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>

            {{-- Jadwal Kontrol Berikutnya --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jadwal Kontrol Berikutnya</label>
                <input type="date" name="kontrol_berikutnya"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-rsj').classList.add('hidden')"
                    class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Tambah Surat Rujukan ODGJ --}}
<div id="modal-rujukan" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-lg text-gray-900">Tambah Surat Rujukan</h3>
            <button onclick="document.getElementById('modal-rujukan').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.storeRujukan', $wargaBinaan) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Terbit</label>
                <input type="date" name="tanggal_terbit" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Berakhir</label>
                <input type="date" name="tanggal_berakhir" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload File Surat Rujukan</label>
                <input type="file" name="file_surat" accept="application/pdf,image/png,image/jpg,image/jpeg"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                           file:rounded-full file:border-0
                           file:text-sm file:font-semibold
                           file:bg-red-50 file:text-red-700
                           hover:file:bg-red-100" />
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-rujukan').classList.add('hidden')"
                    class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Tambah Pemeriksaan Kesehatan --}}
<div id="modal-periksa" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

        <div class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-lg text-gray-900">Tambah Pemeriksaan Kesehatan</h3>
                <p class="text-sm text-gray-400 mt-0.5">Catat hasil pemeriksaan berkala</p>
            </div>
            <button onclick="document.getElementById('modal-periksa').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 mt-0.5">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="form-periksa" method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.storePemeriksaan', $wargaBinaan) }}"
            class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pemeriksaan</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            {{-- TTV Box --}}
            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Tanda-Tanda Vital (TTV)</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Frek. Napas <span class="font-normal text-gray-400">(x/mnt)</span></label>
                        <input type="text" name="frek_napas" placeholder="18"
                            class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tekanan Darah <span class="font-normal text-gray-400">(mmHg)</span></label>
                        <input type="text" name="tekanan_darah" placeholder="120/80"
                            class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Suhu Tubuh <span class="font-normal text-gray-400">(°C)</span></label>
                        <input type="text" name="suhu_tubuh" placeholder="36.5"
                            class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nadi <span class="font-normal text-gray-400">(x/mnt)</span></label>
                        <input type="text" name="nadi" placeholder="80"
                            class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">SPO₂ <span class="font-normal text-gray-400">(%)</span></label>
                        <input type="text" name="spo2" placeholder="98"
                            class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Berat Badan <span class="text-gray-400 font-normal">(kg)</span></label>
                    <input type="number" name="berat_badan" step="0.1" placeholder="60.5"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi Badan <span class="text-gray-400 font-normal">(cm)</span></label>
                    <input type="number" name="tinggi_badan" step="0.1" placeholder="165"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keluhan</label>
                <textarea name="keluhan" rows="2" placeholder="Tuliskan keluhan pasien..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tindakan</label>
                <textarea name="tindakan" rows="2" placeholder="Tindakan yang diberikan..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan TTV</label>
                <textarea name="catatan" rows="2" placeholder="Catatan tambahan..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>
            {{-- Section Riwayat Penyakit Terintegrasi --}}
            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 mt-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Update Riwayat Penyakit</p>
                    <button type="button" onclick="addNewRiwayatRow()" class="text-xs font-semibold text-red-600 hover:text-red-700 hover:bg-red-50 bg-white border border-red-200 px-2.5 py-1 rounded-md transition flex items-center gap-1">
                        <i class="fa-solid fa-plus"></i> Penyakit Baru
                    </button>
                </div>

                <div id="riwayat-penyakit-container" class="space-y-2">
                    @forelse($riwayatPenyakit as $rp)
                    <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg p-2.5">
                        <span class="text-sm font-medium text-gray-700 truncate mr-3 flex-1" title="{{ $rp->nama_penyakit }}">{{ $rp->nama_penyakit }}</span>
                        <div class="flex items-center gap-3">
                            <input type="hidden" name="riwayat_existing[{{ $rp->id }}][nama_penyakit]" value="{{ $rp->nama_penyakit }}">
                            <select name="riwayat_existing[{{ $rp->id }}][status]" class="border border-gray-200 rounded-md text-sm px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-red-500 bg-gray-50 cursor-pointer">
                                <option value="Aktif" {{ $rp->status === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Sembuh" {{ $rp->status === 'Sembuh' ? 'selected' : '' }}>Sembuh</option>
                            </select>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 italic" id="empty-riwayat-msg">Belum ada riwayat penyakit dicatat. Tambahkan jika perlu.</p>
                    @endforelse
                </div>

                <div id="new-riwayat-penyakit-container" class="space-y-2 mt-2 empty:hidden">
                    <!-- Penyakit baru akan ditambahkan ke sini oleh JS -->
                </div>
            </div>
        </form>

        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-white sticky bottom-0 rounded-b-2xl">
            <button type="button" onclick="document.getElementById('modal-periksa').classList.add('hidden')"
                class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition bg-white">Batal</button>
            <button type="submit" form="form-periksa"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan</button>
        </div>
    </div>
</div>

{{-- Modal: Tambah Obat --}}
<div id="modal-tambah-obat" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-bold text-lg text-gray-900">Tambah Obat</h3>
            <button onclick="document.getElementById('modal-tambah-obat').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <p class="text-sm text-gray-400 mb-4">Catat obat yang dikonsumsi warga binaan</p>

        {{-- Tab Asal Obat --}}
        <div class="flex bg-gray-100 rounded-full p-1 mb-5">
            <button type="button" onclick="switchAsalObatTab('periksa')" id="asal-btn-periksa"
                class="flex-1 py-2 text-sm font-semibold rounded-full transition bg-white text-gray-900 shadow">
                OBAT PERIKSA
            </button>
            <button type="button" onclick="switchAsalObatTab('griya')" id="asal-btn-griya"
                class="flex-1 py-2 text-sm font-medium rounded-full transition text-gray-500">
                OBAT GRIYA
            </button>
        </div>

        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'monitoring.storeObat', $wargaBinaan) }}" class="space-y-4">
            @csrf
            <input type="hidden" name="asal_obat" id="input-asal-obat" value="OBAT_PERIKSA">

            <div id="container-obat-periksa">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Obat</label>
                <input id="input-nama-obat" type="text" name="nama_obat" placeholder="Masukkan nama obat" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div id="container-obat-griya" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Cari nama obat</label>
                <div class="relative">
                    <select id="select-logistik-id" name="logistik_id" onchange="syncObatGriya()"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
                        <option value="">Pilih obat dari logistik</option>
                        @foreach($logistikObats as $logistik)
                            <option value="{{ $logistik->id }}" data-nama="{{ $logistik->itemLogistik->nama_item }}" data-bentuk="{{ $logistik->itemLogistik->satuan }}">{{ $logistik->itemLogistik->nama_item }} (Stok: {{ $logistik->jumlah_saat_ini }} {{ $logistik->itemLogistik->satuan }})</option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Aturan Minum</label>
                    <input type="text" name="aturan_minum" placeholder="2 kali sehari sebelum makan" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Bentuk Obat</label>
                    <div class="relative">
                        <select id="input-bentuk-obat" name="bentuk_obat" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
                            <option value="Tablet">Tablet</option>
                            <option value="Kapsul">Kapsul</option>
                            <option value="Sirup">Sirup</option>
                            <option value="Injeksi">Injeksi</option>
                            <option value="Salep">Salep</option>
                            <option value="Tetes">Tetes</option>
                            <option value="Bubuk">Bubuk</option>
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Awal</label>
                    <input type="number" name="jumlah_awal" min="1" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" value="{{ date('Y-m-d') }}" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-tambah-obat').classList.add('hidden')"
                    class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Update Sisa Obat --}}
<div id="modal-update-sisa" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-lg text-gray-900">Update Sisa Obat</h3>
            <button onclick="document.getElementById('modal-update-sisa').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="form-update-sisa" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sisa Terbaru</label>
                <input type="number" id="input-sisa-terbaru" name="sisa" min="0" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Update</label>
                <input type="date" id="input-tanggal-update" name="tgl_update" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-update-sisa').classList.add('hidden')"
                    class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-5 py-2 text-sm font-medium transition">Batal</button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-6 py-2 text-sm transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Detail Pemeriksaan --}}
<div id="modal-detail-periksa" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-lg text-gray-900">Detail Pemeriksaan Kesehatan</h3>
            <button onclick="document.getElementById('modal-detail-periksa').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div class="p-6 overflow-y-auto space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Tanggal Pemeriksaan</p>
                    <p class="text-sm text-gray-800 mt-1 font-medium" id="detail-periksa-tanggal"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Petugas</p>
                    <p class="text-sm text-gray-800 mt-1" id="detail-periksa-petugas"></p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-3">Tanda-Tanda Vital (TTV)</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-4 gap-x-3">
                    <div>
                        <p class="text-xs text-gray-400">Frek. Napas</p>
                        <p class="text-sm font-medium text-gray-800" id="detail-periksa-napas"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Tekanan Darah</p>
                        <p class="text-sm font-medium text-gray-800" id="detail-periksa-td"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Suhu Tubuh</p>
                        <p class="text-sm font-medium text-gray-800" id="detail-periksa-suhu"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Nadi</p>
                        <p class="text-sm font-medium text-gray-800" id="detail-periksa-nadi"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">SPO₂</p>
                        <p class="text-sm font-medium text-gray-800" id="detail-periksa-spo2"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Berat/Tinggi Badan</p>
                        <p class="text-sm font-medium text-gray-800" id="detail-periksa-bbtb"></p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Keluhan</p>
                    <p class="text-sm text-gray-800 mt-1 whitespace-pre-line bg-gray-50 p-3 rounded-lg border border-gray-100 min-h-[40px]" id="detail-periksa-keluhan"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Tindakan</p>
                    <p class="text-sm text-gray-800 mt-1 whitespace-pre-line bg-gray-50 p-3 rounded-lg border border-gray-100 min-h-[40px]" id="detail-periksa-tindakan"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Catatan TTV</p>
                    <p class="text-sm text-gray-800 mt-1 whitespace-pre-line bg-gray-50 p-3 rounded-lg border border-gray-100 min-h-[40px]" id="detail-periksa-catatan"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Riwayat Penyakit</p>
                    <div class="mt-1" id="detail-periksa-riwayat"></div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 rounded-b-2xl flex justify-end">
            <button onclick="document.getElementById('modal-detail-periksa').classList.add('hidden')"
                class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg px-6 py-2 text-sm font-medium transition">Tutup</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var wargaBinaanId = {{ $wargaBinaan->id }};

    // ── Detail Pemeriksaan Modal ──────────────────────────────
    function openDetailPeriksa(data) {
        document.getElementById('detail-periksa-tanggal').innerText = new Date(data.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('detail-periksa-petugas').innerText = data.petugas || '-';
        document.getElementById('detail-periksa-napas').innerText = data.frek_napas ? data.frek_napas + '/mnt' : '-';
        document.getElementById('detail-periksa-td').innerText = data.tekanan_darah || '-';
        document.getElementById('detail-periksa-suhu').innerText = data.suhu_tubuh ? data.suhu_tubuh + '°C' : '-';
        document.getElementById('detail-periksa-nadi').innerText = data.nadi ? data.nadi + '/mnt' : '-';
        document.getElementById('detail-periksa-spo2').innerText = data.spo2 ? data.spo2 + '%' : '-';
        
        let bb = data.berat_badan ? data.berat_badan + ' kg' : '-';
        let tb = data.tinggi_badan ? data.tinggi_badan + ' cm' : '';
        document.getElementById('detail-periksa-bbtb').innerText = bb + (tb ? ' / ' + tb : '');

        document.getElementById('detail-periksa-keluhan').innerText = data.keluhan || '-';
        document.getElementById('detail-periksa-tindakan').innerText = data.tindakan || '-';
        document.getElementById('detail-periksa-catatan').innerText = data.catatan || '-';
        
        let riwayatHtml = '<span class="text-gray-400">-</span>';
        if (data.riwayat_penyakits && Array.isArray(data.riwayat_penyakits) && data.riwayat_penyakits.length > 0) {
            riwayatHtml = '<div class="flex flex-col gap-2">';
            data.riwayat_penyakits.forEach(p => {
                let nama = p.nama_penyakit || '';
                if (p.status === 'Aktif') {
                    riwayatHtml += `<span class="inline-flex items-center w-max bg-red-600 text-white text-[11px] font-semibold px-2.5 py-1 rounded-full">${nama} <span class="ml-1 opacity-80">(Aktif)</span></span>`;
                } else {
                    riwayatHtml += `<span class="inline-flex items-center w-max bg-gray-100 text-gray-700 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-gray-200">${nama} <span class="ml-1 font-normal opacity-70">(Sembuh)</span></span>`;
                }
            });
            riwayatHtml += '</div>';
        } else if (data.riwayat_penyakit) {
            try {
                let parsed = JSON.parse(data.riwayat_penyakit);
                // Jika isinya JSON, kita skip karena itu akumulatif (sesuai request)
                riwayatHtml = '<span class="text-gray-400">-</span>';
            } catch(e) {
                // Jika bukan JSON berarti free-text lama
                riwayatHtml = `<span class="text-sm text-gray-800">${data.riwayat_penyakit}</span>`;
            }
        }
        document.getElementById('detail-periksa-riwayat').innerHTML = riwayatHtml;

        document.getElementById('modal-detail-periksa').classList.remove('hidden');
    }

    function addNewRiwayatRow() {
        const container = document.getElementById('new-riwayat-penyakit-container');
        const emptyMsg = document.getElementById('empty-riwayat-msg');
        if(emptyMsg) emptyMsg.style.display = 'none';

        const index = container.children.length;
        
        const html = `
            <div class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 rounded-lg p-2.5 relative" id="new-riwayat-${index}">
                <input type="text" name="riwayat_baru[${index}][nama_penyakit]" placeholder="Nama Penyakit Baru" required class="flex-1 w-full border border-gray-200 rounded-md text-sm px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-red-500">
                <select name="riwayat_baru[${index}][status]" required class="border border-gray-200 rounded-md text-sm px-2 py-1.5 bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option value="Aktif">Aktif</option>
                    <option value="Sembuh">Sembuh</option>
                </select>
                <button type="button" onclick="document.getElementById('new-riwayat-${index}').remove()" class="text-red-500 hover:text-red-700 hover:bg-red-100 p-1.5 rounded transition"><i class="fa-solid fa-trash text-sm"></i></button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    // ── Main Tab ──────────────────────────────────────────────
    const mainTabs = ['pemeriksaan', 'rsj', 'obat'];

    function switchMainTab(tab) {
        mainTabs.forEach(function(t) {
            const tabEl = document.getElementById('main-tab-' + t);
            const btnEl = document.getElementById('main-btn-' + t);
            if (tabEl) tabEl.classList.add('hidden');
            if (btnEl) {
                btnEl.classList.remove('bg-white', 'shadow', 'text-gray-900');
                btnEl.classList.add('text-gray-500');
            }
        });
        const activeTabEl = document.getElementById('main-tab-' + tab);
        if (activeTabEl) activeTabEl.classList.remove('hidden');
        
        const activeBtn = document.getElementById('main-btn-' + tab);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-500');
            activeBtn.classList.add('bg-white', 'shadow', 'text-gray-900');
        }
    }

    // ── RSJ Sub-tab ───────────────────────────────────────────
    function switchRSJTab(tab) {
        ['bulanan', 'rujukan'].forEach(function(t) {
            document.getElementById('rsj-tab-' + t).classList.add('hidden');
            const btn = document.getElementById('rsj-btn-' + t);
            btn.classList.remove('text-red-600', 'border-red-600', 'font-semibold');
            btn.classList.add('text-gray-400', 'border-transparent', 'font-medium');
        });
        document.getElementById('rsj-tab-' + tab).classList.remove('hidden');
        const activeBtn = document.getElementById('rsj-btn-' + tab);
        activeBtn.classList.remove('text-gray-400', 'border-transparent', 'font-medium');
        activeBtn.classList.add('text-red-600', 'border-red-600', 'font-semibold');
    }

    // ── Open Update Sisa Obat Modal ───────────────────────────
    function openUpdateSisaObat(obatId, currentSisa, currentDate) {
        const modal = document.getElementById('modal-update-sisa');
        const form = document.getElementById('form-update-sisa');

        document.getElementById('input-sisa-terbaru').value = currentSisa;
        document.getElementById('input-tanggal-update').value = currentDate || new Date().toISOString().split('T')[0];

        form.action = '/admin/monitoring/' + wargaBinaanId + '/obat/' + obatId + '/sisa';

        modal.classList.remove('hidden');
    }

    // ── Sync Obat Griya Details ───────────────────────────────────
    function syncObatGriya() {
        const select = document.getElementById('select-logistik-id');
        const inputNama = document.getElementById('input-nama-obat');
        const inputBentuk = document.getElementById('input-bentuk-obat');
        
        if (select.selectedIndex > 0) {
            const opt = select.options[select.selectedIndex];
            if (opt.dataset.nama) inputNama.value = opt.dataset.nama;
            if (opt.dataset.bentuk) inputBentuk.value = opt.dataset.bentuk;
        } else {
            inputNama.value = 'dummy';
        }
    }

    // ── Switch Asal Obat Tab ───────────────────────────────────
    function switchAsalObatTab(tab) {
        const btnPeriksa = document.getElementById('asal-btn-periksa');
        const btnGriya = document.getElementById('asal-btn-griya');
        const inputAsalObat = document.getElementById('input-asal-obat');
        const containerPeriksa = document.getElementById('container-obat-periksa');
        const containerGriya = document.getElementById('container-obat-griya');
        const inputNama = document.getElementById('input-nama-obat');
        const selectLogistik = document.getElementById('select-logistik-id');

        if (tab === 'periksa') {
            btnPeriksa.classList.remove('text-gray-500');
            btnPeriksa.classList.add('bg-white', 'text-gray-900', 'shadow', 'font-semibold');
            btnGriya.classList.remove('bg-white', 'text-gray-900', 'shadow', 'font-semibold');
            btnGriya.classList.add('text-gray-500', 'font-medium');
            inputAsalObat.value = 'OBAT_PERIKSA';
            
            containerPeriksa.classList.remove('hidden');
            containerGriya.classList.add('hidden');
            inputNama.removeAttribute('readonly');
            inputNama.setAttribute('required', 'required');
            if (inputNama.value === 'dummy' || selectLogistik.selectedIndex > 0) {
                inputNama.value = '';
            }
            selectLogistik.removeAttribute('required');
        } else {
            btnGriya.classList.remove('text-gray-500');
            btnGriya.classList.add('bg-white', 'text-gray-900', 'shadow', 'font-semibold');
            btnPeriksa.classList.remove('bg-white', 'text-gray-900', 'shadow', 'font-semibold');
            btnPeriksa.classList.add('text-gray-500', 'font-medium');
            inputAsalObat.value = 'OBAT_GRIYA';

            containerGriya.classList.remove('hidden');
            containerPeriksa.classList.add('hidden');
            inputNama.setAttribute('readonly', 'readonly');
            inputNama.removeAttribute('required');
            selectLogistik.setAttribute('required', 'required');
            syncObatGriya();
        }
    }
    function toggleObatDropdown() {
        const dropdown = document.getElementById('rsj-obat-dropdown');
        const icon = document.getElementById('rsj-obat-icon');
        dropdown.classList.toggle('hidden');
        if(!dropdown.classList.contains('hidden')) {
            icon.classList.add('rotate-180');
            document.getElementById('rsj-obat-search').focus();
        } else {
            icon.classList.remove('rotate-180');
        }
    }

    function filterObatRsj() {
        const search = document.getElementById('rsj-obat-search').value.toLowerCase();
        const items = document.querySelectorAll('.rsj-obat-item');
        items.forEach(item => {
            const text = item.querySelector('.obat-name').innerText.toLowerCase();
            if(text.includes(search)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function updateSelectedObatRsj() {
        const checkboxes = document.querySelectorAll('#rsj-obat-list input[type="checkbox"]:checked');
        const hiddenInput = document.getElementById('hidden-rsj-obat');
        const displayText = document.getElementById('rsj-obat-text');
        
        let selectedValues = [];
        checkboxes.forEach(cb => selectedValues.push(cb.value));

        if(selectedValues.length > 0) {
            hiddenInput.value = selectedValues.join('\n');
            displayText.innerText = selectedValues.join(', ');
            displayText.classList.remove('text-gray-400');
            displayText.classList.add('text-gray-800', 'font-medium');
        } else {
            hiddenInput.value = '';
            displayText.innerText = 'Cari dan pilih obat...';
            displayText.classList.remove('text-gray-800', 'font-medium');
            displayText.classList.add('text-gray-400');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const container = document.getElementById('dropdown-rsj-obat-container');
        if(container && !container.contains(e.target)) {
            const dropdown = document.getElementById('rsj-obat-dropdown');
            const icon = document.getElementById('rsj-obat-icon');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    });

</script>
@endpush
