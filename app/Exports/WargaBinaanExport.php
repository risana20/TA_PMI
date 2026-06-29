<?php

namespace App\Exports;

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

class WargaBinaanExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithCustomValueBinder
{
    protected $tab;
    protected $status;
    protected $search;

    public function __construct($tab = 'ODGJ', $status = null, $search = null)
    {
        $this->tab = $tab;
        $this->status = $status;
        $this->search = $search;
    }

    public function bindValue(Cell $cell, $value)
    {
        $column = $cell->getColumn();

        // Kolom A (NIK) dan Kolom I (No. BPJS) diset secara eksplisit sebagai teks (string)
        if (in_array($column, ['A', 'I'])) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        // Return default behavior
        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        $query = WargaBinaan::where('kategori', $this->tab);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nik', 'like', "%{$this->search}%");
            });
        }

        $data = $query->latest()->get();

        return $data->map(function ($warga) {
            return [
                'nik' => $warga->nik,
                'nama' => $warga->nama,
                'tempat_lahir' => $warga->tempat_lahir,
                'tgl_lahir' => $warga->tgl_lahir?->format('d-m-Y'),
                'jenis_kelamin' => $warga->jenis_kelamin_text,
                'kategori' => $warga->kategori,
                'status' => $warga->status,
                'tgl_masuk' => $warga->tgl_masuk?->format('d-m-Y'),
                'no_bpjs' => $warga->no_bpjs,
                'penanggung_jawab' => $warga->penanggung_jawab,
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
                'NIK',
                'Nama',
                'Tempat Lahir',
                'Tanggal Lahir',
                'Jenis Kelamin',
                'Kategori',
                'Status',
                'Tanggal Masuk',
                'No. BPJS',
                'Penanggung Jawab'
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
