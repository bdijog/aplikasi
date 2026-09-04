<?php

namespace App\Filament\Resources\QueueTickets\Tables;

use App\Enums\AppointmentStatus;
use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use App\Models\QueueTicket;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QueueTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_number')
                    ->label(__('Queue No.'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('queue_date')
                    ->label(__('Date'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('appointment.patient.name')
                    ->label(__('Patient'))
                    ->searchable(),

                TextColumn::make('doctor.name')
                    ->label(__('Doctor'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('priority')
                    ->label(__('Priority'))
                    ->badge(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),

                TextColumn::make('counter')
                    ->label(__('Counter/Clinic'))
                    ->placeholder('-'),

                TextColumn::make('call_count')
                    ->label(__('Calls'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('called_at')
                    ->label(__('Call Time'))
                    ->time('H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('served_at')
                    ->label(__('Serve Time'))
                    ->time('H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('completed_at')
                    ->label(__('Completed'))
                    ->time('H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('doctor_id')
                    ->label(__('Doctor'))
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label(__('Queue Status'))
                    ->options(QueueTicketStatus::class),

                SelectFilter::make('priority')
                    ->label(__('Priority'))
                    ->options(QueueTicketPriority::class),
            ])
            ->recordActions([
                Action::make('call')
                    ->label(__('Call'))
                    ->icon(Heroicon::OutlinedSpeakerWave)
                    ->color('info')
                    ->visible(fn (QueueTicket $record): bool => in_array($record->status, [QueueTicketStatus::Waiting, QueueTicketStatus::Skipped], true))
                    ->action(function (QueueTicket $record): void {
                        $record->increment('call_count');
                        $record->status = QueueTicketStatus::Serving;
                        $record->called_at = now();
                        if (! $record->served_at) {
                            $record->served_at = now();
                        }
                        $record->save();

                        if ($record->appointment) {
                            $record->appointment->update(['status' => AppointmentStatus::InProgress]);
                        }

                        Notification::make()
                            ->title("Memanggil Antrian {$record->display_number}")
                            ->body("Panggilan ke-{$record->call_count} untuk pasien {$record->appointment?->patient?->name}")
                            ->info()
                            ->send();
                    }),

                Action::make('complete')
                    ->label(__('Complete'))
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (QueueTicket $record): bool => $record->status === QueueTicketStatus::Serving)
                    ->action(function (QueueTicket $record): void {
                        $record->status = QueueTicketStatus::Completed;
                        $record->completed_at = now();
                        $record->save();

                        if ($record->appointment) {
                            $record->appointment->update(['status' => AppointmentStatus::Completed]);
                        }

                        Notification::make()
                            ->title("Antrian {$record->display_number} Selesai")
                            ->success()
                            ->send();
                    }),

                Action::make('skip')
                    ->label(__('Skip'))
                    ->icon(Heroicon::OutlinedForward)
                    ->color('warning')
                    ->visible(fn (QueueTicket $record): bool => $record->status === QueueTicketStatus::Serving)
                    ->action(function (QueueTicket $record): void {
                        $record->status = QueueTicketStatus::Skipped;
                        $record->save();

                        Notification::make()
                            ->title("Antrian {$record->display_number} Dilewati")
                            ->warning()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
