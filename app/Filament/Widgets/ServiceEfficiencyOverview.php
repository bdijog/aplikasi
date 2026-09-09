<?php

namespace App\Filament\Widgets;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\QueueTicket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ServiceEfficiencyOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '60s';

    protected function getHeading(): ?string
    {
        return __('Service Efficiency');
    }

    protected function getDescription(): ?string
    {
        return __('Performance indicators for wait times, doctor consultation duration, and patient no-show ratio.');
    }

    /**
     * @var int | array<string, ?int> | null
     */
    protected int|array|null $columns = 3;

    protected function getStats(): array
    {
        return Cache::remember('filament_service_efficiency_overview_stats_'.app()->getLocale(), 60, function () {
            $today = now()->format('Y-m-d');
            $days = [];
            for ($i = 6; $i >= 0; $i--) {
                $days[] = now()->subDays($i)->format('Y-m-d');
            }
            $startDate = $days[0];

            // 1. Rata-rata waktu tunggu (called_at - checked_in_at / created_at)
            $ticketsWithCalls = QueueTicket::with('appointment')
                ->where('queue_date', '>=', $startDate)
                ->whereNotNull('called_at')
                ->get();

            $todayTicketsCalls = $ticketsWithCalls->filter(fn (QueueTicket $t) => $t->queue_date?->format('Y-m-d') === $today);

            // Calculate today's wait times
            $todayWaitMinutes = [];
            foreach ($todayTicketsCalls as $t) {
                $checkIn = $t->appointment?->checked_in_at ?? $t->created_at;
                if ($checkIn && $t->called_at && $t->called_at->greaterThanOrEqualTo($checkIn)) {
                    $todayWaitMinutes[] = $checkIn->diffInMinutes($t->called_at);
                }
            }

            $hasTodayCalls = count($todayWaitMinutes) > 0;
            $avgWaitToday = $hasTodayCalls ? round(array_sum($todayWaitMinutes) / count($todayWaitMinutes), 1) : null;

            // 7 days trend for wait times
            $waitTrend = [];
            $all7DaysWaitMinutes = [];
            foreach ($days as $date) {
                $dayTickets = $ticketsWithCalls->filter(fn (QueueTicket $t) => $t->queue_date?->format('Y-m-d') === $date);
                $dayWaits = [];
                foreach ($dayTickets as $t) {
                    $checkIn = $t->appointment?->checked_in_at ?? $t->created_at;
                    if ($checkIn && $t->called_at && $t->called_at->greaterThanOrEqualTo($checkIn)) {
                        $diff = $checkIn->diffInMinutes($t->called_at);
                        $dayWaits[] = $diff;
                        $all7DaysWaitMinutes[] = $diff;
                    }
                }
                $waitTrend[] = count($dayWaits) > 0 ? round(array_sum($dayWaits) / count($dayWaits), 1) : 0;
            }

            $avgWaitDisplay = $avgWaitToday ?? (count($all7DaysWaitMinutes) > 0 ? round(array_sum($all7DaysWaitMinutes) / count($all7DaysWaitMinutes), 1) : 0);
            $waitDescription = $hasTodayCalls
                ? __('Average queue wait time today')
                : (count($all7DaysWaitMinutes) > 0 ? __('Last 7 days average (no calls today yet)') : __('No queue call data yet'));

            $waitColor = match (true) {
                $avgWaitDisplay > 30 => 'danger',
                $avgWaitDisplay > 20 => 'warning',
                default => 'success',
            };

            // 2. Rata-rata durasi konsultasi (completed_at - served_at)
            $completedTickets = QueueTicket::where('queue_date', '>=', $startDate)
                ->whereNotNull('completed_at')
                ->whereNotNull('served_at')
                ->get();

            $todayConsults = $completedTickets->filter(fn (QueueTicket $t) => $t->queue_date?->format('Y-m-d') === $today);

            $todayConsultMinutes = [];
            foreach ($todayConsults as $t) {
                if ($t->completed_at && $t->served_at && $t->completed_at->greaterThanOrEqualTo($t->served_at)) {
                    $todayConsultMinutes[] = $t->served_at->diffInMinutes($t->completed_at);
                }
            }

            $hasTodayConsults = count($todayConsultMinutes) > 0;
            $avgConsultToday = $hasTodayConsults ? round(array_sum($todayConsultMinutes) / count($todayConsultMinutes), 1) : null;

            // 7 days trend for consult duration
            $consultTrend = [];
            $all7DaysConsultMinutes = [];
            foreach ($days as $date) {
                $dayTickets = $completedTickets->filter(fn (QueueTicket $t) => $t->queue_date?->format('Y-m-d') === $date);
                $dayConsults = [];
                foreach ($dayTickets as $t) {
                    if ($t->completed_at && $t->served_at && $t->completed_at->greaterThanOrEqualTo($t->served_at)) {
                        $diff = $t->served_at->diffInMinutes($t->completed_at);
                        $dayConsults[] = $diff;
                        $all7DaysConsultMinutes[] = $diff;
                    }
                }
                $consultTrend[] = count($dayConsults) > 0 ? round(array_sum($dayConsults) / count($dayConsults), 1) : 0;
            }

            $avgConsultDisplay = $avgConsultToday ?? (count($all7DaysConsultMinutes) > 0 ? round(array_sum($all7DaysConsultMinutes) / count($all7DaysConsultMinutes), 1) : 0);
            $consultDescription = $hasTodayConsults
                ? __('Doctor-patient consultation duration today')
                : (count($all7DaysConsultMinutes) > 0 ? __('Last 7 days average (none completed today yet)') : __('No completed consultation data yet'));

            $consultColor = match (true) {
                $avgConsultDisplay > 25 => 'warning',
                $avgConsultDisplay > 0 => 'success',
                default => 'info',
            };

            // 3. Tingkat no-show (%)
            $allAppts7Days = Appointment::whereBetween('appointment_date', [$startDate, $today])
                ->selectRaw('appointment_date, status, count(*) as total')
                ->groupBy('appointment_date', 'status')
                ->get();

            $todayAppts = $allAppts7Days->filter(fn ($r) => $r->appointment_date?->format('Y-m-d') === $today);
            $totalToday = (int) $todayAppts->sum('total');
            $noShowToday = (int) $todayAppts
                ->filter(fn ($row) => $row->status === AppointmentStatus::NoShow || $row->status === AppointmentStatus::NoShow->value)
                ->sum('total');

            $total7Days = (int) $allAppts7Days->sum('total');
            $noShow7Days = (int) $allAppts7Days
                ->filter(fn ($row) => $row->status === AppointmentStatus::NoShow || $row->status === AppointmentStatus::NoShow->value)
                ->sum('total');

            $hasTodayAppts = $totalToday > 0;
            $rateToday = $hasTodayAppts ? round(($noShowToday / $totalToday) * 100, 1) : 0;
            $rate7Days = $total7Days > 0 ? round(($noShow7Days / $total7Days) * 100, 1) : 0;

            $rateDisplay = $hasTodayAppts ? $rateToday : $rate7Days;

            // 7 days trend for no-show %
            $noShowRateTrend = [];
            foreach ($days as $date) {
                $dayData = $allAppts7Days->filter(fn ($r) => $r->appointment_date?->format('Y-m-d') === $date);
                $dayTotal = (int) $dayData->sum('total');
                $dayNoShow = (int) $dayData
                    ->filter(fn ($row) => $row->status === AppointmentStatus::NoShow || $row->status === AppointmentStatus::NoShow->value)
                    ->sum('total');
                $noShowRateTrend[] = $dayTotal > 0 ? round(($dayNoShow / $dayTotal) * 100, 1) : 0;
            }

            $noShowDescription = $hasTodayAppts
                ? __(':count of :total appointments today (7 Days: :rate%)', [
                    'count' => $noShowToday,
                    'total' => $totalToday,
                    'rate' => $rate7Days,
                ])
                : __('Last 7 days: :count of :total appointments', [
                    'count' => $noShow7Days,
                    'total' => $total7Days,
                ]);

            $noShowRateColor = match (true) {
                $rateDisplay > 15 => 'danger',
                $rateDisplay > 5 => 'warning',
                default => 'success',
            };

            return [
                Stat::make(__('Average Wait Time'), $avgWaitDisplay.' '.__('Minutes'))
                    ->description($waitDescription)
                    ->descriptionIcon('heroicon-m-clock')
                    ->chart($waitTrend)
                    ->color($waitColor),

                Stat::make(__('Average Consultation Duration'), $avgConsultDisplay.' '.__('Minutes'))
                    ->description($consultDescription)
                    ->descriptionIcon('heroicon-m-user-group')
                    ->chart($consultTrend)
                    ->color($consultColor),

                Stat::make(__('No-Show Rate'), $rateDisplay.'%')
                    ->description($noShowDescription)
                    ->descriptionIcon('heroicon-m-user-minus')
                    ->chart($noShowRateTrend)
                    ->color($noShowRateColor),
            ];
        });
    }
}
