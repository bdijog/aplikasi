<?php

namespace Database\Factories;

use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\QueueTicket;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QueueTicket>
 */
class QueueTicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<QueueTicket>
     */
    protected $model = QueueTicket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $queueNumber = fake()->numberBetween(1, 30);
        $prefix = fake()->randomElement(['A', 'B', 'C']);

        return [
            'appointment_id' => Appointment::factory(),
            'doctor_id' => Doctor::factory(),
            'schedule_id' => Schedule::factory(),
            'queue_date' => date('Y-m-d'),
            'queue_number' => $queueNumber,
            'prefix' => $prefix,
            'display_number' => $prefix . '-' . str_pad((string) $queueNumber, 3, '0', STR_PAD_LEFT),
            'status' => QueueTicketStatus::Waiting,
            'priority' => QueueTicketPriority::Normal,
            'called_at' => null,
            'served_at' => null,
            'completed_at' => null,
            'call_count' => 0,
            'counter' => 'Poli ' . fake()->numberBetween(1, 5),
            'notes' => null,
        ];
    }

    /**
     * State for ticket currently being served.
     */
    public function serving(?string $counter = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QueueTicketStatus::Serving,
            'called_at' => now()->subMinutes(5),
            'served_at' => now()->subMinutes(3),
            'call_count' => 1,
            'counter' => $counter ?? 'Poli 1',
        ]);
    }

    /**
     * State for completed queue ticket.
     */
    public function completed(?string $counter = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QueueTicketStatus::Completed,
            'called_at' => now()->subMinutes(30),
            'served_at' => now()->subMinutes(25),
            'completed_at' => now()->subMinutes(5),
            'call_count' => 1,
            'counter' => $counter ?? 'Poli 1',
        ]);
    }

    /**
     * State for skipped ticket.
     */
    public function skipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QueueTicketStatus::Skipped,
            'called_at' => now()->subMinutes(20),
            'call_count' => 3,
            'notes' => 'Pasien tidak hadir saat dipanggil 3x',
        ]);
    }

    /**
     * State for priority patient.
     */
    public function priority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => QueueTicketPriority::Priority,
            'notes' => 'Prioritas: Pasien Lansia / Ibu Hamil',
        ]);
    }

    /**
     * State for emergency ticket.
     */
    public function emergency(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => QueueTicketPriority::Emergency,
            'notes' => 'Emergency / Gawat Darurat',
        ]);
    }
}
