<?php

namespace Database\Seeders;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = Doctor::all();

        if ($doctors->isEmpty()) {
            $this->call(DoctorSeeder::class);
            $doctors = Doctor::all();
        }

        // Standard shift templates
        $shiftTemplates = [
            ['day' => 1, 'start' => '08:00:00', 'end' => '12:00:00', 'notes' => 'Poli Pagi (Senin)'],
            ['day' => 3, 'start' => '08:00:00', 'end' => '12:00:00', 'notes' => 'Poli Pagi (Rabu)'],
            ['day' => 5, 'start' => '08:00:00', 'end' => '12:00:00', 'notes' => 'Poli Pagi (Jumat)'],
            ['day' => 2, 'start' => '13:00:00', 'end' => '17:00:00', 'notes' => 'Poli Siang (Selasa)'],
            ['day' => 4, 'start' => '13:00:00', 'end' => '17:00:00', 'notes' => 'Poli Siang (Kamis)'],
            ['day' => 6, 'start' => '09:00:00', 'end' => '13:00:00', 'notes' => 'Poli Akhir Pekan (Sabtu)'],
        ];

        foreach ($doctors as $index => $doctor) {
            // Assign 2 distinct shifts per doctor
            $firstShiftIndex = $index % 3;
            $secondShiftIndex = ($index % 3) + 3;

            $assignedShifts = [
                $shiftTemplates[$firstShiftIndex],
                $shiftTemplates[$secondShiftIndex],
            ];

            foreach ($assignedShifts as $shift) {
                Schedule::updateOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $shift['day'],
                        'start_time' => $shift['start'],
                    ],
                    [
                        'end_time' => $shift['end'],
                        'max_patients' => 20,
                        'status' => ScheduleStatus::Active,
                        'notes' => $shift['notes'],
                        'type' => ScheduleType::Recurring,
                    ]
                );
            }
        }
    }
}
