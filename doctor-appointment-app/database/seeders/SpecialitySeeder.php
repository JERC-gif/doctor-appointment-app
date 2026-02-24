<?php

// Se agregaron las especialidades médicas iniciales; se usa firstOrCreate para evitar duplicados

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SpecialitySeeder extends Seeder
{
    public function run(): void
    {
        $specialities = [
            'Cardiología',
            'Pediatría',
            'Dermatología',
            'Neurología',
            'Ginecología',
            'Traumatología',
            'Oftalmología',
            'Psiquiatría',
        ];

        foreach ($specialities as $speciality) {
            \App\Models\Speciality::firstOrCreate(['name' => $speciality]);
        }
    }
}
