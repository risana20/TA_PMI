@extends('layout.app')

@section('title', 'Ajuan Reimbursement')

@section('content')

@include('sections.page-header', ['title' => 'Ajuan Reimbursement', 'subtitle' => 'Kelola ajuan penggantian biaya'])

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
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nominal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Jenis Pengeluaran</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Keterangan</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reimbursements as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-4 text-gray-600">{{ $r->tgl_pengajuan->format('d M Y') }}</td>
                <td class="px-4 py-4 font-semibold">Rp {{ number_format($r->nominal, 0, ',', '.') }}</td>
                <td class="px-4 py-4 text-gray-600">{{ $r->jenis_pengeluaran }}</td>
                <td class="px-4 py-4 text-gray-600">{{ $r->keterangan ?? '-' }}</td>
                <td class="px-4 py-4">@include('components.badge-status', ['status' => $r->status])</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada ajuan reimbursement</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $reimbursements->links() }}</div>
</div>

{{-- Modal Ajukan --}}
<div id="modal-ajukan" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Ajukan Reimbursement</h3>
            <button onclick="document.getElementById('modal-ajukan').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'reimbursement.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                <input type="number" name="nominal" min="1" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengeluaran</label>
                <input type="text" name="jenis_pengeluaran" required placeholder="Transport, Konsumsi, ..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-ajukan').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 rounded-lg px-4 py-2 text-sm">Batal</button>
                <button type="submit" class="bg-red-600 text-white font-semibold rounded-lg px-4 py-2 text-sm">Kirim Ajuan</button>
            </div>
        </form>
    </div>
</div>

@endsection
