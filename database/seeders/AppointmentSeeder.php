<?php

namespace Database\Seeders;

use App\Enums\AppointmentSource;
use App\Enums\AppointmentStatus;
use App\Enums\CheckInMethod;
use App\Enums\VisitType;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = Doctor::with('schedules')->get();

        if ($patients->isEmpty()) {
            $this->call(PatientSeeder::class);
            $patients = Patient::all();
        }

        if ($doctors->isEmpty()) {
            $this->call(DoctorSeeder::class);
            $doctors = Doctor::with('schedules')->get();
        }

        $schedules = Schedule::all();
        if ($schedules->isEmpty()) {
            $this->call(ScheduleSeeder::class);
            $doctors = Doctor::with('schedules')->get();
        }

        $today = date('Y-m-d');

        // Create appointments for each doctor across different dates
        foreach ($doctors as $doctor) {
            $doctorSchedules = $doctor->schedules;
            if ($doctorSchedules->isEmpty()) {
                continue;
            }

            // 1. Past completed appointment
            $patient1 = $patients->random();
            $schedule1 = $doctorSchedules->random();
            $pastDate = date('Y-m-d', strtotime('-3 days'));
            Appointment::updateOrCreate(
                ['booking_code' => 'APT-' . date('Ymd', strtotime($pastDate)) . '-' . str_pad($doctor->id, 2, '0', STR_PAD_LEFT) . '01'],
                [
                    'patient_id' => $patient1->id,
                    'doctor_id' => $doctor->id,
                    'schedule_id' => $schedule1->id,
                    'appointment_date' => $pastDate,
                    'estimated_time' => $schedule1->start_time,
                    'visit_type' => VisitType::NewVisit,
                    'chief_complaint' => 'Pemeriksaan keluhan rutin',
                    'status' => AppointmentStatus::Completed,
                    'source' => AppointmentSource::Online,
                    'checked_in_at' => date('Y-m-d H:i:s', strtotime("{$pastDate} {$schedule1->start_time} -15 minutes")),
                    'check_in_method' => CheckInMethod::SelfService,
                ]
            );

            // 2. Today's appointment - Checked in
            $patient2 = $patients->random();
            $schedule2 = $doctorSchedules->random();
            Appointment::updateOrCreate(
                ['booking_code' => 'APT-' . date('Ymd') . '-' . str_pad($doctor->id, 2, '0', STR_PAD_LEFT) . '02'],
                [
                    'patient_id' => $patient2->id,
                    'doctor_id' => $doctor->id,
                    'schedule_id' => $schedule2->id,
                    'appointment_date' => $today,
                    'estimated_time' => $schedule2->start_time,
                    'visit_type' => VisitType::NewVisit,
                    'chief_complaint' => 'Keluhan demam dan flu',
                    'status' => AppointmentStatus::CheckedIn,
                    'source' => AppointmentSource::Online,
                    'checked_in_at' => now()->subMinutes(10),
                    'check_in_method' => CheckInMethod::SelfService,
                ]
            );

            // 3. Upcoming confirmed appointment (H+2)
            $patient3 = $patients->random();
            $schedule3 = $doctorSchedules->random();
            $futureDate = date('Y-m-d', strtotime('+2 days'));
            Appointment::updateOrCreate(
                ['booking_code' => 'APT-' . date('Ymd', strtotime($futureDate)) . '-' . str_pad($doctor->id, 2, '0', STR_PAD_LEFT) . '03'],
                [
                    'patient_id' => $patient3->id,
                    'doctor_id' => $doctor->id,
                    'schedule_id' => $schedule3->id,
                    'appointment_date' => $futureDate,
                    'estimated_time' => $schedule3->start_time,
                    'visit_type' => VisitType::FollowUp,
                    'chief_complaint' => 'Kontrol lanjutan',
                    'status' => AppointmentStatus::Confirmed,
                    'source' => AppointmentSource::Online,
                ]
            );
        }
    }
}
