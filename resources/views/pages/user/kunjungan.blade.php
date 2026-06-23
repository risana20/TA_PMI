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

        {{-- ① Jadwal Disetujui --}}
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-calendar-check text-red-600"></i>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900">Jadwal Kunjungan Griya PMI Beberapa Waktu Kedepan</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Berikut jadwal kunjungan yang sudah diverifikasi. Silakan pilih waktu yang tidak bentrok.</p>
                </div>
            </div>
            @php $disetujui = $jadwalDisetujui; @endphp
            @if($disetujui->count() > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide pb-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide pb-3">Instansi</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide pb-3">Jam Kunjungan</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide pb-3">Tujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($disetujui as $k)
                    <tr>
                        <td class="py-4 text-gray-700">
                            {{ \Carbon\Carbon::parse($k->tgl_kunjungan)->locale('id')->translatedFormat('l, d F Y') }}
                        </td>
                        <td class="py-4 font-semibold text-gray-900">{{ $k->instansi }}</td>
                        <td class="py-4 font-semibold text-gray-900">{{ $k->jam }}</td>
                        <td class="py-4 text-gray-600">{{ $k->tujuan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-sm text-gray-400 text-center py-4">Belum ada jadwal kunjungan yang disetujui.</p>
            @endif
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
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                                    min="{{ date('Y-m-d') }}"
                                    required>
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
(function () {
    const btnKal    = document.getElementById('btn-kalender');
    const cal       = document.getElementById('custom-calendar');
    const tglHidden = document.getElementById('tgl-hidden');
    const tglDisplay= document.getElementById('tgl-display');
    const monthLabel= document.getElementById('month-label');
    const daysGrid  = document.getElementById('days-grid');
    const prevBtn   = document.getElementById('prev-month');
    const nextBtn   = document.getElementById('next-month');

    const MONTHS_EN = ['January','February','March','April','May','June',
                       'July','August','September','October','November','December'];
    const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];
    const DAYS_ID   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

    const now = new Date();
    let viewYear  = now.getFullYear();
    let viewMonth = now.getMonth();
    let selected  = null;

    // Pre-fill if old value exists
    const oldVal = tglHidden.value;
    if (oldVal) {
        const parts = oldVal.split('-');
        selected = new Date(+parts[0], +parts[1] - 1, +parts[2]);
        viewYear  = selected.getFullYear();
        viewMonth = selected.getMonth();
    }

    function pad(n) { return String(n).padStart(2, '0'); }

    function renderCalendar() {
        monthLabel.textContent = MONTHS_EN[viewMonth] + ' ' + viewYear;

        const firstDay    = new Date(viewYear, viewMonth, 1).getDay();
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
        const daysInPrev  = new Date(viewYear, viewMonth, 0).getDate();

        daysGrid.innerHTML = '';

        // Hari dari bulan sebelumnya (abu-abu)
        for (let i = firstDay - 1; i >= 0; i--) {
            const el = document.createElement('div');
            el.textContent = daysInPrev - i;
            el.className = 'text-center text-xs text-gray-300 w-9 h-9 flex items-center justify-center mx-auto';
            daysGrid.appendChild(el);
        }

        // Hari bulan ini
        const today = new Date();
        today.setHours(0,0,0,0);
        for (let day = 1; day <= daysInMonth; day++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = day;

            const currentDate = new Date(viewYear, viewMonth, day);
            const isPast = currentDate < today;

            if (isPast) {
                btn.disabled = true;
                btn.className = 'text-center text-xs text-gray-300 w-9 h-9 flex items-center justify-center mx-auto cursor-not-allowed';
            } else {
                const isSel = selected &&
                    selected.getDate()     === day &&
                    selected.getMonth()    === viewMonth &&
                    selected.getFullYear() === viewYear;

                btn.className = isSel
                    ? 'text-center text-xs font-bold text-white bg-red-600 rounded-full w-9 h-9 flex items-center justify-center mx-auto hover:bg-red-700 transition'
                    : 'text-center text-xs text-gray-700 w-9 h-9 flex items-center justify-center mx-auto hover:bg-red-50 hover:text-red-600 rounded-full transition cursor-pointer';

                btn.addEventListener('click', function () {
                    selected = new Date(viewYear, viewMonth, day);

                    tglHidden.value = viewYear + '-' + pad(viewMonth + 1) + '-' + pad(day);

                    tglDisplay.textContent = DAYS_ID[selected.getDay()] + ', ' + day + ' ' + MONTHS_ID[viewMonth] + ' ' + viewYear;
                    tglDisplay.classList.replace('text-gray-400', 'text-gray-800');

                    cal.classList.add('hidden');
                    renderCalendar();
                });
            }

            daysGrid.appendChild(btn);
        }

        // Hari dari bulan berikutnya (abu-abu)
        const total     = firstDay + daysInMonth;
        const remaining = total % 7 === 0 ? 0 : 7 - (total % 7);
        for (let i = 1; i <= remaining; i++) {
            const el = document.createElement('div');
            el.textContent = i;
            el.className = 'text-center text-xs text-gray-300 w-9 h-9 flex items-center justify-center mx-auto';
            daysGrid.appendChild(el);
        }
    }

    btnKal.addEventListener('click', function (e) {
        e.stopPropagation();
        cal.classList.toggle('hidden');
        renderCalendar();
    });

    prevBtn.addEventListener('click', function () {
        viewMonth--;
        if (viewMonth < 0) { viewMonth = 11; viewYear--; }
        renderCalendar();
    });

    nextBtn.addEventListener('click', function () {
        viewMonth++;
        if (viewMonth > 11) { viewMonth = 0; viewYear++; }
        renderCalendar();
    });

    document.addEventListener('click', function (e) {
        if (!cal.contains(e.target) && e.target !== btnKal) {
            cal.classList.add('hidden');
        }
    });

    // Tampilkan nilai lama jika ada
    if (selected) {
        tglDisplay.textContent = DAYS_ID[selected.getDay()] + ', ' +
            selected.getDate() + ' ' + MONTHS_ID[selected.getMonth()] + ' ' + selected.getFullYear();
        tglDisplay.classList.replace('text-gray-400', 'text-gray-800');
    }

    renderCalendar();
})();

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
    const lbl = document.getElementById('surat-label-text');
    if (input.files && input.files[0]) {
        lbl.textContent = input.files[0].name;
        lbl.classList.remove('text-gray-400');
        lbl.classList.add('text-gray-700');
    }
}

</script>
@endpush
