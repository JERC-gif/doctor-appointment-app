<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $specialities = Speciality::all();
        if ($specialities->isEmpty()) {
            return;
        }

        $doctors = [
            ['name' => 'Dr. Jose delgado', 'email' => 'doctor.jose.delgado@medimatch.com'],
            ['name' => 'Dra. Nico Hernández', 'email' => 'doctor.nico@medimatch.com'],
            ['name' => 'Dr. Kevin duran', 'email' => 'doctor.kevin.duran@medimatch.com'],
            ['name' => 'Dr. Karlos Vega', 'email' => 'doctor.karlos@medimatch.com'],
            ['name' => 'Dra. Laura Martínez', 'email' => 'doctor.laura@medimatch.com'],
            ['name' => 'Dr. Luisito Ríos', 'email' => 'doctor.luisito@medimatch.com'],
        ];

        $specList = $specialities->values();

        foreach ($doctors as $index => $row) {
            $user = User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'password' => Hash::make('password'),
                    'id_number' => 300_000_001 + $index,
                    'phone' => '557'.str_pad((string) ($index + 1), 7, '0', STR_PAD_LEFT),
                    'address' => 'Consultorio '.($index + 1).', Mérida',
                ]
            );
            if (! $user->hasRole('doctor')) {
                $user->assignRole('doctor');
            }

            Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'speciality_id' => $specList[$index % $specList->count()]->id,
                    'medical_license_number' => 'LIC-'.str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
                    'biography' => 'Médico de ejemplo MediMatch.',
                ]
            );
        }
    }
}
