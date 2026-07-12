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

        return $data->map(function ($kunjungan) {
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
