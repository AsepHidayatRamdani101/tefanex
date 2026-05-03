<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RekapAbsensiExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $rekapRows;

    public function __construct($rekapRows)
    {
        $this->rekapRows = $rekapRows ?? [];
    }

    public function array(): array
    {
        $data = [];
        
        if (!is_array($this->rekapRows) || empty($this->rekapRows)) {
            return $data;
        }

        foreach ($this->rekapRows as $index => $row) {
            $data[] = [
                $index + 1,
                $row['nama'] ?? '',
                $row['kelas'] ?? '-',
                $row['hadir'] ?? 0,
                $row['sakit'] ?? 0,
                $row['izin'] ?? 0,
                $row['alpha'] ?? 0,
                $row['total'] ?? 0,
            ];
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Kelas',
            'Hadir',
            'Sakit',
            'Izin',
            'Alpa',
            'Total',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 25,
            'C' => 15,
            'D' => 10,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '366092'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Data styling
        if (count($this->rekapRows) > 0) {
            $lastRow = count($this->rekapRows) + 1;
            $sheet->getStyle('A2:H' . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
        }

        return $sheet;
    }
}
