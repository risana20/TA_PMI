<?php

namespace App\Exports;

use App\Models\MonitoringKesehatan;
use App\Models\WargaBinaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PemeriksaanKesehatanExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $wargaBinaan;

    public function __construct(WargaBinaan $wargaBinaan)
    {
        $this->wargaBinaan = $wargaBinaan;
    }

    public function collection()
    {
        $data = $this->wargaBinaan->monitoringKesehatans()
            ->with('riwayatPenyakits')
            ->latest()
            ->get();

        return $data->map(function ($r, $index) {
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

            return [
                'no' => $index + 1,
                'tanggal' => $r->tanggal?->format('d-m-Y') ?? '-',
                'frek_napas' => $r->frek_napas ? $r->frek_napas . '/mnt' : '-',
                'tekanan_darah' => $r->tekanan_darah ?? '-',
                'suhu_tubuh' => $r->suhu_tubuh ? $r->suhu_tubuh . '°C' : '-',
                'nadi' => $r->nadi ? $r->nadi . '/mnt' : '-',
                'spo2' => $r->spo2 ? $r->spo2 . '%' : '-',
                'bbtb' => ($r->berat_badan ? $r->berat_badan . ' kg' : '-') . ($r->tinggi_badan ? ' / ' . $r->tinggi_badan . ' cm' : ''),
                'riwayat_penyakit' => $riwayatPenyakit,
                'keluhan' => $r->keluhan ?? '-',
                'tindakan' => $r->tindakan ?? '-',
                'catatan' => $r->catatan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Frek. Napas',
            'Tekanan Darah',
            'Suhu',
            'Nadi',
            'SPO₂',
            'BB / TB',
            'Riwayat Penyakit',
            'Keluhan',
            'Tindakan',
            'Catatan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $range = 'A1:' . $highestColumn . $highestRow;

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

        return [];
    }
}
