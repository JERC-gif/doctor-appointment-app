<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el rol 'doctor' (en minúscula como está en RoleSeeder)
        $role = \App\Models\Role::where('name', 'doctor')->first();

        User::create([
            'name' => 'Jorge Ruiz',
            'email' => 'jorgeprueba@gmail.com',
            'password' => 'jorge1234',
            'id_number' => 123456789,
            'phone' => '5555555555',
            'address' => 'Calle 123, Colonia 123,',
            'role_id' => $role ? $role->id : null,
        ]);
    }
}
