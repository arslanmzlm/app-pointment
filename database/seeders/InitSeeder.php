<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Patient;
use App\Models\Product;
use Illuminate\Database\Seeder;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Patient::factory()->count(2000)->create();

        Hospital::factory()
            ->count(2)
            ->hasDoctors(5)
            ->hasServices(20)
            ->create();

        Product::factory()->count(20)->create();
    }
}
