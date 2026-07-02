@extends('layout.app')

@section('title', 'ACC Reimbursement')

@section('content')

@include('sections.page-header', ['title' => 'ACC Reimbursement', 'subtitle' => 'Validasi ajuan reimbursement dari admin'])

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
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Admin</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Detail Pengeluaran</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">total</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nota</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Keterangan</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reimbursements as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-4 text-gray-600">{{ $r->tgl_pengajuan->format('d M Y') }}</td>
                <td class="px-4 py-4 font-medium text-gray-900">{{ $r->user->name }}</td>
                <td class="px-4 py-4 text-gray-600">
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
                <td class="px-4 py-4">
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

                <td class="px-5 py-4 text-gray-600">
                    @if($r->status == 'Ditolak')
                        {{ $r->alasan_tolak ?? '-' }}
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="px-4 py-4">
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
</script>
@endsection
