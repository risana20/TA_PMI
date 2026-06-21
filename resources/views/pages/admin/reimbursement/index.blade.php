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
                            • {{ $d->nama_kebutuhan }}
                            ({{ $d->jenisLogistik->nama_jenis_logistik ?? '-' }})
                            - Rp {{ number_format($d->nominal, 0, ',', '.') }}
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

{{-- Modal Ajukan --}}
<div id="modal-ajukan" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6">

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
                <div class="item space-y-2 border p-3 rounded-lg mb-3">

                    <input type="text"
                        name="details[0][nama_kebutuhan]"
                        placeholder="Nama kebutuhan"
                        class="w-full border rounded px-3 py-2 text-sm">

                    <input type="number"
                        name="details[0][nominal]"
                        placeholder="Nominal"
                        min="0"
                        class="w-full border rounded px-3 py-2 text-sm">

                    <select name="details[0][jenis_logistik_id]"
                        class="w-full border rounded px-3 py-2 text-sm">

                        <option value="">Pilih Jenis Logistik</option>

                        @foreach($jenisLogistiks as $j)
                            <option value="{{ $j->id }}"
                                {{ old('details.0.jenis_logistik_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->nama_jenis_logistik }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="button"
                onclick="addItem()"
                class="text-sm text-blue-600 font-semibold">
                + Tambah Item
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

<script>
let index = document.querySelectorAll('#items .item').length;

const jenisLogistikOptions = `
    @foreach($jenisLogistiks as $j)
        <option value="{{ $j->id }}">
            {{ $j->nama_jenis_logistik }}
        </option>
    @endforeach
`;

function addItem() {
    const container = document.getElementById('items');

    container.insertAdjacentHTML('beforeend', `
        <div class="item space-y-2 border p-3 rounded-lg mb-3">

            <input type="text"
                name="details[${index}][nama_kebutuhan]"
                placeholder="Nama kebutuhan"
                class="w-full border rounded px-3 py-2 text-sm"
                required>

            <input type="number"
                name="details[${index}][nominal]"
                placeholder="Nominal"
                min="0"
                class="w-full border rounded px-3 py-2 text-sm"
                required>

            <select
                name="details[${index}][jenis_logistik_id]"
                class="w-full border rounded px-3 py-2 text-sm"
                required>

                <option value="">Pilih Jenis Logistik</option>

                ${jenisLogistikOptions}

            </select>

            <button type="button"
                onclick="this.parentElement.remove()"
                class="text-red-600 text-sm font-semibold">
                Hapus Item
            </button>

        </div>
    `);

    index++;
}


@if ($errors->any())
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('modal-ajukan').classList.remove('hidden');
});
@endif
</script>

@endsection
