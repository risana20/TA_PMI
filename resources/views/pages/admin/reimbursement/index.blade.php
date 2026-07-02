@extends('layout.app')

@section('title', 'Ajuan Reimbursement')

@section('content')

@include('sections.page-header', [
    'title' => 'Ajuan Reimbursement',
    'subtitle' => 'Kelola ajuan penggantian biaya'
])

<div class="flex justify-end mb-4">
    <button onclick="document.getElementById('modal-ajukan').classList.remove('hidden')"
        class="bg-red-600 text-white rounded-full px-5 py-2 font-semibold text-sm hover:bg-red-700 flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Ajukan Reimbursement
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">

    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Total</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Detail</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nota</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Keterangan</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @forelse($reimbursements as $r)
            <tr class="hover:bg-gray-50">

                <td class="px-4 py-4 text-gray-600">
                    {{ $r->tgl_pengajuan->format('d M Y') }}
                </td>

                <td class="px-4 py-4 font-semibold">
                    Rp {{ number_format($r->total, 0, ',', '.') }}
                </td>

                <td class="px-4 py-4 text-gray-600">
                   @foreach($r->detailReimbursements as $d)
                        <div class="text-sm">
                            • {{ $d->itemLogistik->nama_item ?? '-' }}
                            ({{ $d->jumlah }} {{ $d->itemLogistik->satuan ?? '' }})
                            - Rp {{ number_format($d->nominal,0,',','.') }}
                        </div>
                    @endforeach
                </td>
                <td class="px-4 py-4">
                    @if($r->bukti_nota)
                        <a href="{{ asset('storage/' . $r->bukti_nota) }}"
                        target="_blank"
                        class="text-blue-600 hover:underline">
                            Lihat Nota
                        </a>
                    @else
                        <span class="text-gray-400">Tidak ada</span>
                    @endif
                </td>

                <td class="px-4 py-4">
                    @include('components.badge-status', ['status' => $r->status])
                </td>
                <td class="px-5 py-4 text-gray-600">
                    @if($r->status == 'Ditolak')
                        {{ $r->alasan_tolak ?? '-' }}
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                    Belum ada ajuan reimbursement
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-4 py-3 border-t border-gray-100">
        {{ $reimbursements->links() }}
    </div>
</div>

<div id="modal-ajukan" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 max-h-[85vh] overflow-y-auto relative">

        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Ajukan Reimbursement</h3>

            <button onclick="document.getElementById('modal-ajukan').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600">
                ✕
            </button>
        </div>
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
             enctype="multipart/form-data"
             action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'reimbursement.store') }}"
             class="space-y-4">

            @csrf

            <!-- DETAIL ITEM -->
            <div id="items">
                <div class="item border rounded-lg p-4 mb-3 space-y-3">

                    {{-- Item Logistik --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Nama Kebutuhan
                        </label>

                        <select
                            name="details[0][item_logistik_id]"
                            class="item-select w-full border rounded px-3 py-2 text-sm"
                            required>

                            <option value="">Pilih Item Logistik</option>

                            @foreach($itemLogistiks as $item)

                                <option
                                    value="{{ $item->id }}"
                                    data-satuan="{{ $item->satuan }}">

                                    {{ $item->nama_item }}

                                </option>

                            @endforeach

                        </select>

                        <button
                            type="button"
                            onclick="openModalTambahItem(this)"
                            class="mt-2 text-sm text-blue-600 hover:underline">

                            + Tambahkan Item

                        </button>

                    </div>

                    {{-- Satuan --}}
                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Satuan
                        </label>

                        <input
                            type="text"
                            class="satuan w-full border rounded px-3 py-2 bg-gray-100"
                            readonly>

                    </div>

                    {{-- Jumlah --}}
                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Jumlah
                        </label>

                        <input
                            type="number"
                            name="details[0][jumlah]"
                            min="1"
                            class="w-full border rounded px-3 py-2 text-sm"
                            required>

                    </div>

                    {{-- Nominal --}}
                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Nominal
                        </label>

                        <input
                            type="number"
                            name="details[0][nominal]"
                            min="1"
                            class="w-full border rounded px-3 py-2 text-sm"
                            required>
                    </div>

                </div>

            </div>
            <button type="button"
                onclick="addItem()"
                class="text-sm text-blue-600 font-semibold">
                + Tambah Item Reimbursement
            </button>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Nota</label>
                <input type="file" name="bukti_nota" 
                    class="w-full border rounded px-3 py-2 text-sm">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button"
                    onclick="document.getElementById('modal-ajukan').classList.add('hidden')"
                    class="border px-4 py-2 rounded text-sm">
                    Batal
                </button>

                <button type="submit"
                    class="bg-red-600 text-white px-4 py-2 rounded text-sm font-semibold">
                    Kirim Ajuan
                </button>
            </div>
        </form>
    </div>
</div>
{{-- Modal Tambah Item Logistik --}}
<div id="modal-tambah-item"
    class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">

        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Tambah Item Logistik</h3>

            <button type="button"
                onclick="closeModalTambahItem()"
                class="text-gray-400 hover:text-gray-600">
                ✕
            </button>
        </div>

        <div class="space-y-4">

            {{-- Nama Item --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Item
                </label>

                <input type="text"
                    id="nama_item_baru"
                    class="w-full border rounded-lg px-3 py-2"
                    placeholder="Masukkan nama item">
            </div>

            {{-- Jenis Logistik --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Jenis Logistik
                </label>

                <select id="jenis_logistik_baru"
                    class="w-full border rounded-lg px-3 py-2">

                    <option value="">Pilih Jenis Logistik</option>

                    @foreach($jenisLogistiks as $jenis)
                        <option value="{{ $jenis->id }}">
                            {{ $jenis->nama_jenis_logistik }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- Satuan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Satuan
                </label>

                <input type="text"
                    id="satuan_baru"
                    class="w-full border rounded-lg px-3 py-2"
                    placeholder="Contoh: Kg, Pcs, Box">
            </div>

        </div>

        <div class="flex justify-end gap-3 mt-6">

            <button
                type="button"
                onclick="closeModalTambahItem()"
                class="border px-4 py-2 rounded-lg">
                Batal
            </button>

            <button
                type="button"
                onclick="simpanItemBaru()"
                class="bg-red-600 text-white px-4 py-2 rounded-lg">
                Simpan
            </button>

        </div>

    </div>

</div>

<script>

    let tomSelectInstances = [];
    let index = document.querySelectorAll('#items .item').length;
    const itemData = {
        @foreach($itemLogistiks as $item)
                "{{ $item->id }}": {
                    satuan: "{{ $item->satuan }}"
                },
            @endforeach
        };

    function initTomSelect(select){

    const ts = new TomSelect(select,{

        create:false,

        onChange:function(value){
            console.log(value);
            console.log(itemData[value]);

            const item = itemData[value];
            const inputSatuan = select.closest(".item").querySelector(".satuan");

            if(!item) {
                inputSatuan.value = "";
                return;
            }

            inputSatuan.value = item.satuan;

        }

    });

    tomSelectInstances.push(ts);

    return ts;
    }
    document.addEventListener("DOMContentLoaded",function(){

        document.querySelectorAll(".item-select").forEach(select=>{

            initTomSelect(select);

        });

    });
    

    let optionItems = `
        @foreach($itemLogistiks as $item)

        <option
        value="{{ $item->id }}"
        data-satuan="{{ $item->satuan }}">

        {{ $item->nama_item }}

        </option>

        @endforeach
    `;

    function addItem(){

        const html=`

        <div class="item border rounded-lg p-4 mb-3 space-y-3">

        <div>

        <label class="block text-sm font-medium mb-1">
        Nama Kebutuhan
        </label>

        <select
        name="details[${index}][item_logistik_id]"
        class="item-select w-full"
        required>

        <option value="">
        Pilih Item
        </option>

        ${optionItems}

        </select>

        <button
        type="button"
        onclick="openModalTambahItem(this)"
        class="mt-2 text-sm text-blue-600">

        + Tambahkan Item

        </button>

        </div>

        <div>

        <label class="block text-sm font-medium mb-1">
        Satuan
        </label>

        <input
        type="text"
        class="satuan w-full border rounded px-3 py-2 bg-gray-100"
        readonly>

        </div>

        <div>

        <label class="block text-sm font-medium mb-1">
        Jumlah
        </label>

        <input
        type="number"
        name="details[${index}][jumlah]"
        min="1"
        required
        class="w-full border rounded px-3 py-2">

        </div>

        <div>

        <label class="block text-sm font-medium mb-1">
        Nominal
        </label>

        <input
        type="number"
        name="details[${index}][nominal]"
        min="1"
        required
        class="w-full border rounded px-3 py-2">

        </div>

        <button
        type="button"
        class="text-red-600"
        onclick="removeItem(this)">

        Hapus Item

        </button>

        </div>

        `;

            const container=document.getElementById("items");

            container.insertAdjacentHTML("beforeend",html);

            const selectBaru=container.querySelectorAll(".item-select");

            initTomSelect(selectBaru[selectBaru.length-1]);

            index++;

        }

    let activeTomSelect = null;

    function openModalTambahItem(button) {
        if (button) {
            const itemDiv = button.closest('.item');
            const selectEl = itemDiv.querySelector('.item-select');
            let ts = selectEl.tomselect;
            if (!ts) {
                ts = tomSelectInstances.find(instance => instance.input === selectEl);
            }
            activeTomSelect = ts || tomSelectInstances[tomSelectInstances.length - 1];
        } else {
            activeTomSelect = tomSelectInstances[tomSelectInstances.length - 1];
        }
        document.getElementById('modal-tambah-item').classList.remove('hidden');
    }

    function closeModalTambahItem() {
        document.getElementById('modal-tambah-item').classList.add('hidden');
    }

    function removeItem(button) {
        const itemDiv = button.closest('.item');
        const select = itemDiv.querySelector('.item-select');
        if (select && select.tomselect) {
            const ts = select.tomselect;
            tomSelectInstances = tomSelectInstances.filter(instance => instance !== ts);
            ts.destroy();
        }
        itemDiv.remove();
    }

    async function simpanItemBaru() {

        let nama = document.getElementById('nama_item_baru').value;
        let satuan = document.getElementById('satuan_baru').value;
        let jenis = document.getElementById('jenis_logistik_baru').value;

        if (!nama || !satuan || !jenis) {
            alert('Semua field wajib diisi.');
            return;
        }

        try {

            const response = await fetch("{{ route('admin.item-logistik.store') }}", {

                method: "POST",

                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },

                body: JSON.stringify({
                    nama_item: nama,
                    satuan: satuan,
                    jenis_logistik_id: jenis
                })

            });

            const result = await response.json();

            if(!response.ok){

                alert("Gagal menambahkan item.");

                return;

            }

            tambahItemKeDropdown(result.item);
            document.getElementById("nama_item_baru").value="";
            document.getElementById("jenis_logistik_baru").value="";
            document.getElementById("satuan_baru").value="";

            closeModalTambahItem();

            } catch(error){

                console.log(error);

                alert("Terjadi kesalahan.");

            }

    }

    function tambahItemKeDropdown(item){

        // 1. Add to itemData
        itemData[item.id] = {
            satuan: item.satuan
        };

        // 2. Append to optionItems for future added items
        optionItems += `
            <option value="${item.id}" data-satuan="${item.satuan}">
                ${item.nama_item}
            </option>
        `;

        // 3. Add to all existing TomSelect dropdown options
        tomSelectInstances.forEach(ts => {
            ts.addOption({
                value: item.id,
                text: item.nama_item,
                satuan: item.satuan
            });
            ts.refreshOptions(false);
        });

        // 4. Set the value of the active TomSelect dropdown
        if (activeTomSelect) {
            activeTomSelect.setValue(item.id);
        }

    }

    @if ($errors->any())
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('modal-ajukan').classList.remove('hidden');
    });
    @endif
</script>

@endsection
