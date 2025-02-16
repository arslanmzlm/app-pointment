<?php

namespace Database\Factories;

use App\Enums\AppointmentState;
use App\Models\AppointmentType;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = User::query()->inRandomOrder()->first()->id;
        $doctor = Doctor::query()->inRandomOrder()->first();
        $patient = Patient::query()->inRandomOrder()->first();
        if ($this->faker->boolean(10) && $patient->user) {
            $userId = $patient->user->id;
        } else if ($this->faker->boolean(90) && $doctor->user) {
            $userId = $doctor->user->id;
        }
        $date = Carbon::parse($this->faker->dateTimeBetween('-2 months', '+2 months'));
        $duration = $this->faker->randomElement([15, 30, 45, 60, 90, 120]);

        return [
            'appointment_type_id' => AppointmentType::query()->inRandomOrder()->first()->id,
            'user_id' => $userId,
            'hospital_id' => $doctor->hospital_id,
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'treatment_id' => null,
            'state' => $this->faker->randomElement(AppointmentState::cases()),
            'start_date' => $date,
            'due_date' => $date->clone()->addMinutes($duration),
            'duration' => $duration,
            'title' => $this->faker->sentence(),
            'note' => $this->faker->boolean() ? $this->faker->paragraph() : null,
        ];
    }
}
