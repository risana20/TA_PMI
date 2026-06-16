<!DOCTYPE html>
<html>
<head>
    <title>Monitoring Kesehatan Warga Binaan - {{ $tab }}</title>
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
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        thead {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Monitoring Kesehatan Warga Binaan</h1>
        <p>Kategori: {{ $tab === 'ODGJ' ? 'Griya PMI Peduli (ODGJ)' : 'Griya PMI Bahagia (Lansia)' }}</p>
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
