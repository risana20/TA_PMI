<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Ajuan Reimbursemen</title>
    <style>
        @page {
            margin-bottom: 20mm;
        }
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data Ajuan Reimbursemen</h1>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pengaju</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Nominal</th>
                <th>Total</th>
                <th>Status</th>
                <th>Validator</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reimbursements as $reimbursement)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($reimbursement->tgl_pengajuan)->format('d M Y')  }}</td>
                    <td>{{  optional($reimbursement->user)->name }}</td>
                    <td>@foreach($reimbursement->detailReimbursements as $detail)
                        {{ optional($detail->itemLogistik)->nama_item }}<br>
                        @endforeach
                    </td>
                    <td>@foreach($reimbursement->detailReimbursements as $detail)
                            {{ $detail->jumlah }}<br>
                        @endforeach
                    </td>
                    <td>@foreach($reimbursement->detailReimbursements as $detail)
                            Rp {{ number_format($detail->nominal,0,',','.') }}<br>
                        @endforeach
                    </td>
                    <td> Rp {{ number_format($reimbursement->total,0,',','.') }}</td>
                    <td>{{ $reimbursement->status }}</td>
                    <td>{{ optional($reimbursement->validator)->name ?? '-'}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">
        Tanggal Cetak: {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB<br>
        Dicetak Oleh: {{ auth()->user()->name }}<br>
        Role: {{ auth()->user()->hasRole('superadmin') ? 'Superadmin' : 'Admin' }}
    </div>
</body>
</html>
