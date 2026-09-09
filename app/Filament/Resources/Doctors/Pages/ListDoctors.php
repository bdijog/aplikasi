<?php

namespace App\Filament\Resources\Doctors\Pages;

use App\Filament\Resources\Doctors\DoctorResource;
use App\Services\Reports\ExcelExportService;
use App\Services\Reports\PdfExportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListDoctors extends ListRecords
{
    use Translatable;

    protected static string $resource = DoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label(__('Ekspor Excel'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->action(function (ExcelExportService $excelService) {
                    $records = $this->getFilteredTableQuery()->get();
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

                    return $excelService->exportGenericTable('Daftar Data Dokter', $headers, $rows, 'Data_Dokter_'.now()->format('Ymd_His').'.xlsx');
                }),

            Action::make('export_pdf')
                ->label(__('Ekspor PDF'))
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('danger')
                ->action(function (PdfExportService $pdfService) {
                    $records = $this->getFilteredTableQuery()->get();
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

                    return $pdfService->exportGenericTable('Daftar Data Dokter', $headers, $rows, 'Data_Dokter_'.now()->format('Ymd_His').'.pdf', 'landscape');
                }),

            CreateAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
