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
        User::factory()->create([
            'name' => 'Jorge Ruiz',
            'email' => 'jorgeprueba@gmail.com',
            'password' => bcrypt('jorge1234')
        ]);
    }
};
