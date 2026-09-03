<?php

namespace Tests\Feature;

use App\Enums\AppointmentSource;
use App\Enums\AppointmentStatus;
use App\Enums\CheckInMethod;
use App\Enums\Gender;
use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Enums\VisitType;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Schedule;
use App\Models\ServiceCounter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DoctorScheduleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_create_doctor_and_schedules(): void
    {
        $doctor = Doctor::create([
            'name' => 'dr. Andi Pratama, Sp.A',
            'license_number' => 'STR-TEST-1234567890',
            'email' => 'andi.unique@klinik.test',
            'password' => 'secret123',
            'phone' => '081234567890',
            'is_active' => true,
            'specialty' => 'Spesialis Anak',
        ]);

        $this->assertDatabaseHas('doctors', [
            'email' => 'andi.unique@klinik.test',
            'license_number' => 'STR-TEST-1234567890',
            'specialty' => 'Spesialis Anak',
        ]);

        $schedule = $doctor->schedules()->create([
            'day_of_week' => 1, // Senin
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'max_patients' => 20,
            'status' => ScheduleStatus::Active,
            'type' => ScheduleType::Recurring,
        ]);

        $this->assertEquals('Senin', $schedule->day_name);
        $this->assertEquals(ScheduleStatus::Active, $schedule->status);
        $this->assertEquals(ScheduleType::Recurring, $schedule->type);
        $this->assertEquals($doctor->id, $schedule->doctor->id);
    }

    public function test_can_create_patient_and_appointment_and_queue(): void
    {
        $doctor = Doctor::create([
            'name' => 'dr. Budi Santoso, Sp.PD',
            'license_number' => 'STR-TEST-9876543210',
            'email' => 'budi.unique@klinik.test',
            'password' => 'secret123',
            'specialty' => 'Penyakit Dalam',
        ]);

        $schedule = $doctor->schedules()->create([
            'day_of_week' => 2, // Selasa
            'start_time' => '09:00:00',
            'end_time' => '13:00:00',
            'max_patients' => 15,
            'status' => ScheduleStatus::Active,
            'type' => ScheduleType::Recurring,
        ]);

        $patient = Patient::create([
            'medical_record_number' => 'RM-TEST-UNIQUE-0001',
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad.unique@patient.test',
            'password' => 'patient123',
            'date_of_birth' => '1990-05-15',
            'gender' => Gender::Male,
            'national_id' => '9901011505900001',
            'phone' => '082198765432',
            'blood_type' => 'O',
            'allergies' => ['Paracetamol', 'Penicillin'],
        ]);

        $this->assertDatabaseHas('patients', [
            'medical_record_number' => 'RM-TEST-UNIQUE-0001',
            'gender' => 'male',
        ]);
        $this->assertContains('Paracetamol', $patient->fresh()->allergies);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_code' => 'APT-TEST-UNIQUE-0001',
            'appointment_date' => '2026-09-08',
            'visit_type' => VisitType::NewVisit,
            'chief_complaint' => 'Demam tinggi selama 3 hari',
            'status' => AppointmentStatus::Confirmed,
            'source' => AppointmentSource::Online,
        ]);

        $this->assertEquals(AppointmentStatus::Confirmed, $appointment->status);
        $this->assertEquals($doctor->id, $appointment->doctor->id);
        $this->assertEquals($patient->id, $appointment->patient->id);
        $this->assertEquals($schedule->id, $appointment->schedule->id);

        // Check-in patient and create queue ticket
        $appointment->update([
            'status' => AppointmentStatus::CheckedIn,
            'checked_in_at' => now(),
            'check_in_method' => CheckInMethod::SelfService,
        ]);

        $queue = QueueTicket::create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'queue_date' => '2026-09-08',
            'queue_number' => 1,
            'prefix' => 'A',
            'display_number' => 'A-001',
            'status' => QueueTicketStatus::Waiting,
            'priority' => QueueTicketPriority::Normal,
        ]);

        $this->assertEquals('A-001', $queue->display_number);
        $this->assertEquals(QueueTicketStatus::Waiting, $queue->status);
        $this->assertEquals($appointment->id, $queue->appointment->id);
        $this->assertEquals($queue->id, $appointment->fresh()->queueTicket->id);
        $this->assertEquals(1, $doctor->queueTickets()->count());
    }

    public function test_can_create_service_counter(): void
    {
        $counter = ServiceCounter::create([
            'name' => 'Poli Anak 1',
            'code' => 'TEST-POLI-A-01',
            'location' => 'Lantai 2 Gedung B',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('service_counters', [
            'code' => 'TEST-POLI-A-01',
            'is_active' => true,
        ]);
        $this->assertTrue($counter->is_active);
    }
}
