<?php

namespace App\Filament\Widgets;

use App\Enums\QueueTicketStatus;
use App\Models\Doctor;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ActiveDoctorQueuesTableWidget extends TableWidget
{
    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '10s';

    public function getPollingInterval(): ?string
    {
        return $this->pollingInterval;
    }

    public function table(Table $table): Table
    {
        $today = now()->toDateString();

        return $table
            ->heading(__('Antrian Aktif per Dokter'))
            ->description(__('Pantauan real-time status antrean aktif: nomor sedang dipanggil, jumlah pasien menunggu, dan antrean selesai hari ini.'))
            ->query(
                Doctor::query()
                    ->where('is_active', true)
                    ->with([
                        'queueTickets' => function ($query) use ($today) {
                            $query->whereDate('queue_date', $today)
                                ->orderBy('queue_number', 'asc');
                        },
                    ])
                    ->withCount([
                        'queueTickets as waiting_count' => function ($query) use ($today) {
                            $query->whereDate('queue_date', $today)->where('status', QueueTicketStatus::Waiting);
                        },
                        'queueTickets as serving_count' => function ($query) use ($today) {
                            $query->whereDate('queue_date', $today)->where('status', QueueTicketStatus::Serving);
                        },
                        'queueTickets as completed_today_count' => function ($query) use ($today) {
                            $query->whereDate('queue_date', $today)->where('status', QueueTicketStatus::Completed);
                        },
                        'queueTickets as total_today_count' => function ($query) use ($today) {
                            $query->whereDate('queue_date', $today);
                        },
                    ])
            )
            ->defaultSort('waiting_count', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('Dokter'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-user-circle')
                    ->description(function (Doctor $record): string {
                        $locale = app()->getLocale();
                        $translated = $record->getTranslation('specialty', $locale, false);
                        if ($translated) {
                            return $translated;
                        }

                        if (is_array($record->specialty)) {
                            return $record->specialty[$locale] ?? $record->specialty['id'] ?? $record->specialty['en'] ?? '';
                        }

                        return (string) ($record->specialty ?: '');
                    }),

                TextColumn::make('currently_serving')
                    ->label(__('Sedang Dipanggil'))
                    ->badge()
                    ->state(function (Doctor $record): ?string {
                        $servingTicket = $record->queueTickets->firstWhere('status', QueueTicketStatus::Serving);
                        if ($servingTicket) {
                            $counter = $servingTicket->counter ? " ({$servingTicket->counter})" : '';

                            return $servingTicket->display_number.$counter;
                        }

                        return null;
                    })
                    ->placeholder(__('Belum ada panggilan'))
                    ->color(fn ($state): string => $state ? 'primary' : 'gray')
                    ->icon(fn ($state): ?string => $state ? 'heroicon-m-megaphone' : null),

                TextColumn::make('waiting_count')
                    ->label(__('Menunggu'))
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state > 5 => 'danger',
                        $state > 0 => 'warning',
                        default => 'gray',
                    })
                    ->icon('heroicon-m-clock')
                    ->formatStateUsing(fn ($state): string => "{$state} Pasien"),

                TextColumn::make('completed_today_count')
                    ->label(__('Selesai Hari Ini'))
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => $state > 0 ? 'success' : 'gray')
                    ->icon('heroicon-m-check-circle')
                    ->formatStateUsing(fn ($state): string => "{$state} Pasien"),

                TextColumn::make('operational_status')
                    ->label(__('Status Operasional'))
                    ->badge()
                    ->state(function (Doctor $record): string {
                        $serving = $record->queueTickets->firstWhere('status', QueueTicketStatus::Serving);
                        $waiting = $record->queueTickets->where('status', QueueTicketStatus::Waiting)->count();
                        $completed = $record->queueTickets->where('status', QueueTicketStatus::Completed)->count();

                        if ($serving) {
                            return 'Sedang Melayani';
                        }
                        if ($waiting > 0) {
                            return 'Pasien Menunggu';
                        }
                        if ($completed > 0) {
                            return 'Standby (Selesai)';
                        }

                        return 'Tidak Ada Antrean';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Sedang Melayani' => 'info',
                        'Pasien Menunggu' => 'warning',
                        'Standby (Selesai)' => 'success',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                Action::make('view_queue')
                    ->label(__('Buka Antrean'))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Doctor $record): string => route('filament.admin.resources.queue-tickets.index', [
                        'tableFilters[doctor_id][value]' => $record->id,
                    ])),
            ])
            ->filters([
                Filter::make('queue_date')
                    ->form([
                        DatePicker::make('date')
                            ->label(__('Tanggal Antrean'))
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $date = ! empty($data['date']) ? Carbon::parse($data['date'])->toDateString() : now()->toDateString();

                        return $query->with([
                            'queueTickets' => function ($q) use ($date) {
                                $q->whereDate('queue_date', $date)
                                    ->orderBy('queue_number', 'asc');
                            },
                        ])->withCount([
                            'queueTickets as waiting_count' => function ($q) use ($date) {
                                $q->whereDate('queue_date', $date)->where('status', QueueTicketStatus::Waiting);
                            },
                            'queueTickets as serving_count' => function ($q) use ($date) {
                                $q->whereDate('queue_date', $date)->where('status', QueueTicketStatus::Serving);
                            },
                            'queueTickets as completed_today_count' => function ($q) use ($date) {
                                $q->whereDate('queue_date', $date)->where('status', QueueTicketStatus::Completed);
                            },
                            'queueTickets as total_today_count' => function ($q) use ($date) {
                                $q->whereDate('queue_date', $date);
                            },
                        ]);
                    }),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->poll($this->pollingInterval);
    }
}
