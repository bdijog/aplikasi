<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Schedule;
use App\Models\ServiceCounter;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AllFactoriesAndSeedersTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_factories_create_valid_models(): void
    {
        $user = User::factory()->create();
        $this->assertNotNull($user->id);

        $doctor = Doctor::factory()->create();
        $this->assertNotNull($doctor->id);
        $this->assertStringStartsWith('dr. ', $doctor->name);

        $patient = Patient::factory()->create();
        $this->assertNotNull($patient->id);
        $this->assertStringStartsWith('RM-', $patient->medical_record_number);

        $counter = ServiceCounter::factory()->create();
        $this->assertNotNull($counter->id);

        $schedule = Schedule::factory()->for($doctor)->create();
        $this->assertNotNull($schedule->id);
        $this->assertEquals($doctor->id, $schedule->doctor_id);

        $appointment = Appointment::factory()
            ->for($patient)
            ->for($doctor)
            ->for($schedule)
            ->create();
        $this->assertNotNull($appointment->id);
        $this->assertStringStartsWith('APT-', $appointment->booking_code);

        $ticket = QueueTicket::factory()
            ->for($appointment)
            ->for($doctor)
            ->for($schedule)
            ->create();
        $this->assertNotNull($ticket->id);
        $this->assertNotEmpty($ticket->display_number);
    }

    public function test_database_seeder_populates_all_tables(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(3, User::count());
        $this->assertGreaterThanOrEqual(8, ServiceCounter::count());
        $this->assertGreaterThanOrEqual(10, Doctor::count());
        $this->assertGreaterThanOrEqual(15, Patient::count());
        $this->assertGreaterThanOrEqual(10, Schedule::count());
        $this->assertGreaterThanOrEqual(10, Appointment::count());
        $this->assertGreaterThanOrEqual(5, QueueTicket::count());
    }
}
