<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hospital>
 */
class HospitalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'owner' => $this->faker->name(),
            'start_work' => $this->faker->numberBetween(8, 9),
            'end_work' => $this->faker->numberBetween(18, 19),
            'duration' => $this->faker->randomElement([35, 45, 60]),
            'phone' => $this->faker->e164PhoneNumber(),
            'email' => $this->faker->companyEmail(),
            'address' => $this->faker->address(),
        ];
    }
}
