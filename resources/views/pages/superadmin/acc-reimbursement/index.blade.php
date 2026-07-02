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


<script>
    function openTolakModal(id) {
        document.getElementById('form-tolak').action =
            '/superadmin/acc-reimbursement/' + id + '/batalkan';

        document.getElementById('modal-tolak').classList.remove('hidden');
    }

    function confirmApprove(event, total, saldo) {
        if (total > saldo) {
            alert("Persetujuan Gagal!\n\nSaldo keuangan tidak mencukupi untuk menyetujui reimbursement ini.\n\nSaldo saat ini: Rp " + saldo.toLocaleString('id-ID') + "\nNominal reimbursement: Rp " + total.toLocaleString('id-ID'));
            event.preventDefault();
            return false;
        }
        return confirm("Apakah Anda yakin ingin menyetujui ajuan reimbursement sebesar Rp " + total.toLocaleString('id-ID') + "?");
    }
</script>
@endsection
