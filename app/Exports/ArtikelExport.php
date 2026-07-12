<?php

namespace App\Exports;

use App\Models\Artikel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ArtikelExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithCustomValueBinder
{
   
    protected $status;
    protected $search;

    public function __construct ($status = null, $search = null)
    {
        
        $this->status = $status;
        $this->search = $search;
    }

    public function bindValue(Cell $cell, $value)
    {
        $column = $cell->getColumn();

       
        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        $query = Artikel::with('penulis');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->where(function ($q) {
                 $q->where('judul', 'like', "%{$this->search}%")
                ->orWhere('kategori', 'like', "%{$this->search}%")
                ->orWhere('status', 'like', "%{$this->search}%")
                ->orWhere('tgl_terbit', 'like', "%{$this->search}%")
                ->orWhere('user_id', 'like', "%{$this->search}%");
            });
        }

        $data = $query->get();

        return $data->map(function ($artikel) {
            return [
                'Judul'    => $artikel->judul,
                'Kategori' => $artikel->kategori,
                'Konten' => html_entity_decode(strip_tags($artikel->konten)),
                'Status'   =>$artikel->status,
                'Tanggal terbit'=>$artikel->tgl_terbit,
                'Pembuat' => optional($artikel->penulis)->name,
            ];
        });
    }

    public function headings(): array
    {
        $user = auth()->user();
        $role = $user->hasRole('superadmin') ? 'Superadmin' : 'Admin';
        $tanggalCetak = \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB';

        return [
            ['Informasi Cetak'],
            ['Tanggal Cetak', $tanggalCetak],
            ['Dicetak Oleh', $user->name],
            ['Role', $role],
            [], // spacer row
            [
                'Judul', 
                'Kategori',
                'Konten',
                'Status', 
                'Tanggal terbit',
                'Pembuat',  
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        $sheet->mergeCells('A1:B1');
        
        $range = 'A6:' . $highestColumn . $highestRow;

        $sheet->getStyle('A6:' . $highestColumn . '6')->applyFromArray([
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

        // Style the print info
        $sheet->getStyle('A1:A4')->applyFromArray([
            'font' => [
                'bold' => true,
            ]
        ]);

        return [];
    }
}
