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
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Detail Pengeluaran</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">total</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Nota</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
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
                                <strong>{{ $detail->nama_kebutuhan }}</strong>
                            </div>

                            <div>
                                Jenis:
                                {{ $detail->jenisLogistik->nama_jenis_logistik ?? '-' }}
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
                <td class="px-4 py-4">
                @if($r->status === 'Tunggu Verifikasi')

                    <form method="POST"
                        action="{{ route('superadmin.acc-reimbursement.validasi', $r) }}"
                        class="inline">
                        @csrf
                        <button type="submit"
                            class="text-green-600 hover:text-green-800 text-xs font-medium mr-2">
                            Setujui
                        </button>
                    </form>

                    <form method="POST"
                        action="{{ route('superadmin.acc-reimbursement.batalkan', $r) }}"
                        class="inline">
                        @csrf
                        <button type="submit"
                            class="text-red-600 hover:text-red-800 text-xs font-medium">
                            Tolak
                        </button>
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
