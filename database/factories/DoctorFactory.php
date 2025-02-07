<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'name' => $firstName,
            'surname' => $lastName,
            'full_name' => "{$firstName} {$lastName}",
            'branch' => $this->faker->jobTitle(),
            'title' => $this->faker->jobTitle(),
            'phone' => $this->faker->unique()->e164PhoneNumber(),
            'email' => $this->faker->unique()->email(),
        ];
    }
}
