<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Warga Binaan - {{ $tab }}</title>
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
        <h1>Laporan Data Warga Binaan</h1>
        <p>Kategori: {{ $tab }}</p>
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
                <th>NIK</th>
                <th>Nama</th>
                <th>Tempat, Tgl Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Status</th>
                <th>Tgl Masuk</th>
                <th>Penanggung Jawab</th>
            </tr>
        </thead>
        <tbody>
            @forelse($wargaBinaans as $wargaBinaan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $wargaBinaan->nik }}</td>
                    <td>{{ $wargaBinaan->nama }}</td>
                    <td>{{ $wargaBinaan->tempat_lahir }}, {{ $wargaBinaan->tgl_lahir?->format('d M Y') }}</td>
                    <td>{{ $wargaBinaan->jenis_kelamin_text }}</td>
                    <td>{{ $wargaBinaan->status }}</td>
                    <td>{{ $wargaBinaan->tgl_masuk?->format('d M Y') }}</td>
                    <td>{{ $wargaBinaan->penanggung_jawab }}</td>
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
