<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Logistik & Inventaris</title>
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
        <h1>Laporan Data Logistik & Inventaris</h1>
        <p>Griya PMI Surakarta</p>
        @if($kategori || $status)
        <p style="font-size: 10px;">
            @if($kategori) Kategori: {{ $kategori }} @endif
            @if($kategori && $status) | @endif
            @if($status) Status: {{ $status }} @endif
        </p>
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok Minimum</th>
                <th>Stok Saat Ini</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logistiks as $item)
                @php $kat = $item->itemLogistik->jenisLogistik->nama_jenis_logistik ?? '-'; @endphp
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $item->itemLogistik->nama_item }}</td>
                    <td>{{ $kat }}</td>
                    <td>{{ $item->jumlah_minimum }}</td>
                    <td>{{ $item->jumlah_saat_ini }} {{ $item->itemLogistik->satuan }}</td>
                    <td>{{ strtoupper($item->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data logistik.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
