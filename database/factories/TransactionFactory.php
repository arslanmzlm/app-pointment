<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\TransactionType;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hospital_id = Hospital::all()->random()->id;
        $date = $this->faker->dateTimeBetween('start of the year');

        return [
            'type' => $this->faker->randomElement(TransactionType::cases()),
            'method' => $this->faker->randomElement(PaymentMethod::cases()),
            'total' => $this->faker->randomFloat(2, 200, 99999),
            'user_id' => User::all()->random()->id,
            'hospital_id' => $hospital_id,
            'doctor_id' => $this->faker->boolean() ? Doctor::query()->where('hospital_id', $hospital_id)->get()->random()->id : null,
            'patient_id' => $this->faker->boolean() ? Patient::all()->random()->id : null,
            'treatment_id' => null,
            'description' => null,
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
