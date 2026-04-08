<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $siswas;

    public function __construct($siswas = null)
    {
        $this->siswas = $siswas;
    }

    public function collection()
    {
        if ($this->siswas) {
            return $this->siswas;
        }
        return Siswa::with(['kelas', 'user'])->get();
    }

    public function map($siswa): array
    {
        return [
            $siswa->nim,
            $siswa->nama,
            $siswa->kelas?->name ?? '-',
            $siswa->email ?? '-',
            $siswa->no_telepon ?? '-',
            $siswa->alamat ?? '-',
            $siswa->user?->email ?? '-',
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
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            ],
        ];
    }
}
