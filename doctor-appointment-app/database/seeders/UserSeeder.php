<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Usuario principal de desarrollo para entrar al panel (/admin tras login en /login).
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'jorgeprueba@gmail.com'],
            [
                'name' => 'Jorge Ruiz',
                'password' => Hash::make('123456789'),
                'id_number' => 123456789,
                'phone' => '5555555555',
                'address' => 'Calle 123, Colonia demo',
            ]
        );

        if (! $user->hasRole('Administrador')) {
            $user->assignRole('Administrador');
        }
    }
}
