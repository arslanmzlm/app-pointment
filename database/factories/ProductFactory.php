<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $elements = [
            'VitaBoost',
            'PureEssence',
            'HealthGuard',
            'LifeFlex',
            'BioCare',
            'ImmuneShield',
            'RevitaGlow',
            'NutraVital',
            'FreshStart',
            'BodyHarmony',
            'WellnessWave',
            'FlexiFit',
            'HealMax',
            'SereneLife',
            'VitalFlow',
            'NutriPower',
            'WellnessPulse',
            'EnergyPlus',
            'NatureCure',
            'BioBalance',
        ];

        return [
            'active' => true,
            'category' => $this->faker->randomElement(['İlaç', 'Takviye', 'Tıbbi Malzeme']),
            'name' => $this->faker->randomElement($elements),
            'code' => $this->faker->postcode(),
            'stock' => $this->faker->numberBetween(50, 500),
            'price' => $this->faker->numberBetween(100, 1000),
        ];
    }
}
