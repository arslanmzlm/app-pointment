<?php

namespace Database\Factories;

use App\Models\Province;
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
            'province_id' => Province::query()->inRandomOrder()->first()->id,
            'name' => $this->faker->company(),
            'start_work' => $this->faker->numberBetween(8, 9),
            'end_work' => $this->faker->numberBetween(18, 19),
            'duration' => $this->faker->randomElement([30, 45, 60]),
            'description' => $this->faker->randomHtml(10),
            'owner' => $this->faker->name(),
            'phone' => $this->faker->e164PhoneNumber(),
            'email' => $this->faker->companyEmail(),
            'address' => $this->faker->address(),
            'address_link' => $this->faker->url(),
        ];
    }
}
