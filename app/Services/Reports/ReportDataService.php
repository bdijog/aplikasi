<?php

namespace App\Services\Reports;

use App\Enums\AppointmentStatus;
use App\Enums\QueueTicketStatus;
use App\Enums\ScheduleStatus;
use App\Enums\VisitType;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\QueueTicket;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReportDataService
{
    /**
     * Mengambil data Rekap Kunjungan (Harian / Rentang Tanggal).
     *
     * @param  array{start_date?: ?string, end_date?: ?string, doctor_id?: ?int, status?: ?string}  $filters
     * @return array<string, mixed>
     */
    public function getDailyVisitsData(array $filters = []): array
    {
        $startDate = ! empty($filters['start_date']) ? Carbon::parse($filters['start_date'])->startOfDay() : now()->startOfDay();
        $endDate = ! empty($filters['end_date']) ? Carbon::parse($filters['end_date'])->endOfDay() : now()->endOfDay();
        $doctorId = $filters['doctor_id'] ?? null;
        $status = $filters['status'] ?? null;

        $query = Appointment::query()
            ->with(['patient', 'doctor', 'schedule', 'queueTicket'])
            ->whereBetween('appointment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($doctorId, fn (Builder $q) => $q->where('doctor_id', $doctorId))
            ->when($status, fn (Builder $q) => $q->where('status', $status))
            ->orderBy('appointment_date')
            ->orderBy('estimated_time');

        $records = $query->get();

        $statusCounts = [
            'total' => $records->count(),
            'completed' => $records->where('status', AppointmentStatus::Completed)->count(),
            'checked_in' => $records->where('status', AppointmentStatus::CheckedIn)->count(),
            'in_progress' => $records->where('status', AppointmentStatus::InProgress)->count(),
            'confirmed' => $records->where('status', AppointmentStatus::Confirmed)->count(),
            'pending' => $records->where('status', AppointmentStatus::Pending)->count(),
            'cancelled' => $records->where('status', AppointmentStatus::Cancelled)->count(),
            'no_show' => $records->where('status', AppointmentStatus::NoShow)->count(),
        ];

        // Ringkasan per dokter
        $byDoctor = $records->groupBy('doctor_id')->map(function (Collection $items) {
            /** @var Appointment $first */
            $first = $items->first();
            $doc = $first->doctor;

            return [
                'doctor_name' => $doc?->name ?? 'Tidak Ditentukan',
                'specialty' => is_array($doc?->specialty) ? ($doc->specialty['id'] ?? reset($doc->specialty)) : ($doc?->specialty ?? '-'),
                'total' => $items->count(),
                'completed' => $items->where('status', AppointmentStatus::Completed)->count(),
                'cancelled' => $items->where('status', AppointmentStatus::Cancelled)->count(),
                'no_show' => $items->where('status', AppointmentStatus::NoShow)->count(),
            ];
        })->values()->toArray();

        // Ringkasan per tipe kunjungan
        $visitTypes = [
            'new_visit' => $records->where('visit_type', VisitType::NewVisit)->count(),
            'follow_up' => $records->where('visit_type', VisitType::FollowUp)->count(),
        ];

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'formatted' => $startDate->isSameDay($endDate)
                    ? $startDate->translatedFormat('d F Y')
                    : $startDate->translatedFormat('d F Y').' - '.$endDate->translatedFormat('d F Y'),
            ],
            'selected_doctor' => $doctorId ? Doctor::find($doctorId)?->name : null,
            'summary' => $statusCounts,
            'visit_types' => $visitTypes,
            'by_doctor' => $byDoctor,
            'records' => $records,
        ];
    }

    /**
     * Mengambil data Statistik Kunjungan Bulanan.
     *
     * @return array<string, mixed>
     */
    public function getMonthlyStatsData(int $year, int $month, ?int $doctorId = null): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = Appointment::query()
            ->with(['doctor', 'patient'])
            ->whereBetween('appointment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($doctorId, fn (Builder $q) => $q->where('doctor_id', $doctorId));

        $records = $query->get();
        $total = $records->count();

        $newVisits = $records->where('visit_type', VisitType::NewVisit)->count();
        $followUps = $records->where('visit_type', VisitType::FollowUp)->count();
        $newRatio = $total > 0 ? round(($newVisits / $total) * 100, 1) : 0;
        $followUpRatio = $total > 0 ? round(($followUps / $total) * 100, 1) : 0;

        // Breakdown per spesialisasi
        $bySpecialty = $records->groupBy(function (Appointment $item) {
            $doc = $item->doctor;
            if (! $doc) {
                return 'Lainnya';
            }

            return is_array($doc->specialty) ? ($doc->specialty['id'] ?? reset($doc->specialty)) : ($doc->specialty ?? 'Umum');
        })->map(function (Collection $items, string $spec) use ($total) {
            return [
                'specialty' => $spec,
                'total' => $items->count(),
                'percentage' => $total > 0 ? round(($items->count() / $total) * 100, 1) : 0,
                'completed' => $items->where('status', AppointmentStatus::Completed)->count(),
            ];
        })->values()->sortByDesc('total')->values()->toArray();

        // Breakdown per dokter
        $byDoctor = $records->groupBy('doctor_id')->map(function (Collection $items) use ($total) {
            /** @var Appointment $first */
            $first = $items->first();
            $doc = $first->doctor;

            return [
                'doctor_id' => $doc?->id,
                'doctor_name' => $doc?->name ?? 'Tidak Ditentukan',
                'specialty' => is_array($doc?->specialty) ? ($doc->specialty['id'] ?? reset($doc->specialty)) : ($doc?->specialty ?? '-'),
                'total' => $items->count(),
                'percentage' => $total > 0 ? round(($items->count() / $total) * 100, 1) : 0,
                'new_visits' => $items->where('visit_type', VisitType::NewVisit)->count(),
                'follow_ups' => $items->where('visit_type', VisitType::FollowUp)->count(),
                'completed' => $items->where('status', AppointmentStatus::Completed)->count(),
                'no_show' => $items->where('status', AppointmentStatus::NoShow)->count(),
            ];
        })->values()->sortByDesc('total')->values()->toArray();

        // Breakdown harian dalam 1 bulan
        $dailyBreakdown = [];
        $daysInMonth = $startDate->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currentDate = Carbon::createFromDate($year, $month, $d)->format('Y-m-d');
            $dayRecords = $records->filter(fn (Appointment $item) => $item->appointment_date?->format('Y-m-d') === $currentDate);

            $dailyBreakdown[] = [
                'day' => $d,
                'date' => $currentDate,
                'formatted' => Carbon::parse($currentDate)->translatedFormat('d M'),
                'total' => $dayRecords->count(),
                'new_visits' => $dayRecords->where('visit_type', VisitType::NewVisit)->count(),
                'follow_ups' => $dayRecords->where('visit_type', VisitType::FollowUp)->count(),
                'completed' => $dayRecords->where('status', AppointmentStatus::Completed)->count(),
            ];
        }

        return [
            'year' => $year,
            'month' => $month,
            'month_name' => $startDate->translatedFormat('F Y'),
            'total_visits' => $total,
            'completed_visits' => $records->where('status', AppointmentStatus::Completed)->count(),
            'new_visits' => $newVisits,
            'follow_ups' => $followUps,
            'new_ratio' => $newRatio,
            'follow_up_ratio' => $followUpRatio,
            'by_specialty' => $bySpecialty,
            'by_doctor' => $byDoctor,
            'daily_breakdown' => $dailyBreakdown,
        ];
    }

    /**
     * Mengambil data Laporan Kinerja Dokter.
     *
     * @param  array{start_date?: ?string, end_date?: ?string, doctor_id?: ?int}  $filters
     * @return array<string, mixed>
     */
    public function getDoctorPerformanceData(array $filters = []): array
    {
        $startDate = ! empty($filters['start_date']) ? Carbon::parse($filters['start_date'])->startOfDay() : now()->startOfMonth();
        $endDate = ! empty($filters['end_date']) ? Carbon::parse($filters['end_date'])->endOfDay() : now()->endOfMonth();
        $doctorId = $filters['doctor_id'] ?? null;

        $doctorsQuery = Doctor::query()
            ->when($doctorId, fn (Builder $q) => $q->where('id', $doctorId))
            ->orderBy('name');

        $doctors = $doctorsQuery->get();

        $performance = $doctors->map(function (Doctor $doctor) use ($startDate, $endDate) {
            $appointments = Appointment::query()
                ->where('doctor_id', $doctor->id)
                ->whereBetween('appointment_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get();

            $tickets = QueueTicket::query()
                ->with('appointment')
                ->where('doctor_id', $doctor->id)
                ->whereBetween('queue_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get();

            $totalAppts = $appointments->count();
            $completedAppts = $appointments->where('status', AppointmentStatus::Completed)->count();
            $cancelledAppts = $appointments->where('status', AppointmentStatus::Cancelled)->count();
            $noShowAppts = $appointments->where('status', AppointmentStatus::NoShow)->count();
            $noShowRate = $totalAppts > 0 ? round(($noShowAppts / $totalAppts) * 100, 1) : 0;

            // Durasi konsultasi (completed_at - served_at)
            $consultationMinutes = [];
            foreach ($tickets as $t) {
                if ($t->completed_at && $t->served_at && $t->completed_at->greaterThanOrEqualTo($t->served_at)) {
                    $consultationMinutes[] = $t->served_at->diffInMinutes($t->completed_at);
                }
            }
            $avgConsult = count($consultationMinutes) > 0 ? round(array_sum($consultationMinutes) / count($consultationMinutes), 1) : 0;
            $totalConsult = array_sum($consultationMinutes);

            // Waktu tunggu pasien dokter ini (called_at - checked_in_at / created_at)
            $waitMinutes = [];
            foreach ($tickets as $t) {
                $checkIn = $t->appointment?->checked_in_at ?? $t->created_at;
                if ($checkIn && $t->called_at && $t->called_at->greaterThanOrEqualTo($checkIn)) {
                    $waitMinutes[] = $checkIn->diffInMinutes($t->called_at);
                }
            }
            $avgWait = count($waitMinutes) > 0 ? round(array_sum($waitMinutes) / count($waitMinutes), 1) : 0;

            return [
                'doctor_id' => $doctor->id,
                'name' => $doctor->name,
                'license_number' => $doctor->license_number,
                'specialty' => is_array($doctor->specialty) ? ($doctor->specialty['id'] ?? reset($doctor->specialty)) : ($doctor->specialty ?? '-'),
                'total_appointments' => $totalAppts,
                'completed' => $completedAppts,
                'cancelled' => $cancelledAppts,
                'no_show' => $noShowAppts,
                'no_show_rate' => $noShowRate,
                'avg_consult_minutes' => $avgConsult,
                'total_consult_minutes' => $totalConsult,
                'avg_wait_minutes' => $avgWait,
                'total_tickets' => $tickets->count(),
            ];
        })->sortByDesc('completed')->values()->toArray();

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'formatted' => $startDate->translatedFormat('d F Y').' - '.$endDate->translatedFormat('d F Y'),
            ],
            'selected_doctor' => $doctorId ? Doctor::find($doctorId)?->name : null,
            'summary' => [
                'total_doctors' => count($performance),
                'total_patients' => array_sum(array_column($performance, 'total_appointments')),
                'total_completed' => array_sum(array_column($performance, 'completed')),
                'avg_consult_overall' => count($performance) > 0 ? round(array_sum(array_column($performance, 'avg_consult_minutes')) / count($performance), 1) : 0,
                'avg_wait_overall' => count($performance) > 0 ? round(array_sum(array_column($performance, 'avg_wait_minutes')) / count($performance), 1) : 0,
            ],
            'doctors' => $performance,
        ];
    }

    /**
     * Mengambil data Laporan Antrian & Waktu Tunggu.
     *
     * @param  array{start_date?: ?string, end_date?: ?string, doctor_id?: ?int}  $filters
     * @return array<string, mixed>
     */
    public function getQueueWaitTimeData(array $filters = []): array
    {
        $startDate = ! empty($filters['start_date']) ? Carbon::parse($filters['start_date'])->startOfDay() : now()->startOfWeek();
        $endDate = ! empty($filters['end_date']) ? Carbon::parse($filters['end_date'])->endOfDay() : now()->endOfWeek();
        $doctorId = $filters['doctor_id'] ?? null;

        $tickets = QueueTicket::query()
            ->with(['doctor', 'appointment.patient', 'schedule'])
            ->whereBetween('queue_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($doctorId, fn (Builder $q) => $q->where('doctor_id', $doctorId))
            ->orderBy('queue_date')
            ->orderBy('queue_number')
            ->get();

        $waitMinutes = [];
        $serveMinutes = [];

        foreach ($tickets as $t) {
            $checkIn = $t->appointment?->checked_in_at ?? $t->created_at;
            if ($checkIn && $t->called_at && $t->called_at->greaterThanOrEqualTo($checkIn)) {
                $waitMinutes[] = $checkIn->diffInMinutes($t->called_at);
            }

            if ($t->completed_at && $t->served_at && $t->completed_at->greaterThanOrEqualTo($t->served_at)) {
                $serveMinutes[] = $t->served_at->diffInMinutes($t->completed_at);
            }
        }

        $avgWait = count($waitMinutes) > 0 ? round(array_sum($waitMinutes) / count($waitMinutes), 1) : 0;
        $maxWait = count($waitMinutes) > 0 ? max($waitMinutes) : 0;
        $avgServe = count($serveMinutes) > 0 ? round(array_sum($serveMinutes) / count($serveMinutes), 1) : 0;

        // Statistik per hari
        $byDay = $tickets->groupBy(fn (QueueTicket $t) => $t->queue_date?->format('Y-m-d'))->map(function (Collection $dayTickets, string $date) {
            $dayWaits = [];
            $dayServes = [];

            foreach ($dayTickets as $t) {
                $checkIn = $t->appointment?->checked_in_at ?? $t->created_at;
                if ($checkIn && $t->called_at && $t->called_at->greaterThanOrEqualTo($checkIn)) {
                    $dayWaits[] = $checkIn->diffInMinutes($t->called_at);
                }
                if ($t->completed_at && $t->served_at && $t->completed_at->greaterThanOrEqualTo($t->served_at)) {
                    $dayServes[] = $t->served_at->diffInMinutes($t->completed_at);
                }
            }

            return [
                'date' => $date,
                'formatted_date' => Carbon::parse($date)->translatedFormat('l, d M Y'),
                'total' => $dayTickets->count(),
                'completed' => $dayTickets->where('status', QueueTicketStatus::Completed)->count(),
                'skipped' => $dayTickets->where('status', QueueTicketStatus::Skipped)->count(),
                'avg_wait_minutes' => count($dayWaits) > 0 ? round(array_sum($dayWaits) / count($dayWaits), 1) : 0,
                'avg_serve_minutes' => count($dayServes) > 0 ? round(array_sum($dayServes) / count($dayServes), 1) : 0,
            ];
        })->values()->toArray();

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'formatted' => $startDate->translatedFormat('d F Y').' - '.$endDate->translatedFormat('d F Y'),
            ],
            'selected_doctor' => $doctorId ? Doctor::find($doctorId)?->name : null,
            'summary' => [
                'total_tickets' => $tickets->count(),
                'completed_tickets' => $tickets->where('status', QueueTicketStatus::Completed)->count(),
                'skipped_tickets' => $tickets->where('status', QueueTicketStatus::Skipped)->count(),
                'avg_wait_minutes' => $avgWait,
                'max_wait_minutes' => $maxWait,
                'avg_serve_minutes' => $avgServe,
            ],
            'by_day' => $byDay,
            'records' => $tickets,
        ];
    }

    /**
     * Mengambil data Roster Jadwal Praktik Dokter.
     *
     * @param  array{doctor_id?: ?int, status?: ?string, type?: ?string}  $filters
     * @return array<string, mixed>
     */
    public function getDoctorSchedulesData(array $filters = []): array
    {
        $doctorId = $filters['doctor_id'] ?? null;
        $status = $filters['status'] ?? null;
        $type = $filters['type'] ?? null;

        $records = Schedule::query()
            ->with('doctor')
            ->when($doctorId, fn (Builder $q) => $q->where('doctor_id', $doctorId))
            ->when($status, fn (Builder $q) => $q->where('status', $status))
            ->when($type, fn (Builder $q) => $q->where('type', $type))
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $dayNames = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        return [
            'summary' => [
                'total_schedules' => $records->count(),
                'active_schedules' => $records->where('status', ScheduleStatus::Active)->count(),
                'total_doctors' => $records->pluck('doctor_id')->unique()->count(),
                'total_capacity' => $records->sum('max_patients'),
            ],
            'selected_doctor' => $doctorId ? Doctor::find($doctorId)?->name : null,
            'day_names' => $dayNames,
            'records' => $records,
        ];
    }
}
