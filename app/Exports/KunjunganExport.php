<?php

namespace App\Exports;

use App\Models\Kunjungan;
use App\Models\WargaBinaan;
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

class KunjunganExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithCustomValueBinder
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

        // Kolom NO HP
        if (in_array($column, ['D'])) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        // Return default behavior
        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        $query = Kunjungan::with('wargaBinaan');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_pengunjung', 'like', "%{$this->search}%")
                  ->orWhere('instansi', 'like', "%{$this->search}%")
                  ->orWhere('tujuan', 'like', "%{$this->search}%");
            });
        }

        $data = $query->orderBy('tgl_kunjungan', 'asc')->get();

        $mapped = $data->map(function ($kunjungan) {
            return [
                'nama pengunjung' => $kunjungan->nama_pengunjung,
                'no hp'           => $kunjungan->no_hp,
                'tujuan'          => $kunjungan->tujuan,
                'instansi'        => $kunjungan->instansi,
                'tanggal kunjungan'=> $kunjungan->tgl_kunjungan,
                'jam'             => $kunjungan->jam,
                'status'          => $kunjungan->status,
                
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
                'nama_pengunjung', 
                'no_hp',
                'tujuan',
                'instansi', 
                'tgl_kunjungan',
                'jam',
                'status', 
             
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
                    'argb' => 'FFE4000F',
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

        if ($highestRow > 3) {
            $sheet->getStyle('A' . ($highestRow - 2) . ':A' . $highestRow)
                ->getFont()->setBold(true);
        }

        return [];
    }
}
