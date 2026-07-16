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

        $mapped = $data->map(function ($artikel) {
            return [
                'Judul'    => $artikel->judul,
                'Kategori' => $artikel->kategori,
                'Konten' => html_entity_decode(strip_tags($artikel->konten)),
                'Status'   =>$artikel->status,
                'Tanggal terbit'=>$artikel->tgl_terbit,
                'Pembuat' => optional($artikel->penulis)->name,
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
