<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\AppointmentType;
use App\Models\Hospital;
use App\Models\Product;
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
        if (!User::query()->where('username', 'admin')->exists()) {
            User::factory()->create([
                'username' => 'admin',
                'email' => 'admin@mail.com',
                'type' => UserType::ADMIN,
            ]);
        }

        Province::factory()
            ->count(2)
            ->has(
                Hospital::factory()
                    ->count(2)
                    ->hasDoctors(5)
                    ->hasServices(20)
            )
            ->hasPatients(1000)
            ->create();

        Product::factory()->count(20)->create();

        if (AppointmentType::count() === 0) {
            AppointmentType::create(['name' => 'İlk Randevu']);
            AppointmentType::create(['name' => 'Muayene']);
            AppointmentType::create(['name' => 'Ameliyat']);
            AppointmentType::create(['name' => 'Kontrol']);
        }
    }
}
