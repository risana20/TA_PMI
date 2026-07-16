@extends('layout.public')

@section('title', 'Donasi — Griya PMI Surakarta')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-red-600 to-red-700 text-white pt-12 pb-24">
    <div class="max-w-4xl mx-auto px-4 text-center">

        <div class="inline-flex items-center gap-2 bg-white/20 text-white text-xs font-semibold px-4 py-1.5 rounded-full mb-5 uppercase tracking-widest">
            <i class="fa-solid fa-heart"></i>
            Berbagi Kebaikan
        </div>

        <h1 class="text-4xl font-bold mb-4">Form Donasi</h1>
        <p class="text-red-100 text-base max-w-lg mx-auto leading-relaxed">
            Bantuan Anda sangat berarti bagi warga binaan Griya PMI. Pilih jenis donasi yang ingin Anda salurkan.
        </p>
    </div>
</section>

{{-- Content --}}
<div class="bg-gray-50 pb-16">
    <div class="max-w-2xl mx-auto px-4 -mt-12 relative z-10">

        {{-- Form Card --}}
        <div class="bg-white rounded-2xl shadow-lg p-8">

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('donasi.store') }}" enctype="multipart/form-data" id="form-donasi">
                @csrf
                <input type="hidden" name="nama_donatur" value="{{ auth()->user()->name }}">

                {{-- Pilih Jenis --}}
                <h2 class="text-lg font-bold text-gray-900 mb-1">Pilih Jenis Donasi</h2>
                <p class="text-sm text-gray-400 mb-5">Silakan pilih jenis donasi dan lengkapi data yang diperlukan</p>

                <div class="grid grid-cols-3 gap-3 mb-8 items-stretch">
                    @foreach([
                        'Uang'    => ['fa-credit-card', 'Transfer ke rekening PMI'],
                        'Barang'  => ['fa-box',         'Pakaian, alat rumah tangga, dll'],
                        'Makanan' => ['fa-utensils',    'Bahan mentah atau siap saji'],
                    ] as $jenis => $info)
                    <label class="cursor-pointer flex flex-col">
                        <input type="radio" name="jenis" value="{{ $jenis }}"
                               class="sr-only jenis-radio" {{ $jenis === 'Uang' ? 'checked' : '' }}>
                        <div class="jenis-card flex-1 flex flex-col border-2 rounded-xl p-4 transition
                                {{ $jenis === 'Uang' ? 'border-red-500 bg-red-50' : 'border-gray-200 bg-white' }}"
                             data-jenis="{{ $jenis }}">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3
                                    {{ $jenis === 'Uang' ? 'bg-red-600' : 'bg-gray-100' }}">
                                <i class="fa-solid {{ $info[0] }} text-sm
                                        {{ $jenis === 'Uang' ? 'text-white' : 'text-gray-400' }}"></i>
                            </div>
                            <p class="font-semibold text-sm text-gray-900">
                                {{ $jenis === 'Makanan' ? 'Bahan Makanan' : $jenis }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">{{ $info[1] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>

                {{-- Detail Uang --}}
                <div id="detail-Uang" class="detail-donasi">
                    <h2 class="text-lg font-bold text-gray-900 mb-2">Detail Donasi</h2>
                    
                    {{-- Rekening Info --}}
                    <div class="mb-5 bg-red-50 border border-red-100 rounded-xl p-4 text-sm text-red-900 space-y-2 shadow-sm">
                        <div class="flex items-center gap-2 font-bold">
                            <i class="fa-solid fa-building-columns"></i>
                            <span>Rekening Resmi Griya PMI Surakarta:</span>
                        </div>
                        <ul class="space-y-1 list-disc list-inside text-xs text-red-800">
                            <li><strong>Bank Syariah Indonesia:</strong> 703 962 1597</li>
                            <li><strong>Bank Jateng Syariah:</strong> 5022 040 518</li>
                        </ul>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Donasi (Rp)</label>
                            <input type="number" name="nominal" min="1" placeholder="100000"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Transfer Bank Tujuan</label>
                            <select name="bank_tujuan"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 appearance-none bg-white">
                                <option value="">Pilih bank...</option>
                                <option value="Bank Syariah Indonesia 703 962 1597">Bank Syariah Indonesia 703 962 1597</option>
                                <option value="Bank Jateng Syariah 5022 040 518">Bank Jateng Syariah 5022 040 518</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Transfer</label>
                        <label id="upload-area"
                               class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl py-10 cursor-pointer hover:border-red-300 transition bg-white">
                            <i class="fa-solid fa-upload text-gray-300 text-2xl mb-3"></i>
                            <p class="text-sm text-gray-500">Klik untuk upload atau drag & drop</p>
                            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wide">PNG, JPG hingga 5MB</p>
                            <input type="file" name="bukti_transfer" accept="image/png,image/jpeg"
                                   class="hidden" id="bukti-input" onchange="previewFile(this)">
                        </label>
                        <p id="file-name" class="text-xs text-green-600 mt-1 hidden"></p>
                    </div>
                </div>

                {{-- Detail Barang --}}
                <div id="detail-Barang" class="detail-donasi hidden">
                    <h2 class="text-lg font-bold text-gray-900 mb-5">Detail Donasi</h2>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-4 mb-4">
                        {{-- Kiri: Nama Barang + Jumlah --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                                <select name="nama_barang" id="barang_select" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 appearance-none bg-white">
                                    <option value="">Pilih Barang...</option>
                                    @foreach($barangStok as $stok)
                                        @if($stok->itemLogistik)
                                        <option value="{{ $stok->itemLogistik->nama_item }}" data-satuan="{{ $stok->itemLogistik->satuan }}">
                                            {{ $stok->itemLogistik->nama_item }}
                                        </option>
                                        @endif
                                    @endforeach
                                    <option value="Lainnya">Lainnya (Barang baru)</option>
                                </select>
                            </div>
                            <div id="custom_barang_wrapper" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang Lainnya</label>
                                <input type="text" name="nama_barang_custom" id="nama_barang_custom" placeholder="Contoh: Selimut, Pakaian, dll" disabled
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                    <input type="number" min="1" name="jumlah_barang" placeholder="Contoh: 2" required
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                                    <input type="text" name="satuan" id="satuan_barang" placeholder="Satuan" readonly required pattern="[^0-9]+" title="Satuan tidak boleh mengandung angka"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 focus:outline-none">
                                </div>
                            </div>
                        </div>
                        {{-- Kanan: Kondisi + Metode Penyerahan --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="kondisi" value="Baru" checked class="accent-red-600 w-4 h-4"> Baru
                                    </label>
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="kondisi" value="Bekas Layak" class="accent-red-600 w-4 h-4"> Bekas Layak
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Penyerahan</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="metode_penyerahan" value="Antar Sendiri" checked class="accent-red-600 w-4 h-4"> Antar Sendiri
                                    </label>
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="metode_penyerahan" value="Dijemput petugas" class="accent-red-600 w-4 h-4"> Dijemput petugas
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Penyerahan</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-regular fa-calendar text-sm"></i>
                                </span>
                                <input type="date" name="tgl_penyerahan"
                                    class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Penyerahan</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-regular fa-clock text-sm"></i>
                                </span>
                                <input type="time" name="jam_penyerahan"
                                    class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detail Makanan --}}
                <div id="detail-Makanan" class="detail-donasi hidden">
                    <h2 class="text-lg font-bold text-gray-900 mb-5">Detail Donasi</h2>

                    {{-- 2 kolom: kiri input, kanan radio --}}
                    <div class="grid grid-cols-2 gap-x-6 gap-y-4 mb-4">

                        {{-- Kiri: Nama Makanan + Jumlah --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Makanan</label>
                                <select name="nama_makanan" id="makanan_select" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 appearance-none bg-white">
                                    <option value="">Pilih Makanan...</option>
                                    @foreach($makananStok as $stok)
                                        @if($stok->itemLogistik)
                                        <option value="{{ $stok->itemLogistik->nama_item }}" data-satuan="{{ $stok->itemLogistik->satuan }}">
                                            {{ $stok->itemLogistik->nama_item }}
                                        </option>
                                        @endif
                                    @endforeach
                                    <option value="Lainnya">Lainnya (Makanan baru)</option>
                                </select>
                            </div>
                            <div id="custom_makanan_wrapper" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Makanan Lainnya</label>
                                <input type="text" name="nama_makanan_custom" id="nama_makanan_custom" placeholder="Contoh: Biskuit, Susu, dll" disabled
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                <div class="flex gap-2">
                                    <input type="number" min="1" name="jumlah_makanan_value" id="jumlah_makanan_value" placeholder="Contoh: 10" required
                                        class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <input type="text" name="jumlah_makanan_satuan" id="jumlah_makanan_satuan" placeholder="Satuan" readonly required pattern="[^0-9]+" title="Satuan tidak boleh mengandung angka"
                                         class="w-28 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 focus:outline-none">
                                </div>
                            </div>
                        </div>

                        {{-- Kanan: Jenis + Metode Penyerahan --}}
                        <div class="space-y-4">
                            <div id="jenis_makanan_wrapper" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="jenis_makanan" value="Bahan Mentah" checked disabled
                                            class="accent-red-600 w-4 h-4"> Bahan Mentah
                                    </label>
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="jenis_makanan" value="Siap Saji" disabled
                                            class="accent-red-600 w-4 h-4"> Siap Saji
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Penyerahan</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="metode_penyerahan" value="Antar Sendiri" checked
                                            class="accent-red-600 w-4 h-4"> Antar Sendiri
                                    </label>
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="metode_penyerahan" value="Dijemput petugas"
                                            class="accent-red-600 w-4 h-4"> Dijemput petugas
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Penyerahan</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-regular fa-calendar text-sm"></i>
                                </span>
                                <input type="date" name="tgl_penyerahan"
                                    class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Penyerahan</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-regular fa-clock text-sm"></i>
                                </span>
                                <input type="time" name="jam_penyerahan"
                                    class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full mt-8 bg-red-600 hover:bg-red-700 text-white font-semibold py-3.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-heart"></i> Kirim Donasi
                </button>

            </form>
        </div>

        {{-- Riwayat Donasi --}}
        @if($riwayat->count() > 0)
        <div class="mt-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Riwayat Donasi Saya</h2>
            <div class="space-y-3">
                @foreach($riwayat as $d)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">
                            {{ $d->jenis === 'Makanan' ? 'Bahan Makanan' : $d->jenis }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $d->created_at->format('d M Y') }}</p>
                    </div>
                    @include('components.badge-status', ['status' => $d->status])
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
const radios  = document.querySelectorAll('.jenis-radio');
const cards   = document.querySelectorAll('.jenis-card');
const details = document.querySelectorAll('.detail-donasi');

radios.forEach(radio => {
    radio.addEventListener('change', function () {
        const jenis = this.value;

        updateFormState(jenis);
    });
});

function updateFormState(jenis) {
    cards.forEach(card => {
        const active = card.dataset.jenis === jenis;
        card.classList.toggle('border-red-500', active);
        card.classList.toggle('bg-red-50',      active);
        card.classList.toggle('border-gray-200', !active);
        card.classList.toggle('bg-white',        !active);

        const iconWrap = card.querySelector('div');
        const icon     = card.querySelector('i');
        iconWrap.classList.toggle('bg-red-600',  active);
        iconWrap.classList.toggle('bg-gray-100', !active);
        icon.classList.toggle('text-white',      active);
        icon.classList.toggle('text-gray-400',   !active);
    });

    details.forEach(d => {
        const isActive = d.id === 'detail-' + jenis;
        d.classList.toggle('hidden', !isActive);

        // Disable all inputs in hidden sections, enable in active section
        const inputs = d.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = !isActive;
        });
    });

    // Call custom Makanan/Barang state updates
    if (jenis === 'Makanan') {
        updateMakananInputsState();
    } else if (jenis === 'Barang') {
        updateBarangInputsState();
    }
}

const makananSelect = document.getElementById('makanan_select');
const customMakananWrapper = document.getElementById('custom_makanan_wrapper');
const namaMakananCustom = document.getElementById('nama_makanan_custom');
const jumlahMakananValue = document.getElementById('jumlah_makanan_value');
const jumlahMakananSatuan = document.getElementById('jumlah_makanan_satuan');
const jenisMakananWrapper = document.getElementById('jenis_makanan_wrapper');

function updateMakananInputsState() {
    if (!makananSelect) return;
    const isMakananActive = !document.getElementById('detail-Makanan').classList.contains('hidden');
    if (!isMakananActive) return;

    const isLainnya = makananSelect.value === 'Lainnya';
    if (isLainnya) {
        customMakananWrapper.classList.remove('hidden');
        namaMakananCustom.required = true;
        namaMakananCustom.disabled = false;
        
        jumlahMakananSatuan.removeAttribute('readonly');
        jumlahMakananSatuan.classList.remove('bg-gray-50', 'text-gray-500');
        jumlahMakananSatuan.classList.add('bg-white', 'text-gray-900');
        jumlahMakananSatuan.placeholder = 'Satuan (kg, box, dll)';

        if (jenisMakananWrapper) {
            jenisMakananWrapper.classList.remove('hidden');
            const jenisRadios = jenisMakananWrapper.querySelectorAll('input[type="radio"]');
            jenisRadios.forEach(radio => radio.disabled = false);
        }
    } else {
        customMakananWrapper.classList.add('hidden');
        namaMakananCustom.required = false;
        namaMakananCustom.disabled = true;
        namaMakananCustom.value = '';
        
        jumlahMakananSatuan.setAttribute('readonly', 'true');
        jumlahMakananSatuan.classList.remove('bg-white', 'text-gray-900');
        jumlahMakananSatuan.classList.add('bg-gray-50', 'text-gray-500');
        
        const selectedOption = makananSelect.options[makananSelect.selectedIndex];
        const satuan = selectedOption ? selectedOption.getAttribute('data-satuan') : '';
        jumlahMakananSatuan.value = satuan || '';
        jumlahMakananSatuan.placeholder = 'Satuan';

        if (jenisMakananWrapper) {
            jenisMakananWrapper.classList.add('hidden');
            const jenisRadios = jenisMakananWrapper.querySelectorAll('input[type="radio"]');
            jenisRadios.forEach(radio => radio.disabled = true);
        }
    }
}

const barangSelect = document.getElementById('barang_select');
const customBarangWrapper = document.getElementById('custom_barang_wrapper');
const namaBarangCustom = document.getElementById('nama_barang_custom');
const satuanBarang = document.getElementById('satuan_barang');

function updateBarangInputsState() {
    if (!barangSelect) return;
    const isBarangActive = !document.getElementById('detail-Barang').classList.contains('hidden');
    if (!isBarangActive) return;

    const isLainnya = barangSelect.value === 'Lainnya';
    if (isLainnya) {
        customBarangWrapper.classList.remove('hidden');
        namaBarangCustom.required = true;
        namaBarangCustom.disabled = false;
        
        satuanBarang.removeAttribute('readonly');
        satuanBarang.classList.remove('bg-gray-50', 'text-gray-500');
        satuanBarang.classList.add('bg-white', 'text-gray-900');
        satuanBarang.placeholder = 'Satuan (pcs, unit, dll)';
    } else {
        customBarangWrapper.classList.add('hidden');
        namaBarangCustom.required = false;
        namaBarangCustom.disabled = true;
        namaBarangCustom.value = '';
        
        satuanBarang.setAttribute('readonly', 'true');
        satuanBarang.classList.remove('bg-white', 'text-gray-900');
        satuanBarang.classList.add('bg-gray-50', 'text-gray-500');
        
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        const satuan = selectedOption ? selectedOption.getAttribute('data-satuan') : '';
        satuanBarang.value = satuan || '';
        satuanBarang.placeholder = 'Satuan';
    }
}

if (makananSelect) {
    makananSelect.addEventListener('change', updateMakananInputsState);
}

if (barangSelect) {
    barangSelect.addEventListener('change', updateBarangInputsState);
}

// Initial state
const currentJenis = document.querySelector('.jenis-radio:checked').value;
updateFormState(currentJenis);

cards.forEach(card => {
    card.addEventListener('click', function () {
        document.querySelector(`.jenis-radio[value="${this.dataset.jenis}"]`).click();
    });
});

const blockNumbers = (el) => {
    if (el) {
        el.addEventListener('input', function() {
            this.value = this.value.replace(/[0-9]/g, '');
        });
    }
};
blockNumbers(document.getElementById('satuan_barang'));
blockNumbers(document.getElementById('jumlah_makanan_satuan'));

function previewFile(input) {
    const label = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        label.textContent = '✓ ' + input.files[0].name;
        label.classList.remove('hidden');
    }
}
</script>
@endpush
