<?php

namespace App\Exports;

use App\Models\Reimbursement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ACCReimbursementExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{ 

    public function collection()
    {
        
        $data = Reimbursement::with([
            'user',
            'validator',
            'detailReimbursements.itemLogistik'
        ])
        ->latest()
        ->get();

        return $data -> map(function ($reimbursement) {

            $barang = [];
            $jumlah = [];
            $nominal = [];

            foreach ($reimbursement->detailReimbursements as $detail) {
                $barang[] = $detail->itemLogistik->nama_item ?? '-';
                $jumlah[] = $detail->jumlah;
                $nominal[] = 'Rp ' . number_format($detail->nominal, 0, ',', '.');
            }

            return [
                'Tanggal Pengajuan' => $reimbursement->tgl_pengajuan,
                'Pengaju'           => optional($reimbursement->user)->name,
                'Barang'            => implode(", ", $barang),
                'Jumlah'            => implode(", ", $jumlah),
                'Nominal'           => implode(", ", $nominal),
                'Total'             => 'Rp ' . number_format($reimbursement->total, 0, ',', '.'),
                'Status'            => $reimbursement->status,
                'Tanggal Validasi'  => $reimbursement->tgl_validasi ?? '-',
                'Validator'         => optional($reimbursement->validator)->name ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        $user = auth()->user();

        return [
            ['Informasi Cetak'],
            ['Tanggal Cetak', now()->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB'],
            ['Dicetak Oleh', $user->name],
            ['Role', $user->hasRole('superadmin') ? 'Superadmin' : 'Admin'],
            [],
            [
                'Tanggal Pengajuan',
                'Pengaju',
                'Barang',
                'Jumlah',
                'Nominal',
                'Total',
                'Status',
                'Tanggal Validasi',
                'Validator'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->mergeCells('A1:B1');

        $sheet->getStyle('A6:' . $highestColumn . '6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFE4000F',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A6:' . $highestColumn . $highestRow)
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

        $sheet->getStyle('A1:A4')->getFont()->setBold(true);

        return [];
    }
}