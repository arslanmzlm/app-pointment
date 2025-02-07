<?php

namespace Database\Factories;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => UserType::PATIENT,
            'username' => fake()->unique()->e164PhoneNumber(),
            'name' => fake()->name(),
            'phone' => fake()->e164PhoneNumber,
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('11234566'),
            'remember_token' => Str::random(10),
        ];
    }
}
