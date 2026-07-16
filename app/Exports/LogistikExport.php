<?php

namespace App\Exports;

use App\Models\StokLogistik;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LogistikExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $search;
    protected $kategori;
    protected $status;

    public function __construct($search = null, $kategori = null, $status = null)
    {
        $this->search   = $search;
        $this->kategori = $kategori;
        $this->status   = $status;
    }

    public function collection()
    {
        $query = StokLogistik::with(['itemLogistik.jenisLogistik']);

        if ($this->search) {
            $query->whereHas('itemLogistik', function($q) {
                $q->where('nama_item', 'like', "%{$this->search}%");
            });
        }

        if ($this->kategori) {
            $query->whereHas('itemLogistik.jenisLogistik', function($q) {
                $q->where('nama_jenis_logistik', $this->kategori);
            });
        }

        if ($this->status) {
            if ($this->status === 'Aman') {
                $query->whereRaw('jumlah_saat_ini > jumlah_minimum');
            } elseif ($this->status === 'Mendesak') {
                $query->whereRaw('jumlah_saat_ini <= jumlah_minimum AND jumlah_saat_ini >= (jumlah_minimum * 0.8)');
            } elseif ($this->status === 'Sangat Mendesak') {
                $query->whereRaw('jumlah_saat_ini < (jumlah_minimum * 0.8)');
            }
        }

        $data = $query->get();

        $mapped = $data->map(function ($item, $index) {
            return [
                'no' => $index + 1,
                'nama' => $item->itemLogistik->nama_item ?? '-',
                'kategori' => $item->itemLogistik->jenisLogistik->nama_jenis_logistik ?? '-',
                'stok_minimum' => $item->jumlah_minimum,
                'stok_saat_ini' => $item->jumlah_saat_ini . ' ' . ($item->itemLogistik->satuan ?? ''),
                'status' => strtoupper($item->status),
            ];
        });

        $user = auth()->user();
        $role = $user ? ($user->hasRole('superadmin') ? 'Superadmin' : 'Admin') : 'user';
        $tanggalCetak = \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB';

        $mapped->push([]);
        $mapped->push(['Tanggal Cetak', $tanggalCetak]);
        $mapped->push(['Dicetak Oleh', $user ? $user->name : '-']);
        $mapped->push(['Role', $role]);

        return $mapped;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Barang',
            'Kategori',
            'Stok Minimum',
            'Stok Saat Ini',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        $tableEndRow = max(1, $highestRow - 4);
        $range = 'A1:' . $highestColumn . $tableEndRow;

        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFE4000F', // Brand Red PMI
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style the print info at the bottom-left (last 3 rows, column A & B)
        if ($highestRow > 3) {
            $sheet->getStyle('A' . ($highestRow - 2) . ':A' . $highestRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                ]
            ]);
        }

        return [];
    }
}
