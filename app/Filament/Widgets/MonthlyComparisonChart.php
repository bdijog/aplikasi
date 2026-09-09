<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\QueueTicket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MonthlyComparisonChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '60s';

    protected ?string $heading = 'Perbandingan Bulanan';

    protected ?string $description = 'Komparasi performa mingguan: Bulan Ini vs Bulan Lalu.';

    /**
     * @var int | string | array<string, int | null>
     */
    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    /**
     * @return array<string, string> | null
     */
    protected function getFilters(): ?array
    {
        return [
            'appointments' => 'Janji Temu',
            'queue' => 'Kunjungan Antrean',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? 'appointments';

        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();

        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        if ($activeFilter === 'queue') {
            $thisMonthData = QueueTicket::whereBetween('queue_date', [
                $thisMonthStart->format('Y-m-d'),
                $thisMonthEnd->format('Y-m-d'),
            ])->get(['queue_date']);

            $lastMonthData = QueueTicket::whereBetween('queue_date', [
                $lastMonthStart->format('Y-m-d'),
                $lastMonthEnd->format('Y-m-d'),
            ])->get(['queue_date']);

            $thisCounts = $this->calculateWeeklyCounts($thisMonthData, 'queue_date');
            $lastCounts = $this->calculateWeeklyCounts($lastMonthData, 'queue_date');
        } else {
            $thisMonthData = Appointment::whereBetween('appointment_date', [
                $thisMonthStart->format('Y-m-d'),
                $thisMonthEnd->format('Y-m-d'),
            ])->get(['appointment_date']);

            $lastMonthData = Appointment::whereBetween('appointment_date', [
                $lastMonthStart->format('Y-m-d'),
                $lastMonthEnd->format('Y-m-d'),
            ])->get(['appointment_date']);

            $thisCounts = $this->calculateWeeklyCounts($thisMonthData, 'appointment_date');
            $lastCounts = $this->calculateWeeklyCounts($lastMonthData, 'appointment_date');
        }

        $thisMonthLabel = 'Bulan Ini ('.now()->translatedFormat('M Y').')';
        $lastMonthLabel = 'Bulan Lalu ('.now()->subMonth()->translatedFormat('M Y').')';

        return [
            'datasets' => [
                [
                    'label' => $thisMonthLabel,
                    'data' => $thisCounts,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
                [
                    'label' => $lastMonthLabel,
                    'data' => $lastCounts,
                    'backgroundColor' => '#94a3b8',
                    'borderColor' => '#64748b',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => [
                'Minggu 1 (Tgl 1-7)',
                'Minggu 2 (Tgl 8-14)',
                'Minggu 3 (Tgl 15-21)',
                'Minggu 4 (Tgl 22-28)',
                'Minggu 5 (Tgl 29+)',
            ],
        ];
    }

    /**
     * @param  Collection<int, mixed>  $items
     * @return array<int, int>
     */
    protected function calculateWeeklyCounts(Collection $items, string $dateField): array
    {
        $counts = [0, 0, 0, 0, 0];

        foreach ($items as $item) {
            $rawDate = $item->{$dateField};
            $date = $rawDate instanceof Carbon ? $rawDate : Carbon::parse($rawDate);
            $day = (int) $date->format('j');

            if ($day <= 7) {
                $counts[0]++;
            } elseif ($day <= 14) {
                $counts[1]++;
            } elseif ($day <= 21) {
                $counts[2]++;
            } elseif ($day <= 28) {
                $counts[3]++;
            } else {
                $counts[4]++;
            }
        }

        return $counts;
    }

    protected function getType(): string
    {
        return 'bar';
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
