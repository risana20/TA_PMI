@extends('layout.app')

@section('title', 'ACC Reimbursement')

@section('content')

@include('sections.page-header', ['title' => 'ACC Reimbursement', 'subtitle' => 'Validasi ajuan reimbursement dari admin'])
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">

    {{-- Pencarian --}}
    <form method="GET"
        action="{{ route('superadmin.acc-reimbursement.index') }}"
        id="filterForm"
        class="w-full lg:w-auto">

        <div class="relative w-full lg:w-72">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

            <input
                type="text"
                name="search"
                id="searchInput"
                value="{{ request('search') }}"
                placeholder="Cari Nama..."
                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>

    </form>

    {{-- Tombol --}}
    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">

        <a href="{{ route('superadmin.acc-reimbursement.export.pdf', request()->query()) }}"
            class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm font-medium flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-download"></i>
            <span>Ekspor PDF</span>
        </a>

        <a href="{{ route('superadmin.acc-reimbursement.export.excel', request()->query()) }}"
            class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm font-medium flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-file-excel"></i>
            <span>Ekspor Excel</span>
        </a>

        <button
            onclick="document.getElementById('modal-ajukan').classList.remove('hidden')"
            class="bg-red-600 hover:bg-red-700 text-white rounded-lg px-5 py-2 text-sm font-semibold flex items-center justify-center gap-2 transition">

            <i class="fa-solid fa-plus"></i>
            <span>Ajukan Reimbursement</span>

        </button>

    </div>

</div>
<div class="mb-4 flex justify-between items-center">
    <div class="bg-gray-100 border border-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2">
        <i class="fa-solid fa-wallet text-gray-500"></i>
        <span>Saldo Keuangan Saat Ini: <strong class="text-gray-900">Rp {{ number_format($saldo, 0, ',', '.') }}</strong></span>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Admin</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Detail Pengeluaran</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">total</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nota</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Keterangan</th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Aksi</th>
                <th class="md:hidden px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Detail</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reimbursements as $r)
            <tr class="hover:bg-gray-50">
                <td class="hidden md:table-cell px-4 py-4 text-gray-600">{{ $r->tgl_pengajuan->format('d M Y') }}</td>
                <td class="px-4 py-4 font-medium text-gray-900">{{ $r->user->name }}</td>
                <td class="hidden md:table-cell px-4 py-4 text-gray-600">
                    @foreach($r->detailReimbursements as $detail)
                        <div class="mb-2 text-sm border-b pb-1">
                            <div>
                                <strong>{{ $detail->itemLogistik->nama_item ?? '-' }}</strong>
                            </div>

                            <div>
                                Jenis:
                                {{ $detail->itemLogistik->jenisLogistik->nama_jenis_logistik ?? '-' }}
                            </div>

                            <div>
                                Jumlah:
                                {{ $detail->jumlah }}
                                {{ $detail->itemLogistik->satuan ?? '' }}
                            </div>

                            <div>
                                Rp {{ number_format($detail->nominal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </td>
                <td class="px-4 py-4 font-semibold">Rp {{ number_format($r->total, 0, ',', '.') }}</td>
                <td class="hidden md:table-cell px-4 py-4">
                    @if($r->bukti_nota)
                        <a href="{{ Storage::url($r->bukti_nota) }}"
                        target="_blank"
                        class="text-blue-600 hover:underline">
                            Lihat Nota
                        </a>
                    @else
                        <span class="text-gray-400">Tidak Ada</span>
                    @endif
                </td>
                <td class="px-4 py-4">@include('components.badge-status', ['status' => $r->status])</td>

                <td class="hidden md:table-cell px-5 py-4 text-gray-600">
                    @if($r->status == 'Ditolak')
                        {{ $r->alasan_tolak ?? '-' }}
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="hidden md:table-cell px-4 py-4">
                    @if($r->status === 'Tunggu Verifikasi')

                        <div class="flex items-center gap-2">

                            <form action="{{ route('superadmin.acc-reimbursement.validasi', $r->id) }}"
                                method="POST"
                                class="inline"
                                onsubmit="return confirmApprove(event, {{ $r->total }}, {{ $saldo }})">

                                @csrf

                                <button
                                    type="submit"
                                    class="text-green-600 hover:text-green-800 text-xs font-medium">
                                    Setujui
                                </button>

                            </form>

                            <button type="button"
                                onclick="openTolakModal({{ $r->id }})"
                                class="text-red-600 hover:text-red-800 text-xs font-medium">
                                Tolak
                            </button>

                        </div>

                    @endif
                </td>
                <td class="md:hidden px-4 py-4">
                    <button
                        onclick='openPreview(
                            @json($r->tgl_pengajuan->format("d M Y")),
                            @json($r->user->name),
                            @json($r->detailReimbursements->load("itemLogistik.jenisLogistik")),
                            @json($r->total),
                            @json($r->bukti_nota ? Storage::url($r->bukti_nota) : ""),
                            @json($r->status),
                            @json($r->alasan_tolak ?? "-"),
                            {{ $r->id }},
                            {{ $saldo }}
                        )'
                        class="text-blue-600 hover:text-blue-800">

                        <i class="fa-solid fa-eye"></i>
                    </button>
                </td>
            
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada ajuan reimbursement</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $reimbursements->links() }}</div>
</div>
{{-- Modal Tolak --}}
<div id="modal-tolak" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        {{-- Header merah PMI --}}
        <div class="flex items-center justify-between px-6 py-4 bg-[#CC0001] rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-ban text-white text-sm"></i>
                </div>
                <h3 class="font-bold text-white text-base">Tolak Reimbursement</h3>
            </div>
            <button onclick="document.getElementById('modal-tolak').classList.add('hidden')" class="text-white/70 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="form-tolak" method="POST" class="p-6 space-y-4">
            @csrf
            <p class="text-sm text-gray-500">Berikan alasan penolakan yang jelas agar admin dapat memahami keputusan ini.</p>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alasan Penolakan <span class="text-[#CC0001]">*</span></label>
                <textarea name="alasan_tolak" rows="3" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#CC0001] focus:border-transparent resize-none transition"
                    placeholder="Masukkan alasan penolakan..."></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modal-tolak').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-xl px-5 py-2.5 text-sm font-medium transition">Batal</button>
                <button type="submit" class="bg-[#CC0001] hover:bg-red-800 text-white font-semibold rounded-xl px-5 py-2.5 text-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-ban text-xs"></i> Tolak
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Saldo Tidak Mencukupi --}}
<div id="modal-insufficient" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        {{-- Header --}}
        <div class="px-6 py-5 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-wallet text-[#CC0001] text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Saldo Tidak Mencukupi</h3>
            <p class="text-sm text-gray-500">Persetujuan tidak dapat diproses karena saldo keuangan PMI tidak mencukupi untuk menutupi nominal reimbursement ini.</p>
        </div>
        {{-- Saldo Info --}}
        <div class="mx-6 mb-5 bg-gray-50 rounded-xl border border-gray-100 divide-y divide-gray-100">
            <div class="flex items-center justify-between px-4 py-3">
                <span class="text-xs text-gray-500 font-medium">Saldo Tersedia</span>
                <span id="modal-insuff-saldo" class="text-sm font-bold text-gray-700">—</span>
            </div>
            <div class="flex items-center justify-between px-4 py-3">
                <span class="text-xs text-gray-500 font-medium">Nominal Reimbursement</span>
                <span id="modal-insuff-total" class="text-sm font-bold text-[#CC0001]">—</span>
            </div>
            <div class="flex items-center justify-between px-4 py-3">
                <span class="text-xs text-gray-500 font-medium">Kekurangan</span>
                <span id="modal-insuff-diff" class="text-sm font-bold text-[#CC0001]">—</span>
            </div>
        </div>
        <div class="px-6 pb-6">
            <button onclick="document.getElementById('modal-insufficient').classList.add('hidden')"
                class="w-full bg-[#CC0001] hover:bg-red-800 text-white font-semibold rounded-xl py-2.5 text-sm transition">
                Mengerti
            </button>
        </div>
    </div>
</div>

{{-- Modal: Konfirmasi Setujui --}}
<div id="modal-confirm-approve" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-5 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-circle-check text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Konfirmasi Persetujuan</h3>
            <p class="text-sm text-gray-500 mb-4">Apakah Anda yakin ingin menyetujui ajuan reimbursement sebesar:</p>
            <p id="modal-confirm-total" class="text-2xl font-bold text-gray-900 mb-1">—</p>
            <p class="text-xs text-gray-400">Tindakan ini tidak dapat dibatalkan setelah disetujui.</p>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button onclick="document.getElementById('modal-confirm-approve').classList.add('hidden'); _pendingApproveForm = null;"
                class="flex-1 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-xl py-2.5 text-sm font-medium transition">Batal</button>
            <button id="btn-confirm-approve" onclick="_submitApprove()"
                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl py-2.5 text-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-check text-xs"></i> Ya, Setujui
            </button>
        </div>
    </div>
</div>
<div id="modal-preview"
    class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center mb-5">

            <h3 class="text-xl font-bold">
                Detail Reimbursement
            </h3>

            <button onclick="closePreview()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

        </div>

        <div class="grid grid-cols-2 gap-4 mb-5">

            <div>
                <p class="text-gray-500 text-sm">Tanggal</p>
                <p id="preview-tanggal"></p>
            </div>

            <div>
                <p class="text-gray-500 text-sm">Admin</p>
                <p id="preview-admin"></p>
            </div>

            <div>
                <p class="text-gray-500 text-sm">Total</p>
                <p id="preview-total"></p>
            </div>

            <div>
                <p class="text-gray-500 text-sm">Status</p>
                <div id="preview-status"></div>
            </div>

        </div>

        <div class="mb-5">

            <h4 class="font-semibold mb-2">
                Detail Pengeluaran
            </h4>

            <div id="preview-detail"></div>

        </div>

        <div class="mb-5">

            <h4 class="font-semibold mb-2">
                Bukti Nota
            </h4>

            <div id="preview-nota"></div>

        </div>

        <div class="mb-5">

            <h4 class="font-semibold mb-2">
                Keterangan
            </h4>

            <div id="preview-keterangan"
                class="bg-gray-50 rounded-lg p-3">
            </div>

        </div>

        <div id="preview-action"
            class="flex justify-end gap-2">
        </div>

    </div>

</div>


<script>
    var _pendingApproveForm = null;

    function openTolakModal(id) {
        document.getElementById('form-tolak').action =
            '/superadmin/acc-reimbursement/' + id + '/batalkan';
        document.getElementById('modal-tolak').classList.remove('hidden');
    }

    function confirmApprove(event, total, saldo) {
        event.preventDefault();

        if (total > saldo) {
            // Tampilkan modal saldo tidak mencukupi
            var diff = total - saldo;
            document.getElementById('modal-insuff-saldo').textContent = 'Rp ' + saldo.toLocaleString('id-ID');
            document.getElementById('modal-insuff-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('modal-insuff-diff').textContent  = 'Rp ' + diff.toLocaleString('id-ID');
            document.getElementById('modal-insufficient').classList.remove('hidden');
            return false;
        }

        // Tampilkan modal konfirmasi
        _pendingApproveForm = event.target.closest('form');
        document.getElementById('modal-confirm-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('modal-confirm-approve').classList.remove('hidden');
        return false;
    }

    function _submitApprove() {
        document.getElementById('modal-confirm-approve').classList.add('hidden');
        if (_pendingApproveForm) {
            _pendingApproveForm.submit();
            _pendingApproveForm = null;
        }
    }

    function openPreview(tanggal,admin,detail,total,nota,status,keterangan,id,saldo){

        document.getElementById('preview-tanggal').textContent=tanggal;
        document.getElementById('preview-admin').textContent=admin;
        document.getElementById('preview-total').textContent=
            "Rp "+Number(total).toLocaleString('id-ID');

        document.getElementById('preview-status').innerHTML=status;

        let html='';

        detail.forEach(function(item){

            html+=`
                <div class="border rounded-lg p-3 mb-2">

                    <div><strong>${item.item_logistik?.nama_item ?? '-'}</strong></div>

                    <div>
                        Jenis :
                        ${item.item_logistik?.jenis_logistik?.nama_jenis_logistik ?? '-'}
                    </div>

                    <div>
                        Jumlah :
                        ${item.jumlah}
                        ${item.item_logistik?.satuan ?? ''}
                    </div>

                    <div>
                        Rp ${Number(item.nominal).toLocaleString('id-ID')}
                    </div>

                </div>
            `;

        });

        document.getElementById('preview-detail').innerHTML=html;

        if(nota){

            document.getElementById('preview-nota').innerHTML=
            `<a href="${nota}" target="_blank"
                class="text-blue-600 hover:underline">
                Lihat Nota
            </a>`;

        }else{

            document.getElementById('preview-nota').innerHTML=
            `<span class="text-gray-400">Tidak ada</span>`;

        }

        document.getElementById('preview-keterangan').textContent=
            keterangan ?? '-';

        let aksi='';

        if(status==='Tunggu Verifikasi'){

            aksi=`
                <form
                    method="POST"
                    action="/superadmin/acc-reimbursement/${id}/validasi">

                    @csrf

                    <button
                        class="px-4 py-2 bg-green-600 text-white rounded-lg">

                        Setujui

                    </button>

                </form>

                <button
                    onclick="openTolakModal(${id})"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg">

                    Tolak

                </button>
            `;

        }

        document.getElementById('preview-action').innerHTML=aksi;

        document.getElementById('modal-preview').classList.remove('hidden');

    }

    function closePreview(){

        document.getElementById('modal-preview').classList.add('hidden');

    }
</script>
@endsection
