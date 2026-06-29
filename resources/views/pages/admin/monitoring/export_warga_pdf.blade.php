<!DOCTYPE html>
<html>
<head>
    <title>Laporan Monitoring Kesehatan Warga Binaan - {{ $tab }}</title>
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
            font-size: 16px;
            color: #E4000F;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
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
        <h1>Laporan Monitoring Kesehatan Warga Binaan</h1>
        <p>Kategori: {{ $tab === 'ODGJ' ? 'Griya PMI Peduli (ODGJ)' : ($tab === 'Lansia' ? 'Griya PMI Bahagia (Lansia)' : $tab) }}</p>
        <div style="margin-top: 10px; font-size: 11px; color: #333; text-align: center;">
            Tanggal Cetak: {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB<br>
            Dicetak Oleh: {{ auth()->user()->name }}<br>
            Role: {{ auth()->user()->hasRole('superadmin') ? 'Superadmin' : 'Admin' }}
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 20%">NIK</th>
                <th style="width: 35%">Nama</th>
                <th style="width: 15%">Jenis Kelamin</th>
                <th style="width: 15%">Umur</th>
                <th style="width: 10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($wargaBinaans as $warga)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $warga->nik }}</td>
                    <td>{{ $warga->nama }}</td>
                    <td>{{ $warga->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>{{ $warga->umur }} tahun</td>
                    <td>{{ $warga->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #888;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
