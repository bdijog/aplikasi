<?php

namespace App\Filament\Resources\Schedules\Tables;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Services\Reports\ExcelExportService;
use App\Services\Reports\PdfExportService;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doctor.name')
                    ->label(__('Doctor'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge(),

                TextColumn::make('day_of_week')
                    ->label(__('Practice Day'))
                    ->formatStateUsing(fn ($state) => [
                        0 => 'Minggu',
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                    ][$state] ?? '-')
                    ->sortable(),

                TextColumn::make('specific_date')
                    ->label(__('Special Date'))
                    ->date('d M Y')
                    ->placeholder('-'),

                TextColumn::make('start_time')
                    ->label(__('Start Time'))
                    ->time('H:i'),

                TextColumn::make('end_time')
                    ->label(__('End Time'))
                    ->time('H:i'),

                TextColumn::make('max_patients')
                    ->label(__('Quota'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('Status'))
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
                    ->options(ScheduleStatus::class),

                SelectFilter::make('type')
                    ->label(__('Schedule Type'))
                    ->options(ScheduleType::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export_excel_selected')
                        ->label(__('Ekspor Jadwal Excel'))
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->color('success')
                        ->action(function (Collection $records, ExcelExportService $excelService) {
                            $records->loadMissing('doctor');
                            $dayNames = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
                            $headers = ['No.', 'Nama Dokter', 'Poli / Spesialisasi', 'Tipe', 'Hari', 'Tanggal Khusus', 'Jam Mulai', 'Jam Selesai', 'Kuota', 'Status', 'Catatan'];
                            $rows = [];
                            $no = 1;
                            foreach ($records as $s) {
                                $doc = $s->doctor;
                                $spec = is_array($doc?->specialty) ? ($doc->specialty['id'] ?? reset($doc->specialty)) : ($doc?->specialty ?? '-');
                                $dayStr = $s->day_of_week !== null ? ($dayNames[$s->day_of_week] ?? '-') : '-';
                                $rows[] = [
                                    $no++,
                                    $doc?->name ?? '-',
                                    $spec,
                                    $s->type?->getLabel() ?? (string) $s->type,
                                    $dayStr,
                                    $s->specific_date?->format('d/m/Y') ?? '-',
                                    $s->start_time ? substr((string) $s->start_time, 0, 5) : '-',
                                    $s->end_time ? substr((string) $s->end_time, 0, 5) : '-',
                                    $s->max_patients,
                                    $s->status?->getLabel() ?? (string) $s->status,
                                    $s->notes ?? '-',
                                ];
                            }

                            return $excelService->exportGenericTable('Roster Jadwal Praktik Terpilih', $headers, $rows, 'Jadwal_Terpilih_'.now()->format('Ymd_His').'.xlsx');
                        }),

                    BulkAction::make('export_pdf_selected')
                        ->label(__('Ekspor Jadwal PDF'))
                        ->icon(Heroicon::OutlinedDocumentArrowDown)
                        ->color('danger')
                        ->action(function (Collection $records, PdfExportService $pdfService) {
                            $records->loadMissing('doctor');
                            $dayNames = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
                            $headers = ['No.', 'Nama Dokter', 'Spesialisasi', 'Hari / Tanggal', 'Jam Praktik', 'Kuota', 'Status'];
                            $rows = [];
                            $no = 1;
                            foreach ($records as $s) {
                                $doc = $s->doctor;
                                $spec = is_array($doc?->specialty) ? ($doc->specialty['id'] ?? reset($doc->specialty)) : ($doc?->specialty ?? '-');
                                $dayStr = $s->day_of_week !== null ? ($dayNames[$s->day_of_week] ?? '-') : ($s->specific_date?->format('d/m/Y') ?? '-');
                                $timeStr = ($s->start_time ? substr((string) $s->start_time, 0, 5) : '-').' - '.($s->end_time ? substr((string) $s->end_time, 0, 5) : '-');
                                $rows[] = [
                                    $no++,
                                    $doc?->name ?? '-',
                                    $spec,
                                    $dayStr,
                                    $timeStr,
                                    $s->max_patients.' Pasien',
                                    $s->status?->getLabel() ?? (string) $s->status,
                                ];
                            }

                            return $pdfService->exportGenericTable('Roster Jadwal Praktik Terpilih', $headers, $rows, 'Jadwal_Terpilih_'.now()->format('Ymd_His').'.pdf', 'portrait');
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
