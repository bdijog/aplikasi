<?php

namespace App\Livewire\Frontend;

use App\Enums\ScheduleStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Schedule;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.frontend')]
class DoctorSchedule extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $specialty = 'all';

    #[Url]
    public ?int $day = null;

    #[Url]
    public string $date = '';

    public ?int $selectedDoctorId = null;

    public function mount(): void
    {
        if (empty($this->date)) {
            $this->date = now()->format('Y-m-d');
        }
    }

    public function selectDay(?int $day): void
    {
        $this->day = $day;
    }

    public function selectSpecialty(string $specialty): void
    {
        $this->specialty = $specialty;
    }

    public function showDoctorDetail(int $doctorId): void
    {
        $this->selectedDoctorId = $doctorId;
    }

    public function closeDoctorDetail(): void
    {
        $this->selectedDoctorId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->specialty = 'all';
        $this->day = null;
        $this->date = now()->format('Y-m-d');
    }

    public function render()
    {
        $locale = app()->getLocale();
        $targetDate = ! empty($this->date) ? Carbon::parse($this->date) : now();
        $targetDayOfWeek = $this->day ?? $targetDate->isoFormat('E'); // 1 = Monday, 7 = Sunday

        $query = Doctor::query()
            ->where('is_active', true)
            ->with(['schedules' => function ($q) {
                $q->where('status', ScheduleStatus::Active)
                    ->orderBy('day_of_week')
                    ->orderBy('start_time');
            }]);

        if (! empty($this->search)) {
            $search = '%'.$this->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('license_number', 'like', $search)
                    ->orWhere('specialty->id', 'like', $search)
                    ->orWhere('specialty->en', 'like', $search);
            });
        }

        if ($this->specialty !== 'all') {
            $specSearch = '%'.$this->specialty.'%';
            $query->where(function ($q) use ($specSearch) {
                $q->where('specialty->id', 'like', $specSearch)
                    ->orWhere('specialty->en', 'like', $specSearch);
            });
        }

        if ($this->day !== null) {
            $query->whereHas('schedules', function ($q) {
                $q->where('day_of_week', $this->day)
                    ->where('status', ScheduleStatus::Active);
            });
        }

        $doctors = $query->get()->map(function (Doctor $doctor) use ($targetDayOfWeek, $targetDate) {
            // Find schedule for active/target day
            $activeSchedule = $doctor->schedules->firstWhere('day_of_week', (int) $targetDayOfWeek);

            $bookedCount = 0;
            $quotaRemaining = 20;
            $maxQuota = 20;
            $status = 'available'; // available, limited, full

            if ($activeSchedule) {
                $maxQuota = $activeSchedule->max_patients ?: 20;
                $bookedCount = Appointment::where('doctor_id', $doctor->id)
                    ->where('appointment_date', $targetDate->format('Y-m-d'))
                    ->whereNotIn('status', ['cancelled'])
                    ->count();

                $quotaRemaining = max(0, $maxQuota - $bookedCount);

                if ($quotaRemaining === 0) {
                    $status = 'full';
                } elseif ($quotaRemaining <= 5) {
                    $status = 'limited';
                } else {
                    $status = 'available';
                }
            }

            return [
                'doctor' => $doctor,
                'activeSchedule' => $activeSchedule,
                'scheduledDays' => $doctor->schedules->pluck('day_of_week')->unique()->all(),
                'maxQuota' => $maxQuota,
                'quotaRemaining' => $quotaRemaining,
                'bookedCount' => $bookedCount,
                'status' => $status,
            ];
        });

        // Statistics
        $totalDoctors = Doctor::where('is_active', true)->count();
        $totalSpecialties = 6;
        $totalQuotaToday = $doctors->sum('quotaRemaining');

        $selectedDoctor = $this->selectedDoctorId ? Doctor::with(['schedules' => function ($q) {
            $q->where('status', ScheduleStatus::Active)->orderBy('day_of_week')->orderBy('start_time');
        }])->find($this->selectedDoctorId) : null;

        // Specialties list for buttons
        $specialtiesList = [
            ['id' => 'Penyakit Dalam', 'name' => __('Internal Medicine Specialist'), 'icon' => 'cardiology', 'count' => 1],
            ['id' => 'Anak', 'name' => __('Pediatrician (Children Specialist)'), 'icon' => 'child_care', 'count' => 1],
            ['id' => 'Obstetri', 'name' => __('Obstetrics & Gynecology (Obgyn)'), 'icon' => 'pregnant_woman', 'count' => 1],
            ['id' => 'Jantung', 'name' => __('Cardiology & Vascular'), 'icon' => 'monitor_heart', 'count' => 2],
            ['id' => 'Mata', 'name' => __('Ophthalmologist (Eye Specialist)'), 'icon' => 'visibility', 'count' => 2],
            ['id' => 'Umum', 'name' => __('General Practitioner'), 'icon' => 'medical_services', 'count' => 2],
        ];

        return view('livewire.frontend.doctor-schedule', [
            'doctors' => $doctors,
            'totalDoctors' => $totalDoctors,
            'totalSpecialties' => $totalSpecialties,
            'totalQuotaToday' => $totalQuotaToday,
            'specialtiesList' => $specialtiesList,
            'selectedDoctor' => $selectedDoctor,
            'targetDayOfWeek' => $targetDayOfWeek,
        ]);
    }
}
