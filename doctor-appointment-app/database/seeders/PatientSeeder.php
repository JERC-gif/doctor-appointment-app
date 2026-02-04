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

        // Crear 10 pacientes con sus usuarios asociados
        for ($i = 0; $i < 10; $i++) {
            // Crear usuario con rol de Paciente
            $user = User::factory()->create();
            $user->assignRole('Paciente');

            // Crear registro de paciente vinculado al usuario
            Patient::factory()->create([
                'user_id' => $user->id,
                'blood_type_id' => fake()->randomElement($bloodTypeIds),
            ]);
        }
    }
}