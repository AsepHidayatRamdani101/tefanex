<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                '2024001',
                'John Doe',
                'X IPA 1',
                'john@example.com',
                '081234567890',
                'Jl. Merdeka No. 123',
                'john.doe@school.com',
                'password123',
            ],
            [
                '2024002',
                'Jane Smith',
                'X IPA 2',
                'jane@example.com',
                '081234567891',
                'Jl. Sudirman No. 45',
                'jane.smith@school.com',
                'password456',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Nama',
            'Kelas',
            'Email Siswa',
            'No. Telepon',
            'Alamat',
            'Username Login',
            'Password',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(22);
        $sheet->getColumnDimension('H')->setWidth(18);

        // Header styling
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Add notes/instructions
        $sheet->setCellValue('A10', 'Catatan:');
        $sheet->setCellValue('A11', '• NIM harus unik');
        $sheet->setCellValue('A12', '• Kelas harus sesuai dengan nama atau kode kelas yang ada');
        $sheet->setCellValue('A13', '• Username Login dan Password bersifat opsional');
        $sheet->setCellValue('A14', '• Jika Username Login kosong, siswa tidak akan memiliki akun login');

        return [];
    }
}
