<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Service;
use App\Models\Treatment;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Treatment>
 */
class TreatmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hospital = Hospital::query()->inRandomOrder()->first();
        $doctor = $hospital->doctors->random();
        $date = $this->faker->dateTimeBetween('-2 months');

        return [
            'user_id' => User::query()->inRandomOrder()->first()->id,
            'hospital_id' => $hospital->id,
            'doctor_id' => $doctor->id,
            'patient_id' => Patient::query()->inRandomOrder()->first()->id,
            'total' => null,
            'note' => $this->faker->boolean(25) ? $this->faker->text() : null,
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Treatment $treatment) {
            $total = 0;

            $services = [];
            $serviceCount = 1 + ($this->faker->boolean(10) ? $this->faker->numberBetween(1, 2) : 0);

            for ($i = 0; $i < $serviceCount; $i++) {
                $service = Service::query()->where('hospital_id', $treatment->hospital_id)->inRandomOrder()->first();

                if ($service) {
                    $price = $service->price;

                    $services[] = [
                        'service_id' => $service->id,
                        'price' => $price,
                    ];

                    $total += $price;
                }
            }

            $treatment->services()->createMany($services);

            if ($this->faker->boolean(25)) {
                $productCount = $this->faker->numberBetween(1, 3);

                for ($i = 0; $i < $productCount; $i++) {
                    $product = Product::query()->inRandomOrder()->first();

                    if ($product) {
                        $count = $this->faker->numberBetween(1, 5);

                        $products[] = [
                            'product_id' => $product->id,
                            'count' => $count,
                            'price' => $product->price,
                            'total' => $count * $product->price,
                        ];

                        $total += $count * $product->price;
                    }
                }
            }

            if (!empty($products)) $treatment->products()->createMany($products);

            $treatment->total = $total;
            $treatment->save();

            $payments = [[
                'method' => $this->faker->randomElement(PaymentMethod::cases()),
                'amount' => $treatment->total
            ]];

            TransactionService::storeByTreatment($treatment, $payments);
        });
    }
}
