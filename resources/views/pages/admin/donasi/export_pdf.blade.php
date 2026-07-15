<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Donasi - {{ $tab }}</title>
    <style>
        @page {
            margin-bottom: 20mm;
        }
        body {
            font-family: 'sans-serif';
            font-size: 10px;
        }
        .footer {
            position: fixed;
            bottom: -15mm;
            left: 0;
            right: 0;
            height: 40px;
            font-size: 9px;
            color: #333333;
            text-align: left;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #E4000F;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #555555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        thead {
            background-color: #E4000F;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="footer">
        Tanggal Cetak: {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB<br>
        Dicetak Oleh: {{ auth()->user()->name }}<br>
        Role: {{ auth()->user()->hasRole('superadmin') ? 'Superadmin' : 'Admin' }}
    </div>
    <div class="header">
        <h1>Laporan Data Donasi {{ $tab }}</h1>
        <p>Griya PMI Surakarta</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th>Donatur</th>
                @if($tab === 'Uang')
                    <th>Nominal</th>
                    <th>Bank Tujuan</th>
                @elseif($tab === 'Barang')
                    <th>Nama Barang</th>
                    <th>Kondisi</th>
                    <th>No HP</th>
                    <th>Alamat</th>
                @else
                    <th>Nama Makanan</th>
                    <th>Jenis</th>
                    <th>No HP</th>
                    <th>Alamat</th>
                @endif
                <th>Tanggal @if($tab !== 'Uang') & Jam @endif</th>
                <th>Status</th>
                @if($tab !== 'Uang')
                    <th>Status Logistik</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($donasis as $donasi)
                @php
                    $tanggal = $donasi->created_at->format('d M Y');
                    if ($tab !== 'Uang' && $donasi->jam_penyerahan) {
                        $tanggal .= ' ' . \Carbon\Carbon::parse($donasi->jam_penyerahan)->format('H:i');
                    }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $donasi->nama_donatur }}</td>
                    @if($tab === 'Uang')
                        <td>Rp {{ number_format($donasi->donasiUang->nominal ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $donasi->donasiUang->bank_tujuan ?? '-' }}</td>
                    @elseif($tab === 'Barang')
                        <td>
                            {{ $donasi->pemasukanLogistik->nama_barang ?? $donasi->pemasukanLogistik->stokLogistik->itemLogistik->nama_item ?? '-' }} 
                            ({{ $donasi->pemasukanLogistik->jumlah ?? 0 }} {{ $donasi->pemasukanLogistik->satuan ?? '' }})
                        </td>
                        <td>{{ $donasi->pemasukanLogistik->kondisi ?? '-' }}</td>
                        <td>{{ $donasi->user->phone ?? '-' }}</td>
                        <td>{{ $donasi->user->address ?? '-' }}</td>
                    @else
                        <td>{{ $donasi->donasiMakanan->nama_makanan ?? '-' }} ({{ $donasi->donasiMakanan->jumlah_makanan ?? '-' }})</td>
                        <td>{{ $donasi->donasiMakanan->jenis_makanan ?? '-' }}</td>
                        <td>{{ $donasi->user->phone ?? '-' }}</td>
                        <td>{{ $donasi->user->address ?? '-' }}</td>
                    @endif
                    <td>{{ $tanggal }}</td>
                    <td>{{ $donasi->status }}</td>
                    @if($tab !== 'Uang')
                        <td>
                            @php
                                $hasStockMap = ($donasi->jenis === 'Barang' && $donasi->pemasukanLogistik?->stok_logistik_id !== null) || 
                                               ($donasi->jenis === 'Makanan' && $donasi->pemasukanLogistik !== null);
                            @endphp
                            {{ $hasStockMap ? 'Sudah Ditambahkan' : 'Belum Ditambahkan' }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $tab === 'Uang' ? 6 : 9 }}" style="text-align: center;">Tidak ada data donasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
