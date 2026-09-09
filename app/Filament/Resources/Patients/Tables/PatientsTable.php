<?php

namespace App\Filament\Resources\Patients\Tables;

use App\Enums\Gender;
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

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('medical_record_number')
                    ->label(__('RM No.'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label(__('Patient Name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('national_id')
                    ->label(__('NIK'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable(),

                TextColumn::make('gender')
                    ->label(__('Gender'))
                    ->badge(),

                TextColumn::make('date_of_birth')
                    ->label(__('Date of Birth (short)'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('blood_type')
                    ->label(__('Blood Type (short)'))
                    ->badge()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->label(__('Gender'))
                    ->options(Gender::class),

                SelectFilter::make('blood_type')
                    ->label(__('Blood Type'))
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'AB' => 'AB',
                        'O' => 'O',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export_excel_selected')
                        ->label(__('Ekspor Excel'))
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->color('success')
                        ->action(function (Collection $records, ExcelExportService $excelService) {
                            $headers = ['No.', 'No. RM', 'Nama Pasien', 'NIK', 'Jenis Kelamin', 'Tanggal Lahir', 'Gol. Darah', 'No. Telepon', 'Alamat'];
                            $rows = [];
                            $no = 1;
                            foreach ($records as $p) {
                                $rows[] = [
                                    $no++,
                                    $p->medical_record_number ?? '-',
                                    $p->name,
                                    $p->national_id ?? '-',
                                    $p->gender?->getLabel() ?? (string) $p->gender,
                                    $p->date_of_birth?->format('d/m/Y') ?? '-',
                                    $p->blood_type ?? '-',
                                    $p->phone ?? '-',
                                    $p->address ?? '-',
                                ];
                            }

                            return $excelService->exportGenericTable('Daftar Pasien Terpilih', $headers, $rows, 'Pasien_Terpilih_'.now()->format('Ymd_His').'.xlsx');
                        }),

                    BulkAction::make('export_pdf_selected')
                        ->label(__('Ekspor PDF'))
                        ->icon(Heroicon::OutlinedDocumentArrowDown)
                        ->color('danger')
                        ->action(function (Collection $records, PdfExportService $pdfService) {
                            $headers = ['No.', 'No. RM', 'Nama Pasien', 'NIK', 'Jenis Kelamin', 'Tanggal Lahir', 'Gol. Darah', 'No. Telepon', 'Alamat'];
                            $rows = [];
                            $no = 1;
                            foreach ($records as $p) {
                                $rows[] = [
                                    $no++,
                                    $p->medical_record_number ?? '-',
                                    $p->name,
                                    $p->national_id ?? '-',
                                    $p->gender?->getLabel() ?? (string) $p->gender,
                                    $p->date_of_birth?->format('d/m/Y') ?? '-',
                                    $p->blood_type ?? '-',
                                    $p->phone ?? '-',
                                    $p->address ?? '-',
                                ];
                            }

                            return $pdfService->exportGenericTable('Daftar Pasien Terpilih', $headers, $rows, 'Pasien_Terpilih_'.now()->format('Ymd_His').'.pdf', 'landscape');
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
