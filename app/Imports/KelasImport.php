<?php

namespace App\Imports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;

class KelasImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $importedCount = 0;
    private $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Check if required fields exist
                if (!isset($row['nama_kelas']) || empty($row['nama_kelas'])) {
                    $this->errors[] = "Baris {$row->index}: Nama Kelas tidak boleh kosong";
                    continue;
                }

                if (!isset($row['kode_kelas']) || empty($row['kode_kelas'])) {
                    $this->errors[] = "Baris {$row->index}: Kode Kelas tidak boleh kosong";
                    continue;
                }

                if (!isset($row['kapasitas']) || empty($row['kapasitas'])) {
                    $this->errors[] = "Baris {$row->index}: Kapasitas tidak boleh kosong";
                    continue;
                }

                // Check for duplicates
                $existingByCode = Kelas::where('code', trim($row['kode_kelas']))->first();
                if ($existingByCode) {
                    $this->errors[] = "Baris {$row->index}: Kode Kelas '{$row['kode_kelas']}' sudah ada";
                    continue;
                }

                // Validate capacity
                $capacity = (int) $row['kapasitas'];
                if ($capacity < 1 || $capacity > 200) {
                    $this->errors[] = "Baris {$row->index}: Kapasitas harus antara 1-200";
                    continue;
                }

                // Create kelas
                Kelas::create([
                    'name' => trim($row['nama_kelas']),
                    'code' => trim($row['kode_kelas']),
                    'description' => trim($row['deskripsi'] ?? ''),
                    'capacity' => $capacity,
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->errors[] = "Baris {$row->index}: {$e->getMessage()}";
            }
        }
    }

    public function rules(): array
    {
        return [
            '*.nama_kelas' => 'required|string',
            '*.kode_kelas' => 'required|string',
            '*.kapasitas' => 'required|numeric',
        ];
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
