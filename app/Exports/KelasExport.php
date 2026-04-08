<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KelasExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $kelas;

    public function __construct($kelas = null)
    {
        $this->kelas = $kelas;
    }

    public function collection()
    {
        return $this->kelas ?? Kelas::all();
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

    public function map($kelas): array
    {
        return [
            $kelas->name,
            $kelas->code,
            $kelas->description ?? '-',
            $kelas->capacity,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(12);

        // Header styling
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        return [];
    }
}
