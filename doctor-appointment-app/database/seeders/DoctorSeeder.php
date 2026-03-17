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

        $names = [
            'Dr. Carlos Pérez',
            'Dra. María González',
            'Dr. Luis Torres',
            'Dra. Ana Martínez',
            'Dr. Roberto Sánchez',
            'Dra. Laura Ramírez',
        ];

        foreach ($names as $index => $name) {
            $email = 'doctor' . ($index + 1) . '@medimatch.com';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => $name,
                    'password' => Hash::make('12345678'),
                    'id_number' => 'DOC-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'phone'    => '999' . str_pad($index + 1, 7, '0', STR_PAD_LEFT),
                    'address'  => 'Consultorio ' . ($index + 1),
                ]
            );

            $user->assignRole('doctor');

            Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'speciality_id' => $specialities->random()->id,
                    'medical_license_number' => 'LIC-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                    'biography' => 'Médico especialista con amplia experiencia clínica.',
                ]
            );
        }
    }
}

