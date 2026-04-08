<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KelasTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                'X IPA 1',
                'X-IPA-1',
                'Kelas X IPA 1 - Program Ilmu Pengetahuan Alam',
                '30',
            ],
            [
                'X IPA 2',
                'X-IPA-2',
                'Kelas X IPA 2 - Program Ilmu Pengetahuan Alam',
                '32',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Kelas',
            'Kode Kelas',
            'Deskripsi',
            'Kapasitas',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(12);

        // Header styling
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Add notes/instructions
        $sheet->setCellValue('A7', 'Catatan:');
        $sheet->setCellValue('A8', '• Nama Kelas harus unik');
        $sheet->setCellValue('A9', '• Kode Kelas harus unik dan mudah diingat (contoh: X-IPA-1)');
        $sheet->setCellValue('A10', '• Deskripsi bersifat opsional');
        $sheet->setCellValue('A11', '• Kapasitas adalah jumlah maksimal siswa dalam kelas (1-200)');

        return [];
    }
}
