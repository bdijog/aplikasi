<?php

namespace App\Filament\Resources\QueueTickets\Tables;

use App\Enums\AppointmentStatus;
use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use App\Models\QueueTicket;
use App\Services\Reports\ExcelExportService;
use App\Services\Reports\PdfExportService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

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
                    BulkAction::make('export_excel_selected')
                        ->label(__('Ekspor Antrian Excel'))
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->color('success')
                        ->action(function (Collection $records, ExcelExportService $excelService) {
                            $records->loadMissing(['appointment.patient', 'doctor']);
                            $headers = ['No.', 'No. Antrian', 'Tanggal', 'Nama Pasien', 'Dokter', 'Prioritas', 'Status', 'Counter/Poli', 'Panggilan', 'Waktu Panggil', 'Mulai Layanan', 'Selesai Layanan', 'Durasi Tunggu (Mnt)', 'Durasi Layanan (Mnt)'];
                            $rows = [];
                            $no = 1;
                            foreach ($records as $t) {
                                $checkIn = $t->appointment?->checked_in_at ?? $t->created_at;
                                $wait = ($checkIn && $t->called_at && $t->called_at->greaterThanOrEqualTo($checkIn)) ? $checkIn->diffInMinutes($t->called_at) : '-';
                                $serve = ($t->completed_at && $t->served_at && $t->completed_at->greaterThanOrEqualTo($t->served_at)) ? $t->served_at->diffInMinutes($t->completed_at) : '-';

                                $rows[] = [
                                    $no++,
                                    $t->display_number,
                                    $t->queue_date?->format('d/m/Y') ?? '-',
                                    $t->appointment?->patient?->name ?? '-',
                                    $t->doctor?->name ?? '-',
                                    $t->priority?->getLabel() ?? (string) $t->priority,
                                    $t->status?->getLabel() ?? (string) $t->status,
                                    $t->counter ?? '-',
                                    $t->call_count,
                                    $t->called_at?->format('H:i:s') ?? '-',
                                    $t->served_at?->format('H:i:s') ?? '-',
                                    $t->completed_at?->format('H:i:s') ?? '-',
                                    $wait,
                                    $serve,
                                ];
                            }

                            return $excelService->exportGenericTable('Daftar Tiket Antrian Terpilih', $headers, $rows, 'Antrian_Terpilih_'.now()->format('Ymd_His').'.xlsx');
                        }),

                    BulkAction::make('export_pdf_selected')
                        ->label(__('Ekspor Antrian PDF'))
                        ->icon(Heroicon::OutlinedDocumentArrowDown)
                        ->color('danger')
                        ->action(function (Collection $records, PdfExportService $pdfService) {
                            $records->loadMissing(['appointment.patient', 'doctor']);
                            $headers = ['No.', 'No. Tiket', 'Tanggal', 'Nama Pasien', 'Dokter', 'Prioritas', 'Status', 'Panggil', 'Selesai'];
                            $rows = [];
                            $no = 1;
                            foreach ($records as $t) {
                                $rows[] = [
                                    $no++,
                                    $t->display_number,
                                    $t->queue_date?->format('d/m/Y') ?? '-',
                                    $t->appointment?->patient?->name ?? '-',
                                    $t->doctor?->name ?? '-',
                                    $t->priority?->getLabel() ?? (string) $t->priority,
                                    $t->status?->getLabel() ?? (string) $t->status,
                                    $t->called_at?->format('H:i:s') ?? '-',
                                    $t->completed_at?->format('H:i:s') ?? '-',
                                ];
                            }

                            return $pdfService->exportGenericTable('Daftar Tiket Antrian Terpilih', $headers, $rows, 'Antrian_Terpilih_'.now()->format('Ymd_His').'.pdf', 'landscape');
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
