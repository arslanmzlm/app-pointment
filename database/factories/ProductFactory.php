<?php

namespace Database\Factories;

use App\Models\Hospital;
use App\Models\Product;
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
            'brand' => $this->faker->company,
            'name' => $this->faker->randomElement($elements),
            'slug' => $this->faker->unique()->slug(),
            'code' => $this->faker->postcode(),
            'price' => $this->faker->numberBetween(100, 1000),
            'description' => $this->faker->randomHtml(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            Hospital::all()->each(function (Hospital $hospital) use ($product) {
                $product->stocks()->create(['hospital_id' => $hospital->id, 'stock' => $this->faker->numberBetween(1, 1000)]);
            });
        });
    }
}
