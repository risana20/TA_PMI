<?php

namespace App\Exports;

use App\Models\Donasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DonasiExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $tab;
    protected $search;
    protected $statusDonasi;
    protected $statusLogistik;

    public function __construct($tab = 'Uang', $search = null, $statusDonasi = null, $statusLogistik = null)
    {
        $this->tab            = $tab;
        $this->search         = $search;
        $this->statusDonasi   = $statusDonasi;
        $this->statusLogistik = $statusLogistik;
    }

    public function collection()
    {
        $query = Donasi::with(['user', 'donasiUang', 'donasiMakanan', 'pemasukanLogistik.stokLogistik.itemLogistik'])
                    ->where('jenis', $this->tab)
                    ->latest();

        if ($this->search) {
            $query->where('nama_donatur', 'like', "%{$this->search}%");
        }

        if ($this->statusDonasi) {
            $query->where('status', $this->statusDonasi);
        }

        if ($this->statusLogistik && $this->tab !== 'Uang') {
            if ($this->tab === 'Barang') {
                if ($this->statusLogistik === 'Sudah') {
                    $query->whereHas('pemasukanLogistik', function ($q) {
                        $q->whereNotNull('stok_logistik_id');
                    });
                } elseif ($this->statusLogistik === 'Belum') {
                    $query->where(function ($q) {
                        $q->whereHas('pemasukanLogistik', function ($sub) {
                            $sub->whereNull('stok_logistik_id');
                        })->orWhereDoesntHave('pemasukanLogistik');
                    });
                }
            } elseif ($this->tab === 'Makanan') {
                if ($this->statusLogistik === 'Sudah') {
                    $query->whereHas('pemasukanLogistik');
                } elseif ($this->statusLogistik === 'Belum') {
                    $query->whereDoesntHave('pemasukanLogistik');
                }
            }
        }

        $data = $query->get();

        return $data->map(function ($donasi, $index) {
            $no = $index + 1;
            $donatur = $donasi->nama_donatur;
            $tanggal = $donasi->created_at->format('d-m-Y');
            if ($this->tab !== 'Uang' && $donasi->jam_penyerahan) {
                $tanggal .= ' ' . \Carbon\Carbon::parse($donasi->jam_penyerahan)->format('H:i');
            }
            $status = $donasi->status;

            if ($this->tab === 'Uang') {
                return [
                    'no' => $no,
                    'donatur' => $donatur,
                    'nominal' => $donasi->donasiUang->nominal ?? 0,
                    'bank_tujuan' => $donasi->donasiUang->bank_tujuan ?? '-',
                    'tanggal' => $tanggal,
                    'status' => $status,
                ];
            } elseif ($this->tab === 'Barang') {
                $nama_barang = $donasi->pemasukanLogistik->nama_barang ?? $donasi->pemasukanLogistik->stokLogistik->itemLogistik->nama_item ?? '-';
                $jumlah = $donasi->pemasukanLogistik->jumlah ?? 0;
                $satuan = $donasi->pemasukanLogistik->satuan ?? '';
                
                $hasStockMap = ($donasi->pemasukanLogistik?->stok_logistik_id !== null);
                $statusLogistik = $hasStockMap ? 'Sudah Ditambahkan' : 'Belum Ditambahkan';

                return [
                    'no' => $no,
                    'donatur' => $donatur,
                    'nama_barang' => $nama_barang . ' (' . $jumlah . ' ' . $satuan . ')',
                    'kondisi' => $donasi->pemasukanLogistik->kondisi ?? '-',
                    'phone' => $donasi->user->phone ?? '-',
                    'address' => $donasi->user->address ?? '-',
                    'tanggal' => $tanggal,
                    'status' => $status,
                    'status_logistik' => $statusLogistik,
                ];
            } else {
                // Makanan
                $nama_makanan = $donasi->donasiMakanan->nama_makanan ?? '-';
                $jumlah = $donasi->donasiMakanan->jumlah_makanan ?? '-';
                
                $hasStockMap = ($donasi->pemasukanLogistik !== null);
                $statusLogistik = $hasStockMap ? 'Sudah Ditambahkan' : 'Belum Ditambahkan';

                return [
                    'no' => $no,
                    'donatur' => $donatur,
                    'nama_makanan' => $nama_makanan . ' (' . $jumlah . ')',
                    'jenis' => $donasi->donasiMakanan->jenis_makanan ?? '-',
                    'phone' => $donasi->user->phone ?? '-',
                    'address' => $donasi->user->address ?? '-',
                    'tanggal' => $tanggal,
                    'status' => $status,
                    'status_logistik' => $statusLogistik,
                ];
            }
        });
    }

    public function headings(): array
    {
        $user = auth()->user();
        $role = $user->hasRole('superadmin') ? 'Superadmin' : 'Admin';
        $tanggalCetak = \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB';

        $info = [
            ['Informasi Cetak'],
            ['Tanggal Cetak', $tanggalCetak],
            ['Dicetak Oleh', $user->name],
            ['Role', $role],
            [], // spacer row
        ];

        if ($this->tab === 'Uang') {
            $headers = [
                'No',
                'Donatur',
                'Nominal (Rp)',
                'Bank Tujuan',
                'Tanggal',
                'Status'
            ];
        } elseif ($this->tab === 'Barang') {
            $headers = [
                'No',
                'Donatur',
                'Nama Barang',
                'Kondisi',
                'No HP',
                'Alamat',
                'Tanggal & Jam',
                'Status',
                'Status Logistik'
            ];
        } else {
            $headers = [
                'No',
                'Donatur',
                'Nama Makanan',
                'Jenis',
                'No HP',
                'Alamat',
                'Tanggal & Jam',
                'Status',
                'Status Logistik'
            ];
        }

        $info[] = $headers;
        return $info;
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

        // Format column C (Nominal) as currency if tab is Uang
        if ($this->tab === 'Uang' && $highestRow > 6) {
            $sheet->getStyle('C7:C' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');
        }

        return [];
    }
}
