<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //definir role
        $roles = [
            'paciente',
            'doctor',
            'recepcionista',
            'Administrador',
        ];
        //crear en la db
        foreach ($roles as $role) {
            Role::create([
                'name' => $role
            ]);
        }
    }
}
