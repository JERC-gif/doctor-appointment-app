<?php

namespace Database\Seeders;

use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de tipos de sangre disponibles
        $bloodTypeIds = BloodType::pluck('id')->toArray();

        // Pacientes con nombres específicos solicitados
        $namedPatients = [
            'Jorge',
            'Javier',
            'Andres',
            'Karlos',
            'Kevin',
            'Jose',
            'Luisito',
            'Joel',
        ];

        foreach ($namedPatients as $index => $name) {
            $user = User::factory()->create([
                'name'  => $name,
                'email' => 'paciente' . ($index + 1) . '@medimatch.com',
            ]);
            $user->assignRole('paciente');

            Patient::factory()->create([
                'user_id' => $user->id,
                'blood_type_id' => fake()->randomElement($bloodTypeIds),
            ]);
        }

        // Pacientes adicionales aleatorios para tener más variedad
        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create();
            $user->assignRole('paciente');

            Patient::factory()->create([
                'user_id' => $user->id,
                'blood_type_id' => fake()->randomElement($bloodTypeIds),
            ]);
        }
    }
}