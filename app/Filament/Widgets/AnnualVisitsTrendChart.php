<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\QueueTicket;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class AnnualVisitsTrendChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    public function getHeading(): string|Htmlable|null
    {
        return __('Annual Visits Trend');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('Growth of patient queue visits and appointment bookings in the last 12 months.');
    }

    /**
     * @var int | string | array<string, int | null>
     */
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $startOfMonthWindow = now()->subMonths(11)->startOfMonth();
        $endOfMonthWindow = now()->endOfMonth();

        $months = [];
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $months[] = $monthKey;
            $labels[] = $month->translatedFormat('M Y');
        }

        $tickets = QueueTicket::whereBetween('queue_date', [
            $startOfMonthWindow->format('Y-m-d'),
            $endOfMonthWindow->format('Y-m-d'),
        ])->get(['queue_date']);

        $appointments = Appointment::whereBetween('appointment_date', [
            $startOfMonthWindow->format('Y-m-d'),
            $endOfMonthWindow->format('Y-m-d'),
        ])->get(['appointment_date']);

        $queueData = [];
        $apptData = [];

        foreach ($months as $monthKey) {
            $queueCount = $tickets->filter(fn ($item) => $item->queue_date?->format('Y-m') === $monthKey)->count();
            $apptCount = $appointments->filter(fn ($item) => $item->appointment_date?->format('Y-m') === $monthKey)->count();

            $queueData[] = $queueCount;
            $apptData[] = $apptCount;
        }

        return [
            'datasets' => [
                [
                    'label' => __('Patient Visits (Queue)'),
                    'data' => $queueData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
                [
                    'label' => __('Registered Appointments'),
                    'data' => $apptData,
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
