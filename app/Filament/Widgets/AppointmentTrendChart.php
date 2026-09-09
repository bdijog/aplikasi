<?php

namespace App\Filament\Widgets;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class AppointmentTrendChart extends ChartWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '30s';

    public function getHeading(): string|Htmlable|null
    {
        return __('Appointment Trend Last 7 Days');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('Daily appointment booking counts over the last 7 days.');
    }

    /**
     * @var int | string | array<string, int | null>
     */
    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected function getData(): array
    {
        $today = now()->startOfDay();
        $startDate = now()->subDays(6)->startOfDay();

        $days = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $days[] = $dateKey;
            $labels[] = $date->translatedFormat('D, d M');
        }

        $appointments = Appointment::whereBetween('appointment_date', [$startDate->format('Y-m-d'), $today->format('Y-m-d')])
            ->get(['appointment_date', 'status']);

        $totalData = [];
        $completedData = [];

        foreach ($days as $dateKey) {
            $dayAppts = $appointments->filter(fn ($item) => $item->appointment_date?->format('Y-m-d') === $dateKey);
            $totalData[] = $dayAppts->count();

            $completedData[] = $dayAppts->filter(fn ($item) => in_array($item->status, [
                AppointmentStatus::CheckedIn,
                AppointmentStatus::CheckedIn->value,
                AppointmentStatus::Completed,
                AppointmentStatus::Completed->value,
                AppointmentStatus::InProgress,
                AppointmentStatus::InProgress->value,
            ], true))->count();
        }

        return [
            'datasets' => [
                [
                    'label' => __('Total Appointments'),
                    'data' => $totalData,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
                [
                    'label' => __('Attended / Completed'),
                    'data' => $completedData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
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
