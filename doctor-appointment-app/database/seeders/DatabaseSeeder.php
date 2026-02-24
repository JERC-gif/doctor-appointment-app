<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // El orden importa: roles y usuarios primero, luego datos dependientes
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            BloodTypeSeeder::class,
            PatientSeeder::class,
            SpecialitySeeder::class,
        ]);
    }
};
