@extends('layout.public')

@section('title', 'Kunjungan — Griya PMI Surakarta')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-red-600 to-red-700 text-white pt-12 pb-24">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="inline-flex items-center gap-2 bg-white/20 text-white text-xs font-semibold px-4 py-1.5 rounded-full mb-5 uppercase tracking-widest">
            <i class="fa-solid fa-calendar-days"></i>
            Jadwalkan Kunjungan
        </div>
        <h1 class="text-4xl font-bold mb-4">Form Kunjungan</h1>
        <p class="text-red-100 text-base max-w-lg mx-auto leading-relaxed pb-7">
            Isi form di bawah ini untuk mengajukan kunjungan ke Griya PMI Surakarta
        </p>
        @auth
       <button onclick="openModal()"
            class="inline-flex items-center bg-white text-red-600 rounded-full px-5 py-2 font-semibold text-sm hover:bg-red-400 hover:text-white gap-2 transition ">
            <i class="fa-solid fa-arrow-right"></i> Ajukan Kunjungan
        </button>
        @endauth

        @guest
        <a href="{{ route('login') }}"
            class="inline-flex items-center bg-white text-red-600 rounded-full px-5 py-2 font-semibold text-sm hover:bg-red-400 hover:text-white gap-2 transition">
            <i class="fa-solid fa-arrow-right"></i> Ajukan Kunjungan
        </a>
        @endguest
    </div>
</section>

{{-- Content --}}
<div class="bg-gray-50 pb-16">
    <div class="max-w-2xl mx-auto px-4 -mt-12 relative z-10 space-y-6">

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        {{-- ① Jadwal Disetujui (Tampilan Kalender & Detail Sesi) --}}
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <!-- VIEW 1: KALENDER -->
            <div id="calendar-view" class="block">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-calendar-days text-red-600"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900">Jadwal Kunjungan Griya PMI</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Pilih tanggal pada kalender untuk melihat jadwal atau mengajukan kunjungan.</p>
                        </div>
                    </div>
                </div>

                <!-- Kontrol Kalender (Navigasi Bulan) -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                    <button type="button" id="prev-month-btn" class="p-2 hover:bg-gray-100 rounded-lg text-gray-600 transition">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <h3 id="current-month-year" class="font-bold text-gray-800 text-sm"></h3>
                    <button type="button" id="next-month-btn" class="p-2 hover:bg-gray-100 rounded-lg text-gray-600 transition">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>

                <!-- Grid Nama Hari -->
                <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-400 uppercase mb-2">
                    <div>Min</div>
                    <div>Sen</div>
                    <div>Sel</div>
                    <div>Rab</div>
                    <div>Kam</div>
                    <div>Jum</div>
                    <div>Sab</div>
                </div>

                <!-- Grid Hari/Tanggal -->
                <div id="calendar-grid" class="grid grid-cols-7 gap-2">
                    <!-- Javascript will render the days here -->
                </div>
            </div>

            <!-- VIEW 2: DETAIL HARI (LIST SESI) -->
            <div id="detail-view" class="hidden">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="showCalendarView()" class="p-2 hover:bg-gray-100 rounded-lg text-gray-600 transition mr-1">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                        <div>
                            <h2 id="detail-date-title" class="font-bold text-gray-900 text-base">Detail Kunjungan</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Daftar sesi kunjungan yang tersedia atau sudah terisi pada tanggal ini.</p>
                        </div>
                    </div>
                    <div>
                        @auth
                        <button type="button" id="btn-ajukan-kunjungan-detail" onclick="openModalWithSelectedDate()"
                            class="inline-flex items-center bg-red-600 text-white rounded-xl px-4 py-2 font-semibold text-xs hover:bg-red-700 gap-1.5 transition">
                            <i class="fa-solid fa-plus"></i> Ajukan Kunjungan
                        </button>
                        @endauth
                        @guest
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center bg-red-600 text-white rounded-xl px-4 py-2 font-semibold text-xs hover:bg-red-700 gap-1.5 transition">
                            <i class="fa-solid fa-plus"></i> Ajukan Kunjungan
                        </a>
                        @endguest
                    </div>
                </div>

                <!-- List Sesi Kunjungan -->
                <div class="space-y-3">
                    <!-- Sesi 1 -->
                    <div id="sesi-1-item" class="flex items-center justify-between p-4 rounded-xl border border-gray-100 transition bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center font-bold text-xs text-gray-500">1</div>
                            <div>
                                <h4 class="font-bold text-sm text-gray-900">Sesi 1 (08.00 - 09.30 WIB)</h4>
                                <p id="sesi-1-status" class="text-xs text-green-600 mt-0.5 font-medium"><i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Tersedia</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sesi 2 -->
                    <div id="sesi-2-item" class="flex items-center justify-between p-4 rounded-xl border border-gray-100 transition bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center font-bold text-xs text-gray-500">2</div>
                            <div>
                                <h4 class="font-bold text-sm text-gray-900">Sesi 2 (09.30 - 11.00 WIB)</h4>
                                <p id="sesi-2-status" class="text-xs text-green-600 mt-0.5 font-medium"><i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Tersedia</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sesi 3 -->
                    <div id="sesi-3-item" class="flex items-center justify-between p-4 rounded-xl border border-gray-100 transition bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center font-bold text-xs text-gray-500">3</div>
                            <div>
                                <h4 class="font-bold text-sm text-gray-900">Sesi 3 (11.00 - 12.30 WIB)</h4>
                                <p id="sesi-3-status" class="text-xs text-green-600 mt-0.5 font-medium"><i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Tersedia</p>
                            </div>
                        </div>
                    </div>

                    <!-- Istirahat (Break) -->
                    <div class="flex items-center justify-between p-3 rounded-xl border border-dashed border-gray-200 bg-gray-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-mug-hot text-xs"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-xs text-gray-500">Istirahat (12.30 - 13.00 WIB)</h4>
                                <p class="text-[10px] text-gray-400 mt-0.5 font-normal">Sesi istirahat - Tidak tersedia untuk kunjungan</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full uppercase tracking-wider text-[10px]">Istirahat</span>
                    </div>

                    <!-- Sesi 4 -->
                    <div id="sesi-4-item" class="flex items-center justify-between p-4 rounded-xl border border-gray-100 transition bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center font-bold text-xs text-gray-500">4</div>
                            <div>
                                <h4 class="font-bold text-sm text-gray-900">Sesi 4 (13.00 - 14.30 WIB)</h4>
                                <p id="sesi-4-status" class="text-xs text-green-600 mt-0.5 font-medium"><i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Tersedia</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sesi 5 -->
                    <div id="sesi-5-item" class="flex items-center justify-between p-4 rounded-xl border border-gray-100 transition bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center font-bold text-xs text-gray-500">5</div>
                            <div>
                                <h4 class="font-bold text-sm text-gray-900">Sesi 5 (14.30 - 16.00 WIB)</h4>
                                <p id="sesi-5-status" class="text-xs text-green-600 mt-0.5 font-medium"><i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Tersedia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @auth
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

                <form method="POST" action="{{ route('kunjungan.store') }}" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="nama_pengunjung" value="{{ auth()->user()->name }}">
                    <input type="hidden" name="no_hp" value="{{ auth()->user()->phone }}">

                    <div class="space-y-5">
                        {{-- Nama Pengunjung --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pengunjung</label>
                            <input type="text"
                                value="{{ auth()->user()->name }}"
                                readonly
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100">
                        </div>
                        {{-- No HP --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                            <input type="text"
                                value="{{ auth()->user()->phone }}"
                                readonly
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100">
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
                                required
                                value="{{ old('instansi') }}"
                                placeholder="Contoh: Universitas X / Masyarakat Umum"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                        {{-- Mengunjungi WBP Checkbox --}}
                        <div class="mt-4">
                            <label class="inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" id="mengunjungi-wbp-checkbox" name="mengunjungi_wbp" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-4 h-4" onchange="toggleWbpSearch(this.checked)" {{ old('mengunjungi_wbp') ? 'checked' : '' }}>
                                <span class="ml-2 text-sm font-semibold text-gray-700">Mengunjungi 1 orang warga binaan</span>
                            </label>
                        </div>

                        {{-- Warga Binaan Search Container --}}
                        <div id="wbp-search-container" class="{{ old('mengunjungi_wbp') ? '' : 'hidden' }} mt-3 space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Nama Warga Binaan (Aktif)</label>
                            <div class="relative">
                                @php
                                    $oldWbpName = '';
                                    if (old('warga_binaan_id')) {
                                        $oldWbp = \App\Models\WargaBinaan::find(old('warga_binaan_id'));
                                        if ($oldWbp) {
                                            $oldWbpName = $oldWbp->nama;
                                        }
                                    }
                                @endphp
                                <input type="text" id="wbp-search-input" placeholder="Ketik nama warga binaan..." class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500" autocomplete="off" value="{{ $oldWbpName }}" {{ old('warga_binaan_id') ? 'readonly' : '' }} {{ old('mengunjungi_wbp') ? 'required' : '' }}>
                                <input type="hidden" id="wbp-id-input" name="warga_binaan_id" value="{{ old('warga_binaan_id') }}">
                                <button type="button" id="clear-wbp-btn" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 {{ old('warga_binaan_id') ? '' : 'hidden' }}">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                {{-- Dropdown Suggestions --}}
                                <div id="wbp-suggestions" class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
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

                                <input type="date"
                                    id="tgl-picker"
                                    name="tgl_kunjungan"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 bg-white"
                                    min="{{ date('Y-m-d') }}"
                                    required>
                            </div>

                            {{-- Jam --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Sesi Kunjungan
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
                            Ajukan Kunjungan

                        </button>

                    </div>
                </form>
            </div>
        </div>
        @endauth


        {{-- ③ Riwayat (PROSES / DITOLAK) --}}
        {{-- @php $riwayat_lain = $riwayat->whereNotIn('status', ['DISETUJUI']); @endphp
        @if($riwayat_lain->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="font-bold text-gray-900 mb-4">Riwayat Kunjungan</h2>
            <div class="divide-y divide-gray-50">
                @foreach($riwayat_lain as $k)
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">{{ $k->tujuan }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ \Carbon\Carbon::parse($k->tgl_kunjungan)->format('d M Y') }} · {{ $k->jam }}
                        </p>
                    </div>
                    @include('components.badge-status', ['status' => $k->status])
                </div>
                @endforeach
            </div>
        </div>
        @endif --}}

    </div>
</div>

@endsection

@push('scripts')
<script>
    const dbJadwalDisetujui = @json($jadwalDisetujui);

    const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; 

    MONTHS_ID[11] = 'Desember';

    const DAYS_ID   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

    //menyimpan bulan dan tanggal hari ini
    let viewDate = new Date();
    let selectedDateStr = "";

    // mengubah date menjadi format
    function formatYYYYMMDD(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    //mengambil tanggal kunjungan
    function getVisitDateString(visit) {
        if (!visit.tgl_kunjungan) return "";
        return visit.tgl_kunjungan.split('T')[0].split(' ')[0];
    }

    //mengubah jam menjadi sesi
    function mapJamToSession(jamStr) {
        if (!jamStr) return null;
        jamStr = jamStr.trim();
        if (jamStr.toLowerCase().startsWith('sesi 1')) return 'Sesi 1';
        if (jamStr.toLowerCase().startsWith('sesi 2')) return 'Sesi 2';
        if (jamStr.toLowerCase().startsWith('sesi 3')) return 'Sesi 3';
        if (jamStr.toLowerCase().startsWith('sesi 4')) return 'Sesi 4';
        if (jamStr.toLowerCase().startsWith('sesi 5')) return 'Sesi 5';

        // Parse HH:MM from HH:MM:SS or HH.MM
        let timePart = jamStr.replace('.', ':');
        let match = timePart.match(/(\d{2}):(\d{2})/);
        if (!match) return null;
        let hour = parseInt(match[1]);
        let min = parseInt(match[2]);
        let totalMinutes = hour * 60 + min;

        // Sesi 1: 08.00-09.30 -> 480 to 570 mins
        if (totalMinutes >= 480 && totalMinutes < 570) return 'Sesi 1';
        // Sesi 2: 09.30-11.00 -> 570 to 660 mins
        if (totalMinutes >= 570 && totalMinutes < 660) return 'Sesi 2';
        // Sesi 3: 11.00-12.30 -> 660 to 750 mins
        if (totalMinutes >= 660 && totalMinutes < 780) return 'Sesi 3';
        // Sesi 4: 13.00-14.30 -> 780 to 870 mins
        if (totalMinutes >= 780 && totalMinutes < 870) return 'Sesi 4';
        // Sesi 5: 14.30-16.00 -> 870 to 990 mins
        if (totalMinutes >= 870 && totalMinutes <= 990) return 'Sesi 5';

        return null;
    }

    //menampilkan kalender
    function renderCalendar() {
        const grid = document.getElementById('calendar-grid');
        const label = document.getElementById('current-month-year');
        if (!grid || !label) return;

        grid.innerHTML = "";
        const year = viewDate.getFullYear();
        const month = viewDate.getMonth();

        //bulan
        label.textContent = `${MONTHS_ID[month]} ${year}`;

        const firstDayIndex = new Date(year, month, 1).getDay();
        const lastDay = new Date(year, month + 1, 0).getDate();
        const prevLastDay = new Date(year, month, 0).getDate();

        const today = new Date();
        today.setHours(0,0,0,0);

        // Padding cells from previous month
        for (let i = firstDayIndex; i > 0; i--) {
            const dayNum = prevLastDay - i + 1;
            const cell = document.createElement('div');
            cell.className = 'bg-gray-50/50 border border-gray-100/50 rounded-2xl min-h-[65px] p-2 text-center text-xs text-gray-300 flex items-center justify-center cursor-not-allowed';
            cell.textContent = dayNum;
            grid.appendChild(cell);
        }

        // membuat tanggal 
        for (let day = 1; day <= lastDay; day++) {
            const currentCellDate = new Date(year, month, day);
            const dateStr = formatYYYYMMDD(currentCellDate);
            const isPast = currentCellDate < today;

            const cell = document.createElement('button');
            cell.type = "button";
            cell.className = 'relative flex flex-col items-center justify-between p-2 rounded-2xl border transition text-center min-h-[65px]';

            const numSpan = document.createElement('span');
            numSpan.className = 'text-xs font-bold';
            numSpan.textContent = day;
            cell.appendChild(numSpan);

            if (isPast) {
                cell.className += ' bg-gray-50 border-gray-100 text-gray-300 cursor-not-allowed';
                cell.disabled = true;
            } else {
                // memeriksa kunjungan
                const booked = [];
                dbJadwalDisetujui.forEach(v => {
                    const vDate = getVisitDateString(v);
                    if (vDate === dateStr) {
                        const sess = mapJamToSession(v.jam);
                        if (sess) booked.push(sess);
                    }
                });

                const isFull = booked.length >= 5;

                if (isFull) {
                    cell.className += ' bg-red-50 border-red-200 text-red-700 hover:bg-red-100';
                } else if (booked.length > 0) {
                    cell.className += ' bg-orange-50 border-orange-200 text-orange-800 hover:bg-orange-100';
                } else {
                    cell.className += ' bg-white border-gray-100 text-gray-700 hover:border-red-300 hover:bg-red-50/30';
                }

                // Draw dots indicator for the 5 sessions
                const dotsContainer = document.createElement('div');
                dotsContainer.className = 'flex justify-center gap-0.5 mt-1';
                for (let s = 1; s <= 5; s++) {
                    const isBooked = booked.includes('Sesi ' + s);
                    const dot = document.createElement('span');
                    dot.className = `w-1.5 h-1.5 rounded-full ${isBooked ? 'bg-red-500' : 'bg-green-400'}`;
                    dotsContainer.appendChild(dot);
                }
                cell.appendChild(dotsContainer);

                // Click handler
                cell.addEventListener('click', () => {
                    showDayDetail(dateStr);
                });
            }

            grid.appendChild(cell);
        }

        // Padding cells from next month
        const totalCells = firstDayIndex + lastDay;
        const remaining = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let i = 1; i <= remaining; i++) {
            const cell = document.createElement('div');
            cell.className = 'bg-gray-50/50 border border-gray-100/50 rounded-2xl min-h-[65px] p-2 text-center text-xs text-gray-300 flex items-center justify-center cursor-not-allowed';
            cell.textContent = i;
            grid.appendChild(cell);
        }
    }

    //menampilkan sesi tanggal
    function showDayDetail(dateStr) {
        selectedDateStr = dateStr;
        const parts = dateStr.split('-');
        const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);

        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const dateTitleStr = dateObj.toLocaleDateString('id-ID', options);
        document.getElementById('detail-date-title').textContent = dateTitleStr;

        const booked = [];
        dbJadwalDisetujui.forEach(v => {
            const vDate = getVisitDateString(v);
            if (vDate === dateStr) {
                const sess = mapJamToSession(v.jam);
                if (sess) booked.push(sess);
            }
        });

        const todayStr = formatYYYYMMDD(new Date());
        const isToday = (dateStr === todayStr);

        const nowTime = new Date();
        const currentHour = nowTime.getHours();
        const currentMin = nowTime.getMinutes();
        const currentTotalMinutes = currentHour * 60 + currentMin;

        const sessionStartTimes = {
            'Sesi 1': 480, // 08:00
            'Sesi 2': 570, // 09:30
            'Sesi 3': 660, // 11:00
            'Sesi 4': 780, // 13:00
            'Sesi 5': 870, // 14:30
        };

        let availableCount = 0;

        //mengecek setiap sesi
        for (let s = 1; s <= 5; s++) {
            const sessionName = 'Sesi ' + s;
            const statusEl = document.getElementById(`sesi-${s}-status`);
            const itemEl = document.getElementById(`sesi-${s}-item`);

            const isBooked = booked.includes(sessionName);
            const startTime = sessionStartTimes[sessionName];
            const isPassed = isToday && (currentTotalMinutes >= startTime);

            if (isBooked) {
                statusEl.innerHTML = `<i class="fa-solid fa-circle-xmark mr-1 text-[10px]"></i> Terisi (Tidak Tersedia)`;
                statusEl.className = "text-xs text-red-600 mt-0.5 font-medium";
                itemEl.className = "flex items-center justify-between p-4 rounded-xl border border-red-100 bg-red-50/20 opacity-85 transition";
            } else if (isPassed) {
                statusEl.innerHTML = `<i class="fa-solid fa-clock-rotate-left mr-1 text-[10px]"></i> Sudah Terlewat`;
                statusEl.className = "text-xs text-gray-400 mt-0.5 font-medium";
                itemEl.className = "flex items-center justify-between p-4 rounded-xl border border-gray-100 bg-gray-50 opacity-60 transition";
            } else {
                statusEl.innerHTML = `<i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Tersedia`;
                statusEl.className = "text-xs text-green-600 mt-0.5 font-medium";
                itemEl.className = "flex items-center justify-between p-4 rounded-xl border border-gray-100 transition bg-white";
                availableCount++;
            }
        }

        //tanggal tidak bisa diklik
        const btnAjukan = document.getElementById('btn-ajukan-kunjungan-detail');
        if (btnAjukan) {
            if (availableCount === 0) {
                btnAjukan.disabled = true;
                btnAjukan.className = "inline-flex items-center bg-gray-300 text-gray-500 rounded-xl px-4 py-2 font-semibold text-xs cursor-not-allowed gap-1.5 transition";
                btnAjukan.innerHTML = `<i class="fa-solid fa-ban"></i> Jadwal Penuh`;
            } else {
                btnAjukan.disabled = false;
                btnAjukan.className = "inline-flex items-center bg-red-600 text-white rounded-xl px-4 py-2 font-semibold text-xs hover:bg-red-700 gap-1.5 transition";
                btnAjukan.innerHTML = `<i class="fa-solid fa-plus"></i> Ajukan Kunjungan`;
            }
        }

        document.getElementById('calendar-view').classList.replace('block', 'hidden');
        document.getElementById('detail-view').classList.replace('hidden', 'block');
    }

    //halaman detail disembunyikan
    function showCalendarView() {
        document.getElementById('detail-view').classList.replace('block', 'hidden');
        document.getElementById('calendar-view').classList.replace('hidden', 'block');
        renderCalendar();
    }

    //membuka modal ajuan
    function openModalWithSelectedDate() {
        const tglPicker = document.getElementById('tgl-picker');
        if (tglPicker && selectedDateStr) {
            tglPicker.value = selectedDateStr;
            updateAvailableSessions(selectedDateStr);
        }
        openModal();
    }

    function updateAvailableSessions(dateStr) {
        const jamSelect = document.getElementById('jam-select');
        if (!jamSelect) return;

        // Reset all options
        Array.from(jamSelect.options).forEach(opt => {
            if (!opt.value) return;
            opt.disabled = false;
            opt.textContent = opt.value;
        });

        if (!dateStr) return;

        // Find booked sessions
        const bookedSessions = [];
        dbJadwalDisetujui.forEach(v => {
            const vDate = getVisitDateString(v);
            if (vDate === dateStr) {
                const sess = mapJamToSession(v.jam);
                if (sess) bookedSessions.push(sess);
            }
        });

        // Check today's time logic
        const todayStr = formatYYYYMMDD(new Date());
        const isToday = (dateStr === todayStr);

        const nowTime = new Date();
        const currentHour = nowTime.getHours();
        const currentMin = nowTime.getMinutes();
        const currentTotalMinutes = currentHour * 60 + currentMin;

        const sessionStartTimes = {
            'Sesi 1: 08.00-09.30': 480,
            'Sesi 2: 09.30-11.00': 570,
            'Sesi 3: 11.00-12.30': 660,
            'Sesi 4: 13.00-14.30': 780,
            'Sesi 5: 14.30-16.00': 870,
        };

        // Disable booked or passed options
        Array.from(jamSelect.options).forEach(opt => {
            if (!opt.value) return;
            const optSess = mapJamToSession(opt.value);
            const startTime = sessionStartTimes[opt.value];
            const isPassed = isToday && (currentTotalMinutes >= startTime);

            if (bookedSessions.includes(optSess)) {
                opt.disabled = true;
                opt.textContent = opt.value + ' (Terisi)';
            } else if (isPassed) {
                opt.disabled = true;
                opt.textContent = opt.value + ' (Sudah Terlewat)';
            }
        });
    }

    // Bind navigation buttons
    document.getElementById('prev-month-btn').addEventListener('click', () => {
        viewDate.setMonth(viewDate.getMonth() - 1);
        renderCalendar();
    });

    document.getElementById('next-month-btn').addEventListener('click', () => {
        viewDate.setMonth(viewDate.getMonth() + 1);
        renderCalendar();
    });

    // mengubah tanggal kunjungan di form
    document.getElementById('tgl-picker').addEventListener('change', function() {
        updateAvailableSessions(this.value);
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
                suratLabel.classList.remove('opacity-50', 'pointer-events-none');
            } else {
                suratInput.disabled = true;
                suratInput.required = false;
                suratInput.value = "";
                suratLabel.classList.add('opacity-50', 'pointer-events-none');
            }
        }

        tujuan.addEventListener('change', updateSurat);
        updateSurat();
    }

    function openModal() {
        const modal = document.getElementById('modal-tambah');
        if (!modal) return;
        modal.classList.remove('hidden');

        const tglPicker = document.getElementById('tgl-picker');
        if (tglPicker) {
            if (!tglPicker.value) {
                tglPicker.value = formatYYYYMMDD(new Date());
            }
            updateAvailableSessions(tglPicker.value);
        }
        initSurat();
    }

    function previewSurat(input) {
        const lbl = document.getElementById('surat-label-text');
        if (input.files && input.files[0]) {
            lbl.textContent = input.files[0].name;
            lbl.classList.remove('text-gray-400');
            lbl.classList.add('text-gray-700');
        }
    }

    // Autocomplete WBP Search
    let wbpTimeout = null;
    const wbpSearchInput = document.getElementById('wbp-search-input');
    const wbpIdInput = document.getElementById('wbp-id-input');
    const wbpSuggestions = document.getElementById('wbp-suggestions');
    const clearWbpBtn = document.getElementById('clear-wbp-btn');

    //hiden kolom
    function toggleWbpSearch(checked) {
        const container = document.getElementById('wbp-search-container');
        if (checked) {
            container.classList.remove('hidden');
            wbpSearchInput.required = true;
        } else {
            container.classList.add('hidden');
            wbpSearchInput.required = false;
            clearWbpSelection();
        }
    }

    //menghapus data wargabinaan yang telah dipilih
    function clearWbpSelection() {
        wbpSearchInput.value = '';
        wbpIdInput.value = '';
        wbpSearchInput.readOnly = false;
        clearWbpBtn.classList.add('hidden');
        wbpSuggestions.innerHTML = '';
        wbpSuggestions.classList.add('hidden');
    }

    if (wbpSearchInput) {
        wbpSearchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(wbpTimeout);
            
            if (query.length < 2) {
                wbpSuggestions.innerHTML = '';
                wbpSuggestions.classList.add('hidden');
                return;
            }
            
            wbpTimeout = setTimeout(() => {
                fetch(`/kunjungan/search-wbp?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        wbpSuggestions.innerHTML = '';
                        if (data.length === 0) {
                            wbpSuggestions.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Tidak ada warga binaan aktif ditemukan</div>';
                            wbpSuggestions.classList.remove('hidden');
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
                                selectWbp(wbp.id, wbp.nama);
                            });
                            wbpSuggestions.appendChild(btn);
                        });
                        wbpSuggestions.classList.remove('hidden');
                    });
            }, 300);
        });

        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!wbpSearchInput.contains(e.target) && !wbpSuggestions.contains(e.target)) {
                wbpSuggestions.classList.add('hidden');
            }
        });
    }

    function selectWbp(id, name) {
        wbpSearchInput.value = name;
        wbpIdInput.value = id;
        wbpSearchInput.readOnly = true;
        wbpSuggestions.classList.add('hidden');
        clearWbpBtn.classList.remove('hidden');
    }

    if (clearWbpBtn) {
        clearWbpBtn.addEventListener('click', clearWbpSelection);
    }

    // Initial Render
    renderCalendar();
</script>
    @endpush

