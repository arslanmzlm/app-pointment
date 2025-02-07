<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Hospital;
use App\Models\Province;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@mail.com',
            'type' => UserType::ADMIN
        ]);

        Province::factory()
            ->count(5)
            ->has(
                Hospital::factory()
                    ->count(2)
                    ->hasDoctors(10)
                    ->hasServices(20)
                    ->hasProducts(20)
            )
            ->hasPatients(1000)
            ->create();
    }
}
