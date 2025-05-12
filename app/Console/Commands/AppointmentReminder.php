<?php

namespace App\Console\Commands;

use App\Services\MessageService;
use Illuminate\Console\Command;

class AppointmentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:appointment:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminds appointments.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        \Log::info('Appointment reminder command executed.');

        MessageService::remindMany();
    }
}
