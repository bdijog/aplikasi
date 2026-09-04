<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Enums\AppointmentSource;
use App\Enums\AppointmentStatus;
use App\Enums\CheckInMethod;
use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use App\Enums\VisitType;
use App\Models\Appointment;
use App\Models\QueueTicket;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_code')
                    ->label(__('Booking Code'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('appointment_date')
                    ->label(__('Date'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('estimated_time')
                    ->label(__('Time'))
                    ->time('H:i')
                    ->placeholder('-'),

                TextColumn::make('patient.name')
                    ->label(__('Patient'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('doctor.name')
                    ->label(__('Doctor'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),

                TextColumn::make('visit_type')
                    ->label(__('Visit'))
                    ->badge(),

                TextColumn::make('source')
                    ->label(__('Source'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('doctor_id')
                    ->label(__('Doctor'))
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(AppointmentStatus::class),

                SelectFilter::make('visit_type')
                    ->label(__('Visit Type'))
                    ->options(VisitType::class),

                SelectFilter::make('source')
                    ->label(__('Booking Source'))
                    ->options(AppointmentSource::class),
            ])
            ->recordActions([
                Action::make('checkIn')
                    ->label(__('Check-in'))
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('Process Patient Check-in'))
                    ->modalDescription(__('Has the patient arrived at the clinic? The system will mark the appointment as Checked-in and issue a queue ticket.'))
                    ->visible(fn (Appointment $record): bool => in_array($record->status, [AppointmentStatus::Confirmed, AppointmentStatus::Pending], true))
                    ->action(function (Appointment $record): void {
                        $record->status = AppointmentStatus::CheckedIn;
                        $record->checked_in_at = now();
                        $record->check_in_method = CheckInMethod::Counter;
                        $record->save();

                        // Otomatis terbitkan QueueTicket jika belum ada
                        if (! $record->queueTicket) {
                            $today = now()->toDateString();
                            $lastNumber = QueueTicket::where('doctor_id', $record->doctor_id)
                                ->where('queue_date', $today)
                                ->max('queue_number') ?? 0;
                            $nextNumber = $lastNumber + 1;
                            $prefix = 'A';
                            $display = sprintf('%s-%03d', $prefix, $nextNumber);

                            QueueTicket::create([
                                'appointment_id' => $record->id,
                                'doctor_id' => $record->doctor_id,
                                'schedule_id' => $record->schedule_id,
                                'queue_date' => $today,
                                'queue_number' => $nextNumber,
                                'prefix' => $prefix,
                                'display_number' => $display,
                                'status' => QueueTicketStatus::Waiting,
                                'priority' => QueueTicketPriority::Normal,
                                'call_count' => 0,
                            ]);
                        }

                        Notification::make()
                            ->title(__('Check-in Successful'))
                            ->body("Pasien {$record->patient->name} telah check-in dan tiket antrian berhasil dibuat.")
                            ->success()
                            ->send();
                    }),

                Action::make('confirm')
                    ->label(__('Confirm'))
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('info')
                    ->visible(fn (Appointment $record): bool => $record->status === AppointmentStatus::Pending)
                    ->action(function (Appointment $record): void {
                        $record->status = AppointmentStatus::Confirmed;
                        $record->save();

                        Notification::make()
                            ->title(__('Appointment Confirmed'))
                            ->success()
                            ->send();
                    }),

                Action::make('cancel')
                    ->label(__('Cancel'))
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->form([
                        Textarea::make('cancellation_reason')
                            ->label(__('Cancellation Reason'))
                            ->required(),
                    ])
                    ->visible(fn (Appointment $record): bool => ! in_array($record->status, [AppointmentStatus::Cancelled, AppointmentStatus::Completed], true))
                    ->action(function (Appointment $record, array $data): void {
                        $record->status = AppointmentStatus::Cancelled;
                        $record->cancellation_reason = $data['cancellation_reason'];
                        $record->cancelled_at = now();
                        $record->save();

                        if ($record->queueTicket) {
                            $record->queueTicket->update(['status' => QueueTicketStatus::Cancelled]);
                        }

                        Notification::make()
                            ->title(__('Appointment Cancelled'))
                            ->danger()
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
