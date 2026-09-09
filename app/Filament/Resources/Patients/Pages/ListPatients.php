<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use App\Services\Reports\ExcelExportService;
use App\Services\Reports\PdfExportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListPatients extends ListRecords
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label(__('Ekspor Excel'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->action(function (ExcelExportService $excelService) {
                    $records = $this->getFilteredTableQuery()->get();
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

                    return $excelService->exportGenericTable('Daftar Data Pasien', $headers, $rows, 'Data_Pasien_'.now()->format('Ymd_His').'.xlsx');
                }),

            Action::make('export_pdf')
                ->label(__('Ekspor PDF'))
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('danger')
                ->action(function (PdfExportService $pdfService) {
                    $records = $this->getFilteredTableQuery()->get();
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

                    return $pdfService->exportGenericTable('Daftar Data Pasien', $headers, $rows, 'Data_Pasien_'.now()->format('Ymd_His').'.pdf', 'landscape');
                }),

            CreateAction::make(),
        ];
    }
}
