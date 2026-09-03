<?php

namespace Database\Factories;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Schedule>
     */
    protected $model = Schedule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shifts = [
            ['start' => '08:00:00', 'end' => '12:00:00', 'notes' => 'Sesi Pagi'],
            ['start' => '13:00:00', 'end' => '17:00:00', 'notes' => 'Sesi Siang'],
            ['start' => '17:00:00', 'end' => '21:00:00', 'notes' => 'Sesi Sore / Malam'],
        ];

        $shift = fake()->randomElement($shifts);

        return [
            'doctor_id' => Doctor::factory(),
            'day_of_week' => fake()->numberBetween(1, 6), // Senin - Sabtu
            'specific_date' => null,
            'start_time' => $shift['start'],
            'end_time' => $shift['end'],
            'max_patients' => fake()->randomElement([15, 20, 25, 30]),
            'status' => ScheduleStatus::Active,
            'notes' => $shift['notes'],
            'type' => ScheduleType::Recurring,
        ];
    }

    /**
     * State for one-time special schedule.
     */
    public function oneTime(?string $date = null): static
    {
        $specificDate = $date ?? fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d');
        $dayOfWeek = (int) date('w', strtotime($specificDate));

        return $this->state(fn (array $attributes) => [
            'type' => ScheduleType::OneTime,
            'specific_date' => $specificDate,
            'day_of_week' => $dayOfWeek,
            'notes' => 'Jadwal Khusus / Pengganti',
        ]);
    }

    /**
     * State for inactive schedule.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ScheduleStatus::Inactive,
        ]);
    }

    /**
     * State for cancelled schedule.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ScheduleStatus::Cancelled,
        ]);
    }

    /**
     * State for specific day of the week.
     */
    public function forDay(int $dayOfWeek): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => $dayOfWeek,
        ]);
    }

    /**
     * State for morning shift.
     */
    public function morningShift(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'notes' => 'Sesi Pagi',
        ]);
    }

    /**
     * State for afternoon shift.
     */
    public function afternoonShift(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => '13:00:00',
            'end_time' => '17:00:00',
            'notes' => 'Sesi Siang',
        ]);
    }
}
