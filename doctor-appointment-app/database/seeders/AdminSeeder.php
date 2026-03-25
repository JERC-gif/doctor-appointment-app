<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Usuarios con rol Spatie "Administrador" (reciben reporte diario de citas).
     */
    public function run(): void
    {
        $admins = [
            ['name' => 'Jorge Ruiz', 'email' => 'jrui46783@gmail.com', 'id_number' => 100_000_001],
            ['name' => 'Nico Administrador', 'email' => 'admin.nico@medimatch.com', 'id_number' => 100_000_002],
            ['name' => 'Kevin Durán', 'email' => 'admin.kevin@medimatch.com', 'id_number' => 100_000_003],
        ];

        foreach ($admins as $row) {
            $user = User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'password' => Hash::make('password'),
                    'id_number' => $row['id_number'],
                    'phone' => '555'.str_pad((string) ($row['id_number'] % 1_000_000), 7, '0', STR_PAD_LEFT),
                    'address' => 'Oficina administración MediMatch',
                ]
            );
            if (! $user->hasRole('Administrador')) {
                $user->assignRole('Administrador');
            }
        }
    }
}
