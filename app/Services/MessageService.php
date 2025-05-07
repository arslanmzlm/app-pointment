<?php

namespace App\Services;

use App\Enums\MessageType;
use App\Models\Appointment;
use App\Models\Message;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

class MessageService
{
    public static function checkPatient(Patient $patient): bool
    {
        if ($patient->phone === null) {
            return false;
        }

        if (!$patient->notification) {
            return false;
        }

        return true;
    }

    public static function appoint(Appointment $appointment): void
    {
        $patient = $appointment->patient;

        if (self::checkPatient($patient)) {
            $date = $appointment->start_date->format('d.m.Y H:i');
            $hospitalPhone = $appointment->hospital->phone_normalized;

            $phone = $patient->phone->getRawNumber();
            $message = "Sn. {$patient->full_name}, {$date} tarihinde kliniğimizde randevunuz oluşturulmuştur. Kliniğimizi {$hospitalPhone} numarasından arayarak iptal edebilirsiniz. BTM Ayak Sağlığı Merkezi sağlıklı günler diler.";

            $jobId = SmsService::send($phone, $message);

            self::log(MessageType::APPOINT, $phone, $message, $jobId, patientId: $patient->id);
        }
    }

    public static function remindMany(): void
    {
        $appointments = AppointmentService::getForReminder();
        $chunks = $appointments->chunk(100);

        foreach ($chunks as $chunk) {
            /* @var Collection<int, Appointment> $chunk */

            $messages = [];

            foreach ($chunk as $appointment) {
                $patient = $appointment->patient;

                if (self::checkPatient($patient)) {
                    $date = $appointment->start_date->format('d.m.Y H:i');
                    $hospitalPhone = $appointment->hospital->phone_normalized;

                    $messages[] = [
                        'patient_id' => $patient->id,
                        'phone' => $patient->phone->getRawNumber(),
                        'message' => "Sn. {$patient->full_name}, yarın ({$date}) kliniğimizde randevunuz vardır. Kliniğimizi {$hospitalPhone} numarasından arayarak iptal edebilirsiniz. BTM Ayak Sağlığı Merkezi sağlıklı günler diler."
                    ];
                }
            }

            $jobId = SmsService::sendMany($messages);

            self::logMany(MessageType::REMIND, $messages, $jobId);
        }
    }

    private static function log(MessageType $messageType, string $phone, string $message, mixed $jobId = null, ?int $patientId = null, ?int $userId = null): void
    {
        Message::query()->create([
            'type' => $messageType,
            'patient_id' => $patientId,
            'user_id' => $userId,
            'phone' => $phone,
            'message' => $message,
            'job_id' => $jobId,
        ]);
    }

    private static function logMany(MessageType $messageType, array $messages, mixed $jobId = null): void
    {
        $insert = [];
        $now = now();

        foreach ($messages as $message) {
            $insert[] = [
                'type' => $messageType,
                'patient_id' => $message['patient_id'] ?? null,
                'user_id' => $message['user_id'] ?? null,
                'phone' => $message['phone'],
                'message' => $message['message'],
                'job_id' => $jobId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Message::query()->insert($insert);
    }
}
