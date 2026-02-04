<?php

namespace Database\Factories;

use App\Models\BloodType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'blood_type_id' => BloodType::inRandomOrder()->first()?->id,
            'date_of_birth' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'allergies' => fake()->optional(0.3)->sentence(),
            'chronic_conditions' => fake()->optional(0.3)->sentence(),
            'surgical_history' => fake()->optional(0.2)->sentence(),
            'observations' => fake()->optional(0.4)->paragraph(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->numerify('999#######'),
            'emergency_relationship' => fake()->randomElement(['Padre', 'Madre', 'Hermano/a', 'Esposo/a', 'Hijo/a', 'Otro']),
        ];
    }
}