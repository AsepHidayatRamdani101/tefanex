<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GradeExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected Collection $results;

    public function __construct(Collection $results)
    {
        $this->results = $results;
    }

    public function collection()
    {
        return $this->results;
    }

    public function headings(): array
    {
        return [
            'No',
            'Siswa',
            'Kelas',
            'Project',
            'Materi',
            'Pretest',
            'Posttest',
            'Nilai Tugas',
            'Rata-rata Nilai',
            'Catatan Sikap',
        ];
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $row->user?->name ?? '-',
            $row->user?->siswa?->kelas?->name ?? '-',
            $row->project_name ?? '-',
            $row->material_title ?? '-',
            $this->formatValue($row->pretest_score),
            $this->formatValue($row->posttest_score),
            $this->formatValue($row->task_score_value),
            $this->formatValue($row->average_score),
            $row->attitude_note ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:J' . ($this->results->count() + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return [];
    }

    private function formatValue($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.') . '%';
    }
}