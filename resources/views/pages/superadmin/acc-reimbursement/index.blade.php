@extends('layout.app')

@section('title', 'ACC Reimbursement')

@section('content')

@include('sections.page-header', ['title' => 'ACC Reimbursement', 'subtitle' => 'Validasi ajuan reimbursement dari admin'])

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Admin</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nominal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Jenis Pengeluaran</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Keterangan</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reimbursements as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-4 text-gray-600">{{ $r->tgl_pengajuan->format('d M Y') }}</td>
                <td class="px-4 py-4 font-medium text-gray-900">{{ $r->user->name }}</td>
                <td class="px-4 py-4 font-semibold">Rp {{ number_format($r->nominal, 0, ',', '.') }}</td>
                <td class="px-4 py-4 text-gray-600">{{ $r->jenis_pengeluaran }}</td>
                <td class="px-4 py-4 text-gray-600">{{ $r->keterangan ?? '-' }}</td>
                <td class="px-4 py-4">@include('components.badge-status', ['status' => $r->status])</td>
                <td class="px-4 py-4">
                    @if($r->status === 'DIPROSES')
                    <form method="POST" action="{{ route('superadmin.acc-reimbursement.validasi', $r) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-green-500 hover:text-green-700 text-xs font-medium mr-2">Validasi</button>
                    </form>
                    @endif
                    @if($r->status === 'DIVALIDASI')
                    <form method="POST" action="{{ route('superadmin.acc-reimbursement.batalkan', $r) }}" class="inline"
                        onsubmit="return confirm('Batalkan validasi ini?')">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Batalkan</button>
                    </form>
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

@endsection
