<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //llamar a rolleseeder
        $this->call([
            RoleSeeder::class
        ]);

        // User::factory(10)->create();
        User::create([
            'name' => 'Jorge Ruiz',
            'email' => 'jorgeprueba@gmail.com',
            'password' => 'jorge1234', // El modelo tiene 'hashed' cast, así que se hashea automáticamente
            'email_verified_at' => now(),
        ]);

        // Crear usuario admin adicional
        User::create([
            'name' => 'Admin',
            'email' => 'admin@healthify.com',
            'password' => 'admin1234',
            'email_verified_at' => now(),
        ]);
    }
};
