<?php

namespace Database\Seeders;

use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    /**
     * Pacientes de ejemplo (rol paciente + perfil Patient).
     */
    public function run(): void
    {
        $bloodTypeIds = BloodType::pluck('id')->toArray();
        if ($bloodTypeIds === []) {
            return;
        }

        $patients = [
            ['name' => 'Karlos', 'email' => 'karlos@medimatch.com'],
            ['name' => 'Jose delgado', 'email' => 'jose.delgado@medimatch.com'],
            ['name' => 'Nico', 'email' => 'nico@medimatch.com'],
            ['name' => 'Kevin duran', 'email' => 'kevin.duran@medimatch.com'],
            ['name' => 'Joel Camarena', 'email' => 'joel.camarena@medimatch.com'],
            ['name' => 'Luisito Flores', 'email' => 'luisito.flores@medimatch.com'],
            ['name' => 'Javier Blanco', 'email' => 'javier.blanco@medimatch.com'],
            ['name' => 'Andres Lima', 'email' => 'andres.lima@medimatch.com'],
        ];

        foreach ($patients as $index => $row) {
            $user = User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'password' => Hash::make('password'),
                    'id_number' => 200_000_001 + $index,
                    'phone' => '556'.str_pad((string) ($index + 1), 7, '0', STR_PAD_LEFT),
                    'address' => 'Calle demo '.($index + 1).', Mérida',
                ]
            );
            if (! $user->hasRole('paciente')) {
                $user->assignRole('paciente');
            }

            Patient::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'blood_type_id' => fake()->randomElement($bloodTypeIds),
                    'date_of_birth' => now()->subYears(25 + $index)->format('Y-m-d'),
                    'gender' => $index % 2 === 0 ? 'male' : 'female',
                    'emergency_contact_name' => 'Contacto emergencia',
                    'emergency_contact_phone' => '5550000000',
                    'emergency_relationship' => 'Familiar',
                ]
            );
        }

        for ($i = 0; $i < 3; $i++) {
            $user = User::factory()->create();
            if (! $user->hasRole('paciente')) {
                $user->assignRole('paciente');
            }
            Patient::factory()->create([
                'user_id' => $user->id,
                'blood_type_id' => fake()->randomElement($bloodTypeIds),
            ]);
        }
    }
}
