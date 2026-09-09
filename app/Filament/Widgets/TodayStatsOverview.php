<?php

namespace App\Filament\Widgets;

use App\Enums\AppointmentStatus;
use App\Enums\QueueTicketStatus;
use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\QueueTicket;
use App\Models\Schedule;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TodayStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '15s';

    protected ?string $heading = 'Ringkasan Hari Ini';

    protected ?string $description = 'Pantauan operasional janji temu, antrean poliklinik, dan dokter aktif hari ini.';

    /**
     * @var int | array<string, ?int> | null
     */
    protected int | array | null $columns = [
        'sm' => 2,
        'md' => 3,
        'xl' => 5,
    ];

    protected function getStats(): array
    {
        $today = now()->format('Y-m-d');

        // 7 days window for sparkline trends
        $startDate = now()->subDays(6)->format('Y-m-d');
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $days[] = now()->subDays($i)->format('Y-m-d');
        }

        // 1. Appointments Data
        $appts7Days = Appointment::whereBetween('appointment_date', [$startDate, $today])
            ->selectRaw('appointment_date, status, count(*) as total')
            ->groupBy('appointment_date', 'status')
            ->get();

        $todayAppts = $appts7Days->filter(fn ($r) => $r->appointment_date?->format('Y-m-d') === $today);
        $totalTodayAppointments = (int) $todayAppts->sum('total');

        $pendingToday = (int) $todayAppts
            ->filter(fn ($row) => $row->status === AppointmentStatus::Pending || $row->status === AppointmentStatus::Pending->value)
            ->sum('total');

        $confirmedToday = (int) $todayAppts
            ->filter(fn ($row) => $row->status === AppointmentStatus::Confirmed || $row->status === AppointmentStatus::Confirmed->value)
            ->sum('total');

        $checkedInToday = (int) $todayAppts
            ->filter(fn ($row) => $row->status === AppointmentStatus::CheckedIn || $row->status === AppointmentStatus::CheckedIn->value)
            ->sum('total');

        $noShowToday = (int) $todayAppts
            ->filter(fn ($row) => $row->status === AppointmentStatus::NoShow || $row->status === AppointmentStatus::NoShow->value)
            ->sum('total');

        // 7-day trend arrays
        $appointmentTrend = [];
        $noShowTrend = [];
        foreach ($days as $date) {
            $dayData = $appts7Days->filter(fn ($r) => $r->appointment_date?->format('Y-m-d') === $date);
            $appointmentTrend[] = (int) $dayData->sum('total');
            $noShowTrend[] = (int) $dayData
                ->filter(fn ($row) => $row->status === AppointmentStatus::NoShow || $row->status === AppointmentStatus::NoShow->value)
                ->sum('total');
        }

        // 2. Queue Tickets Data
        $tickets7Days = QueueTicket::whereBetween('queue_date', [$startDate, $today])
            ->selectRaw('queue_date, status, count(*) as total')
            ->groupBy('queue_date', 'status')
            ->get();

        $todayTickets = $tickets7Days->filter(fn ($r) => $r->queue_date?->format('Y-m-d') === $today);

        $waitingToday = (int) $todayTickets
            ->filter(fn ($row) => $row->status === QueueTicketStatus::Waiting || $row->status === QueueTicketStatus::Waiting->value)
            ->sum('total');

        $servingToday = (int) $todayTickets
            ->filter(fn ($row) => $row->status === QueueTicketStatus::Serving || $row->status === QueueTicketStatus::Serving->value)
            ->sum('total');

        $completedToday = (int) $todayTickets
            ->filter(fn ($row) => $row->status === QueueTicketStatus::Completed || $row->status === QueueTicketStatus::Completed->value)
            ->sum('total');

        $waitingTrend = [];
        $completedTrend = [];
        foreach ($days as $date) {
            $dayData = $tickets7Days->filter(fn ($r) => $r->queue_date?->format('Y-m-d') === $date);
            $waitingTrend[] = (int) $dayData
                ->filter(fn ($row) => $row->status === QueueTicketStatus::Waiting || $row->status === QueueTicketStatus::Waiting->value)
                ->sum('total');
            $completedTrend[] = (int) $dayData
                ->filter(fn ($row) => $row->status === QueueTicketStatus::Completed || $row->status === QueueTicketStatus::Completed->value)
                ->sum('total');
        }

        // 3. Active Doctors Today
        $activeSchedules = Schedule::where('status', ScheduleStatus::Active)->get();
        $totalActiveDoctors = Doctor::where('is_active', true)->count();

        $doctorTrend = [];
        foreach ($days as $date) {
            $carbonDate = Carbon::parse($date);
            $dow = $carbonDate->dayOfWeek;

            $doctorsOnDay = $activeSchedules->filter(function (Schedule $schedule) use ($dow, $date) {
                if ($schedule->type === ScheduleType::Recurring && $schedule->day_of_week === $dow) {
                    return true;
                }
                if ($schedule->type === ScheduleType::OneTime && $schedule->specific_date?->format('Y-m-d') === $date) {
                    return true;
                }

                return false;
            })->pluck('doctor_id')->unique()->count();

            $doctorTrend[] = $doctorsOnDay;
        }

        $activeDoctorsToday = end($doctorTrend) ?: 0;

        // Dynamic Colors
        $waitingColor = match (true) {
            $waitingToday > 15 => 'danger',
            $waitingToday > 5 => 'warning',
            default => 'success',
        };

        $noShowColor = match (true) {
            $noShowToday > 3 => 'danger',
            $noShowToday > 0 => 'warning',
            default => 'success',
        };

        return [
            Stat::make('Total Janji Temu Hari Ini', (string) $totalTodayAppointments)
                ->description("Pending: {$pendingToday} • Konfirmasi: {$confirmedToday} • Check-in: {$checkedInToday}")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart($appointmentTrend)
                ->color('primary'),

            Stat::make('Antrean Aktif Sekarang', (string) $waitingToday)
                ->description("{$waitingToday} menunggu • {$servingToday} sedang dilayani")
                ->descriptionIcon('heroicon-m-ticket')
                ->chart($waitingTrend)
                ->color($waitingColor),

            Stat::make('Pasien Sudah Dilayani', (string) $completedToday)
                ->description('Konsultasi dokter selesai hari ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart($completedTrend)
                ->color('success'),

            Stat::make('No-Show Hari Ini', (string) $noShowToday)
                ->description($noShowToday > 0 ? 'Pasien tidak hadir sesuai jadwal' : 'Tidak ada pasien no-show')
                ->descriptionIcon('heroicon-m-user-minus')
                ->chart($noShowTrend)
                ->color($noShowColor),

            Stat::make('Dokter Aktif Hari Ini', (string) $activeDoctorsToday)
                ->description("Dari {$totalActiveDoctors} dokter aktif terdaftar")
                ->descriptionIcon('heroicon-m-user-group')
                ->chart($doctorTrend)
                ->color('info'),
        ];
    }
}
