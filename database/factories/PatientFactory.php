<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Models\Province;
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
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $date = $this->faker->dateTimeBetween('-2 months');

        return [
            'province_id' => Province::query()->inRandomOrder()->first()->id,
            'old' => $this->faker->boolean(),
            'name' => $firstName,
            'surname' => $lastName,
            'full_name' => "{$firstName} {$lastName}",
            'phone' => $this->faker->unique()->e164PhoneNumber(),
            'email' => $this->faker->unique()->email(),
            'gender' => $this->faker->randomElement(Gender::cases()),
            'birthday' => $this->faker->date(),
            'notification' => $this->faker->boolean(),
            'created_by' => $this->faker->boolean() ? User::query()->inRandomOrder()->first()->id : null,
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
