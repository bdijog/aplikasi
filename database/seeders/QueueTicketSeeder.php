<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use App\Models\Appointment;
use App\Models\QueueTicket;
use Illuminate\Database\Seeder;

class QueueTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = date('Y-m-d');

        // Fetch today's appointments that don't have queue tickets yet
        $appointments = Appointment::where('appointment_date', $today)
            ->whereDoesntHave('queueTicket')
            ->get();

        if ($appointments->isEmpty()) {
            $this->call(AppointmentSeeder::class);
            $appointments = Appointment::where('appointment_date', $today)
                ->whereDoesntHave('queueTicket')
                ->get();
        }

        $queueByDoctor = [];

        foreach ($appointments as $appointment) {
            $doctorId = $appointment->doctor_id;
            $queueByDoctor[$doctorId] = ($queueByDoctor[$doctorId] ?? 0) + 1;
            $queueNumber = $queueByDoctor[$doctorId];

            $prefix = chr(65 + ($doctorId % 26)); // 'A', 'B', etc.
            $displayNumber = $prefix . '-' . str_pad((string) $queueNumber, 3, '0', STR_PAD_LEFT);

            // Determine status based on appointment status
            $ticketStatus = match ($appointment->status) {
                AppointmentStatus::Completed => QueueTicketStatus::Completed,
                AppointmentStatus::InProgress => QueueTicketStatus::Serving,
                default => QueueTicketStatus::Waiting,
            };

            QueueTicket::updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'doctor_id' => $appointment->doctor_id,
                    'schedule_id' => $appointment->schedule_id,
                    'queue_date' => $today,
                    'queue_number' => $queueNumber,
                    'prefix' => $prefix,
                    'display_number' => $displayNumber,
                    'status' => $ticketStatus,
                    'priority' => QueueTicketPriority::Normal,
                    'counter' => 'Poli ' . $prefix . ' 1',
                    'call_count' => $ticketStatus === QueueTicketStatus::Waiting ? 0 : 1,
                    'called_at' => $ticketStatus === QueueTicketStatus::Waiting ? null : now()->subMinutes(10),
                    'served_at' => $ticketStatus === QueueTicketStatus::Waiting ? null : now()->subMinutes(8),
                    'completed_at' => $ticketStatus === QueueTicketStatus::Completed ? now()->subMinutes(2) : null,
                ]
            );
        }
    }
}
