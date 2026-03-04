<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role sudah ada
        $roles = [
            'guru',
            'siswa',
            'kepala_produksi',
            'bendahara',
            'marketing'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web'
            ]);
        }

        // Guru
        $guru = User::firstOrCreate(
            ['email' => 'guru@tefa.com'],
            [
                'name' => 'Guru TEFA',
                'password' => Hash::make('password')
            ]
        );
        $guru->assignRole('guru');

        // Kepala Produksi
        $kp = User::firstOrCreate(
            ['email' => 'produksi@tefa.com'],
            [
                'name' => 'Kepala Produksi',
                'password' => Hash::make('password')
            ]
        );
        $kp->assignRole('kepala_produksi');

        // Bendahara
        $bendahara = User::firstOrCreate(
            ['email' => 'bendahara@tefa.com'],
            [
                'name' => 'Bendahara',
                'password' => Hash::make('password')
            ]
        );
        $bendahara->assignRole('bendahara');

        // Marketing
        $marketing = User::firstOrCreate(
            ['email' => 'marketing@tefa.com'],
            [
                'name' => 'Marketing TEFA',
                'password' => Hash::make('password')
            ]
        );
        $marketing->assignRole('marketing');

        // 5 Siswa
        for ($i = 1; $i <= 5; $i++) {
            $siswa = User::firstOrCreate(
                ['email' => "siswa$i@tefa.com"],
                [
                    'name' => "Siswa $i",
                    'password' => Hash::make('password')
                ]
            );
            $siswa->assignRole('siswa');
        }
    }
}
