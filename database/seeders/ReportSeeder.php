<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Transaction;
use App\Models\Treatment;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Appointment::factory()->count(1000)->create();
        Treatment::factory()->count(1000)->create();
        Transaction::factory()->count(1000)->create();
    }
}
