<?php

namespace Database\Factories;

use App\Enums\AppointmentSource;
use App\Enums\AppointmentStatus;
use App\Enums\CheckInMethod;
use App\Enums\VisitType;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Appointment>
     */
    protected $model = Appointment::class;

    /**
     * List of realistic medical complaints in Indonesian.
     *
     * @var list<string>
     */
    protected static array $complaints = [
        'Demam tinggi dan sakit kepala sejak 3 hari lalu',
        'Batuk kering disertai sesak napas ringan',
        'Kontrol rutin tekanan darah dan gula darah',
        'Nyeri ulu hati dan mual setelah makan',
        'Pemeriksaan kehamilan rutin (USG)',
        'Mata merah, berair, dan terasa berpasir',
        'Nyeri dada hilang timbul dan mudah lelah',
        'Alergi kulit gatal-gatal kemerahan di seluruh tubuh',
        'Nyeri sendi lutut terutama saat berjalan',
        'Konsultasi imunisasi dan tumbuh kembang anak',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $appointmentDate = fake()->dateTimeBetween('now', '+14 days')->format('Y-m-d');

        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'schedule_id' => Schedule::factory(),
            'booking_code' => 'APT-' . date('Ymd', strtotime($appointmentDate)) . '-' . fake()->unique()->numerify('####'),
            'appointment_date' => $appointmentDate,
            'estimated_time' => fake()->randomElement(['08:30:00', '09:00:00', '09:30:00', '10:00:00', '13:30:00', '14:00:00', '14:30:00']),
            'visit_type' => fake()->randomElement([VisitType::NewVisit, VisitType::FollowUp]),
            'chief_complaint' => fake()->randomElement(self::$complaints),
            'patient_notes' => fake()->optional(0.3)->sentence(),
            'status' => AppointmentStatus::Confirmed,
            'source' => fake()->randomElement([AppointmentSource::Online, AppointmentSource::WalkIn, AppointmentSource::Phone]),
            'cancellation_reason' => null,
            'cancelled_at' => null,
            'checked_in_at' => null,
            'check_in_method' => null,
            'checked_in_by' => null,
            'created_by' => null,
            'metadata' => null,
        ];
    }

    /**
     * State for pending appointment.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::Pending,
        ]);
    }

    /**
     * State for checked-in appointment.
     */
    public function checkedIn(?CheckInMethod $method = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::CheckedIn,
            'checked_in_at' => now(),
            'check_in_method' => $method ?? CheckInMethod::SelfService,
        ]);
    }

    /**
     * State for in-progress consultation.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::InProgress,
            'checked_in_at' => now()->subMinutes(20),
            'check_in_method' => CheckInMethod::SelfService,
        ]);
    }

    /**
     * State for completed appointment.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::Completed,
            'checked_in_at' => now()->subHours(2),
            'check_in_method' => CheckInMethod::Counter,
        ]);
    }

    /**
     * State for cancelled appointment.
     */
    public function cancelled(?string $reason = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason ?? 'Pasien berhalangan hadir',
        ]);
    }

    /**
     * State for no-show appointment.
     */
    public function noShow(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::NoShow,
        ]);
    }

    /**
     * Set appointment date to today.
     */
    public function today(): static
    {
        $today = date('Y-m-d');
        return $this->state(fn (array $attributes) => [
            'appointment_date' => $today,
            'booking_code' => 'APT-' . date('Ymd') . '-' . fake()->unique()->numerify('####'),
        ]);
    }
}
