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
            'kepala_tefa',
            'bendahara',
            'marketing',
            'admin',
            'produksi',
            'designer',
            'qa'
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

        // Kepala TEFA
        $kepalaTefa = User::firstOrCreate(
            ['email' => 'kepala_tefa@tefa.com'],
            [
                'name' => 'Kepala TEFA',
                'password' => Hash::make('password')    
            ]
        );
        $kepalaTefa->assignRole('kepala_tefa');

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@tefa.com'],
            [
                'name' => 'Admin TEFA',
                'password' => Hash::make('password')
            ]
        );
        $admin->assignRole('admin');

        // Produksi
        $produksi = User::firstOrCreate(
            ['email' => 'produksi@tefa.com'],
            [
                'name' => 'Produksi TEFA',
                'password' => Hash::make('password')
            ]
        );
        $produksi->assignRole('produksi');

        

        // Designer
        $designer = User::firstOrCreate(
            ['email' => 'designer@tefa.com'],
            [
                'name' => 'Designer TEFA',
                'password' => Hash::make('password')
            ]
        );
        $designer->assignRole('designer');

        // QA
        $qa = User::firstOrCreate(
            ['email' => 'qa@tefa.com'],
            [
                'name' => 'QA TEFA',
                'password' => Hash::make('password')
            ]
        );
        $qa->assignRole('qa');


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

        
    }
}
