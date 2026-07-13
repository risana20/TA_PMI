@extends('layout.app')

@section('title', 'Keuangan')

@section('content')

@include('sections.page-header', ['title' => 'Keuangan', 'subtitle' => 'Kelola pemasukan dan pengeluaran'])
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- Summary Cards --}}

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    
    @include('ui.card-stat', [
        'label' => 'Total Pemasukan',
        'value' => 'Rp ' . number_format($totalPemasukan, 0, ',', '.'),
        'icon' => 'fa-arrow-down',
        'iconBg' => 'bg-green-100',
        'iconColor' => 'text-green-600',
    ])
    @include('ui.card-stat', [
        'label' => 'Total Pengeluaran',
        'value' => 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'),
        'icon' => 'fa-arrow-up',
        'iconBg' => 'bg-red-100',
        'iconColor' => 'text-red-600',
    ])
    @include('ui.card-stat', [
        'label' => 'Saldo',
        'value' => 'Rp ' . number_format($saldo, 0, ',', '.'),
        'icon' => 'fa-wallet',
        'iconBg' => $saldo >= 0 ? 'bg-blue-100' : 'bg-red-100',
        'iconColor' => $saldo >= 0 ? 'text-blue-600' : 'text-red-600',
    ])
</div>

{{-- chart --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
    <div class="bg-white p-5 rounded shadow">
        <form method="GET">

            <input type="hidden" 
            name="bulan" 
            value="{{ $bulan }}">


            <select 
            name="tahun" 
            onchange="this.form.submit()"
            class="border rounded p-2">


            @for($i=2024;$i<=date('Y');$i++)

            <option value="{{$i}}"
            {{ (int)$tahun === $i ? 'selected' : '' }}>

            {{$i}}

            </option>

            @endfor


            </select>

        </form>
        <h3>
            Pengeluaran Tahun {{ $tahun}}
        </h3>
        <canvas id="barPengeluaran" height="120" ></canvas>
    </div>
    <div class="bg-white p-5 rounded shadow">
        <form method="GET">

            <input type="hidden" 
            name="tahun" 
            value="{{ $tahun }}">


            <select 
            name="bulan" 
            onchange="this.form.submit()"
            class="border rounded p-2">


            @for($i=1;$i<=12;$i++)

            <option value="{{$i}}"
            {{ (int)$bulan === $i ? 'selected' : '' }}>

            {{ date('F', mktime(0,0,0,$i,1)) }}

            </option>

            @endfor


            </select>

        </form>
        <h3>Barang Dibeli Bulan {{ date('F', mktime(0,0,0,$bulan,1)) }}</h3>
        <canvas id="donutBarang" height="120"></canvas>
    </div>
</div>

<div class="grid grid-cols-1  gap-6 mb-6">
    {{-- Tabel Pemasukan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Pemasukan</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400">Tanggal</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400">donatur</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400">Nominal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pemasukans as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-600">{{ $p->donasi->created_at->format('d M Y') }}</td>
                    <td>{{ $p->donasi->user->name }}</td>
                    <td class="px-4 py-3 font-semibold text-green-600">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-4 text-center text-gray-400 text-xs">Belum ada pemasukan</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-2 border-t border-gray-100 text-xs">{{ $pemasukans->links() }}</div>
    </div>

    {{-- Tabel Pengeluaran --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Pengeluaran</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400">Tanggal</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400">Detail Reimbursements</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400">Total</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400">Nota</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pengeluarans as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">{{ $p->tgl_validasi ? $p->tgl_validasi->format('d M Y') : $p->tgl_pengajuan->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            @forelse($p->detailReimbursements as $detail)

                                <div>
                                    {{ $detail->itemLogistik->nama_item ?? '-' }}
                                    ({{ $detail->itemLogistik->jenisLogistik->nama_jenis_logistik ?? '-' }})
                                    - {{ $detail->jumlah }} {{ $detail->itemLogistik->satuan ?? '' }}
                                    - Rp {{ number_format($detail->nominal,0,',','.') }}
                                </div>

                            @empty

                                <span class="text-gray-400">
                                    Tidak ada detail
                                </span>

                            @endforelse
                        </td>
                        <td class="px-4 py-3 font-semibold text-red-600">
                            Rp {{ number_format($p->total, 0, ',', '.') }}
                        </td>

                        <td class="px-4 py-3">
                            @if($p->bukti_nota)
                                <button 
                                    onclick="lihatNota('{{ asset('storage/'.$p->bukti_nota) }}')"
                                    class="text-blue-600 hover:underline text-sm">
                                    Lihat
                                </button>
                            @else
                                <span class="text-gray-400 text-xs">Tidak ada</span>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-400 text-xs">
                        Belum ada pengeluaran
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-2 border-t border-gray-100 text-xs">{{ $pengeluarans->links() }}</div>
    </div>
</div>

{{-- Modal Pemasukan
<div id="modal-pemasukan" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Tambah Pemasukan</h3>
            <button onclick="document.getElementById('modal-pemasukan').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'keuangan.storePemasukan') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                <input type="number" name="nominal" min="1" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <input type="text" name="kategori" required placeholder="Donasi, Bantuan Pemerintah, ..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-pemasukan').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 rounded-lg px-4 py-2 text-sm">Batal</button>
                <button type="submit" class="bg-red-600 text-white font-semibold rounded-lg px-4 py-2 text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Pengeluaran --}}
{{-- <div id="modal-pengeluaran" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Tambah Pengeluaran</h3>
            <button onclick="document.getElementById('modal-pengeluaran').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'keuangan.storePengeluaran') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                <input type="number" id="nominalPengeluaran" name="nominal" min="1"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">

                <p id="errorSaldo" class="text-red-500 text-xs mt-1 hidden"></p>

                @error('nominal')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengeluaran</label>
                <input type="text" name="kategori" required placeholder="Logistik, Operasional, ..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Nota (opsional)</label>
                <input type="file" name="bukti_nota" accept="image/*"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-pengeluaran').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 rounded-lg px-4 py-2 text-sm">Batal</button>
                <button type="submit" id="btnSimpanPengeluaran"
                    class="bg-red-600 text-white font-semibold rounded-lg px-4 py-2 text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div> --}}
{{-- modal lihat nota --}}
<div id="modal-nota" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">

    <div class="bg-white rounded-xl shadow-lg max-w-lg w-full p-4 relative">

        <button onclick="tutupNota()" 
        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <h3 class="font-semibold mb-3">Bukti Nota</h3>

        <img id="gambar-nota" src="" class="w-full rounded-lg">

    </div>

</div>


<script>
    const saldo = {{ $saldo }};
    const inputNominal = document.getElementById('nominalPengeluaran');
    const errorSaldo = document.getElementById('errorSaldo');

    inputNominal.addEventListener('input', function(){

        const nominal = parseInt(this.value);

        if(nominal > saldo){
            errorSaldo.innerText = "Saldo tidak cukup. Saldo tersedia Rp " + saldo.toLocaleString('id-ID');
            errorSaldo.classList.remove('hidden');
        }else{
            errorSaldo.classList.add('hidden');
        }

    });

    const btnSimpan = document.getElementById('btnSimpanPengeluaran');

    inputNominal.addEventListener('input', function(){

        const nominal = parseInt(this.value);

        if(nominal > saldo){
            errorSaldo.innerText = "Saldo tidak cukup. Saldo tersedia Rp " + saldo.toLocaleString('id-ID');
            errorSaldo.classList.remove('hidden');
            btnSimpan.disabled = true;
            btnSimpan.classList.add('opacity-50');
        }else{
            errorSaldo.classList.add('hidden');
            btnSimpan.disabled = false;
            btnSimpan.classList.remove('opacity-50');
        }

    });
    function lihatNota(src){
        document.getElementById('gambar-nota').src = src;
        document.getElementById('modal-nota').classList.remove('hidden');
    }

    function tutupNota(){
        document.getElementById('modal-nota').classList.add('hidden');
    }
    let bulan = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
    ];


    let dataPengeluaran = Array(12).fill(0);
    let dataPemasukan = Array(12).fill(0);


    @foreach($pengeluaranPerBulan as $p)

    dataPengeluaran[{{$p->bulan-1}}] =
    {{$p->total}};

    @endforeach

    @foreach($pemasukanPerBulan as $p)
    dataPemasukan[{{$p->bulan-1}}]
    ={{$p->total}};
    @endforeach

    new Chart(
    document.getElementById('barPengeluaran'),
    {

    type:'bar',

    data:{

    labels:bulan,

    datasets:[{
        label:'Pengeluaran',
        data:dataPengeluaran
    },
    {
        label:'Pemasukam',
        data:dataPemasukan
    }]

    },

    options:{

    responsive:true,

    scales:{

    y:{
    beginAtZero:true
    }

    }

    }

    });



    @if($barangPerBulan->count()==0)
    new Chart(
    document.getElementById('donutBarang'),
    {

    type:'doughnut',

    data:{

    labels:['Tidak ada pengeluaran'],

    datasets:[{

    data:[1],

    backgroundColor:[
    '#9CA3AF'
    ]

    }]

    }

    });


    @else


    new Chart(
    document.getElementById('donutBarang'),
    {

    type:'doughnut',

    data:{

    labels:[

    @foreach($barangPerBulan as $b)

    "{{ $b->itemLogistik->nama_item ?? 'Tidak Diketahui' }}",

    @endforeach

    ],


    datasets:[{

    data:[

    @foreach($barangPerBulan as $b)

    {{$b->total}},

    @endforeach

    ]

    }]

    }

    });


    @endif
</script>
@endsection
