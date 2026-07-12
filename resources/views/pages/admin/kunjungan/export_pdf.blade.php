<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Seluruh Kunjungan </title>
    <style>
        body {
            font-family: 'sans-serif';
            font-size: 10px;
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
    <div class="header">
        <h1>Laporan Data Kunjungan</h1>
        <div style="margin-top: 10px; font-size: 11px; color: #333; text-align: center;">
            Tanggal Cetak: {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB<br>
            Dicetak Oleh: {{ auth()->user()->name }}<br>
            Role: {{ auth()->user()->hasRole('superadmin') ? 'Superadmin' : 'Admin' }}
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pengunjung</th>
                <th>No.HP</th>
                <th>Tujuan</th>
                <th>Instansi</th>
                <th>Tanggal Kunjungan</th>
                <th>Jam</th>
                <th>Status</th>
             
            </tr>
        </thead>
        <tbody>
            @forelse($kunjungans as $kunjungan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $kunjungan->nama_pengunjung }}</td>
                    <td>{{ $kunjungan->no_hp }}</td>
                    <td>{{ $kunjungan->tujuan }}</td>
                    <td>{{ $kunjungan->instansi }}</td>
                    <td>{{ $kunjungan->tgl_kunjungan }}, {{ $kunjungan->tgl_kunjungan?->format('d M Y') }}</td>
                    <td>{{ $kunjungan->jam }}</td>
                    <td>{{ $kunjungan->status }}</td>
                    
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
