<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Seluruh Artikel </title>
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
        <h1>Laporan Data Artikel</h1>
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
                <th>Judul</th>
                <th>Kategori</th>
                <th>Konten</th>
                <th>Status</th>
                <th>Tanggal terbit</th>
                <th>Pembuat</th>
             
            </tr>
        </thead>
        <tbody>
            @forelse($artikels as $artikel)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $artikel->judul }}</td>
                    <td>{{ $artikel->kategori }}</td>
                    <td>{{ strip_tags($artikel->konten) }}</td>
                    <td>{{ $artikel->status }}</td>
                    <td>{{ $artikel->tgl_terbit ? \Carbon\Carbon::parse($artikel->tgl_terbit)->format('d M Y'): '-' }} </td>
                    <td>{{ optional($artikel->Penulis)->name }}</td>
                    
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
