<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pemeriksaan Kesehatan - {{ $wargaBinaan->nama }}</title>
    <style>
        body {
            font-family: 'sans-serif';
            font-size: 8px;
        }
        @page {
            size: A4 landscape;
            margin: 15mm;
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
            padding: 5px;
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
        <h1>Laporan Pemeriksaan Kesehatan Warga Binaan</h1>
        <p>Nama: {{ $wargaBinaan->nama }} ({{ $wargaBinaan->nik }}) | Kategori: {{ $wargaBinaan->kategori }}</p>
        <div style="margin-top: 10px; font-size: 11px; color: #333; text-align: center;">
            Tanggal Cetak: {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB<br>
            Dicetak Oleh: {{ auth()->user()->name }}<br>
            Role: {{ auth()->user()->hasRole('superadmin') ? 'Superadmin' : 'Admin' }}
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 3%">No</th>
                <th style="width: 8%">Tanggal</th>
                <th style="width: 8%">Frek. Napas</th>
                <th style="width: 8%">Tensi (TD)</th>
                <th style="width: 6%">Suhu</th>
                <th style="width: 7%">Nadi</th>
                <th style="width: 6%">SPO₂</th>
                <th style="width: 10%">BB / TB</th>
                <th style="width: 20%">Riwayat Penyakit</th>
                <th style="width: 12%">Keluhan</th>
                <th style="width: 12%">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $r)
                @php
                    $riwayatPenyakit = '-';
                    if ($r->riwayatPenyakits && $r->riwayatPenyakits->isNotEmpty()) {
                        $riwayatPenyakit = $r->riwayatPenyakits->map(function($p) {
                            return $p->nama_penyakit . ' (' . ($p->status ?? 'Sembuh') . ')';
                        })->join(', ');
                    } elseif (!empty($r->riwayat_penyakit)) {
                        $decoded = json_decode($r->riwayat_penyakit, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $riwayatPenyakit = collect($decoded)->map(function($p) {
                                return ($p['nama_penyakit'] ?? '') . ' (' . ($p['status'] ?? 'Sembuh') . ')';
                            })->join(', ');
                        } else {
                            $riwayatPenyakit = $r->riwayat_penyakit;
                        }
                    }
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $r->tanggal?->format('d M Y') ?? '-' }}</td>
                    <td>{{ $r->frek_napas ? $r->frek_napas . '/mnt' : '-' }}</td>
                    <td>{{ $r->tekanan_darah ?? '-' }}</td>
                    <td>{{ $r->suhu_tubuh ? $r->suhu_tubuh . '°C' : '-' }}</td>
                    <td>{{ $r->nadi ? $r->nadi . '/mnt' : '-' }}</td>
                    <td>{{ $r->spo2 ? $r->spo2 . '%' : '-' }}</td>
                    <td>
                        {{ $r->berat_badan ? $r->berat_badan . ' kg' : '-' }}
                        @if($r->tinggi_badan) / {{ $r->tinggi_badan }} cm @endif
                    </td>
                    <td>{{ $riwayatPenyakit }}</td>
                    <td>{{ $r->keluhan ?? '-' }}</td>
                    <td>{{ $r->tindakan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; color: #888;">Tidak ada data pemeriksaan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
