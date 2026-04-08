<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $errors = [];
    protected $imported = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Validasi NIM tidak kosong
                if (empty($row['nim'])) {
                    $this->errors[] = "Baris " . ($row->getIndex() + 1) . ": NIM tidak boleh kosong";
                    continue;
                }

                // Cek NIM duplikat di database
                if (Siswa::where('nim', $row['nim'])->exists()) {
                    $this->errors[] = "Baris " . ($row->getIndex() + 1) . ": NIM {$row['nim']} sudah terdaftar";
                    continue;
                }

                $siswaData = [
                    'nim' => $row['nim'],
                    'nama' => $row['nama'] ?? '',
                    'email' => $row['email_siswa'] ?? null,
                    'no_telepon' => $row['no_telepon'] ?? null,
                    'alamat' => $row['alamat'] ?? null,
                ];

                // Handle kelas lookup
                if (!empty($row['kelas'])) {
                    $kelas = \App\Models\Kelas::where('name', $row['kelas'])
                        ->orWhere('code', $row['kelas'])
                        ->first();
                    if ($kelas) {
                        $siswaData['kelas_id'] = $kelas->id;
                    }
                }

                // Handle user creation
                if (!empty($row['username_login']) && !empty($row['password'])) {
                    // Check if user exists
                    $user = User::where('email', $row['username_login'])->first();
                    if (!$user) {
                        $user = User::create([
                            'email' => $row['username_login'],
                            'name' => $row['nama'] ?? $row['username_login'],
                            'password' => Hash::make($row['password']),
                        ]);
                        $user->assignRole('siswa');
                    }
                    $siswaData['user_id'] = $user->id;
                }

                Siswa::create($siswaData);
                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = "Baris " . ($row->getIndex() + 1) . ": " . $e->getMessage();
            }
        }
    }

    public function rules(): array
    {
        return [
            'nim' => 'required|string',
            'nama' => 'required|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nim.required' => 'NIM tidak boleh kosong',
            'nama.required' => 'Nama tidak boleh kosong',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getImportedCount()
    {
        return $this->imported;
    }
}
