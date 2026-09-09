<?php

namespace App\Filament\Resources\Doctors\Tables;

use App\Services\Reports\ExcelExportService;
use App\Services\Reports\PdfExportService;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class DoctorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label(__('Photo'))
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=Dr&background=0D8ABC&color=fff'),

                TextColumn::make('name')
                    ->label(__('Doctor Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('specialty')
                    ->label(__('Specialty'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->placeholder('Umum'),

                TextColumn::make('license_number')
                    ->label(__('STR No.'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('Active Status')),
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
                            $headers = ['No.', 'Nama Dokter', 'Spesialisasi', 'No. STR / SIP', 'No. Telepon', 'Email', 'Status'];
                            $rows = [];
                            $no = 1;
                            foreach ($records as $d) {
                                $spec = is_array($d->specialty) ? ($d->specialty['id'] ?? reset($d->specialty)) : ($d->specialty ?? 'Umum');
                                $rows[] = [
                                    $no++,
                                    $d->name,
                                    $spec,
                                    $d->license_number ?? '-',
                                    $d->phone ?? '-',
                                    $d->email ?? '-',
                                    $d->is_active ? 'Aktif' : 'Tidak Aktif',
                                ];
                            }

                            return $excelService->exportGenericTable('Daftar Dokter Terpilih', $headers, $rows, 'Dokter_Terpilih_'.now()->format('Ymd_His').'.xlsx');
                        }),

                    BulkAction::make('export_pdf_selected')
                        ->label(__('Ekspor PDF'))
                        ->icon(Heroicon::OutlinedDocumentArrowDown)
                        ->color('danger')
                        ->action(function (Collection $records, PdfExportService $pdfService) {
                            $headers = ['No.', 'Nama Dokter', 'Spesialisasi', 'No. STR / SIP', 'No. Telepon', 'Email', 'Status'];
                            $rows = [];
                            $no = 1;
                            foreach ($records as $d) {
                                $spec = is_array($d->specialty) ? ($d->specialty['id'] ?? reset($d->specialty)) : ($d->specialty ?? 'Umum');
                                $rows[] = [
                                    $no++,
                                    $d->name,
                                    $spec,
                                    $d->license_number ?? '-',
                                    $d->phone ?? '-',
                                    $d->email ?? '-',
                                    $d->is_active ? 'Aktif' : 'Tidak Aktif',
                                ];
                            }

                            return $pdfService->exportGenericTable('Daftar Dokter Terpilih', $headers, $rows, 'Dokter_Terpilih_'.now()->format('Ymd_His').'.pdf', 'landscape');
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
