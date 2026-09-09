<?php

namespace App\Filament\Widgets;

use App\Enums\AppointmentStatus;
use App\Models\Doctor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class DoctorVisitRankTableWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    public function getPollingInterval(): ?string
    {
        return $this->pollingInterval;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Dokter Paling Banyak Kunjungan'))
            ->description(__('Peringkat dokter berdasarkan total kunjungan pasien yang dilayani dan rata-rata durasi konsultasi.'))
            ->query(
                Doctor::query()
                    ->where('is_active', true)
                    ->withCount([
                        'appointments as visits_count' => function ($query) {
                            $query->whereIn('status', [
                                AppointmentStatus::Completed,
                                AppointmentStatus::CheckedIn,
                                AppointmentStatus::InProgress,
                            ]);
                        },
                    ])
                    ->with([
                        'queueTickets' => function ($query) {
                            $query->whereNotNull('served_at')->whereNotNull('completed_at');
                        },
                    ])
            )
            ->defaultSort('visits_count', 'desc')
            ->columns([
                TextColumn::make('rank')
                    ->label(__('Peringkat'))
                    ->rowIndex()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'warning',
                        '2' => 'gray',
                        '3' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): ?string => match ($state) {
                        '1' => 'heroicon-m-trophy',
                        '2' => 'heroicon-m-star',
                        '3' => 'heroicon-m-sparkles',
                        default => null,
                    })
                    ->formatStateUsing(fn (string $state): string => "#{$state}"),

                TextColumn::make('name')
                    ->label(__('Nama Dokter'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Doctor $record): ?string => $record->license_number ? 'SIP: '.$record->license_number : null),

                TextColumn::make('specialty')
                    ->label(__('Spesialisasi'))
                    ->badge()
                    ->color('info')
                    ->state(function (Doctor $record): string {
                        $locale = app()->getLocale();
                        $translated = $record->getTranslation('specialty', $locale, false);
                        if ($translated) {
                            return $translated;
                        }

                        if (is_array($record->specialty)) {
                            return $record->specialty[$locale] ?? $record->specialty['id'] ?? $record->specialty['en'] ?? '-';
                        }

                        return (string) ($record->specialty ?: '-');
                    }),

                TextColumn::make('visits_count')
                    ->label(__('Jumlah Pasien'))
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-user-group')
                    ->formatStateUsing(fn ($state): string => number_format((int) $state, 0, ',', '.').' Pasien'),

                TextColumn::make('avg_duration')
                    ->label(__('Rata-rata Durasi'))
                    ->badge()
                    ->state(function (Doctor $record): string {
                        $validTickets = $record->queueTickets->filter(
                            fn ($t) => $t->completed_at && $t->served_at && $t->completed_at->greaterThanOrEqualTo($t->served_at)
                        );

                        if ($validTickets->isEmpty()) {
                            return '-';
                        }

                        $avgMinutes = round($validTickets->avg(fn ($t) => $t->served_at->diffInMinutes($t->completed_at)), 1);

                        return $avgMinutes.' mnt';
                    })
                    ->color(function (string $state): string {
                        if ($state === '-') {
                            return 'gray';
                        }
                        $minutes = (float) $state;

                        return match (true) {
                            $minutes > 30 => 'warning',
                            $minutes > 0 => 'success',
                            default => 'gray',
                        };
                    })
                    ->icon(fn (string $state): ?string => $state !== '-' ? 'heroicon-m-clock' : null),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->label(__('Periode'))
                    ->options([
                        'all' => 'Semua Waktu',
                        'today' => 'Hari Ini',
                        'week' => '7 Hari Terakhir',
                        'month' => 'Bulan Ini',
                    ])
                    ->default('all')
                    ->query(function (Builder $query, array $data): Builder {
                        $period = $data['value'] ?? 'all';
                        $startDate = match ($period) {
                            'today' => now()->startOfDay(),
                            'week' => now()->subDays(6)->startOfDay(),
                            'month' => now()->startOfMonth(),
                            default => null,
                        };

                        return $query->withCount([
                            'appointments as visits_count' => function ($q) use ($startDate) {
                                $q->whereIn('status', [
                                    AppointmentStatus::Completed,
                                    AppointmentStatus::CheckedIn,
                                    AppointmentStatus::InProgress,
                                ]);

                                if ($startDate) {
                                    $q->where('appointment_date', '>=', $startDate->format('Y-m-d'));
                                }
                            },
                        ])->with([
                            'queueTickets' => function ($q) use ($startDate) {
                                $q->whereNotNull('served_at')->whereNotNull('completed_at');
                                if ($startDate) {
                                    $q->whereDate('queue_date', '>=', $startDate->format('Y-m-d'));
                                }
                            },
                        ]);
                    }),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->poll($this->pollingInterval);
    }
}
