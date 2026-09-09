<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Services\Reports\ExcelExportService;
use App\Services\Reports\PdfExportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label(__('Ekspor Excel'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->action(function (ExcelExportService $excelService) {
                    $records = $this->getFilteredTableQuery()->with(['patient', 'doctor', 'schedule', 'queueTicket'])->get();
                    $headers = ['No.', 'Kode Booking', 'Tanggal', 'Jam', 'Pasien', 'No. RM', 'Dokter', 'Poli', 'Kunjungan', 'Status', 'Sumber', 'Check-In'];
                    $rows = [];
                    $no = 1;
                    foreach ($records as $r) {
                        $doc = $r->doctor;
                        $spec = is_array($doc?->specialty) ? ($doc->specialty['id'] ?? reset($doc->specialty)) : ($doc?->specialty ?? '-');
                        $rows[] = [
                            $no++,
                            $r->booking_code,
                            $r->appointment_date?->format('d/m/Y') ?? '-',
                            $r->estimated_time ? substr((string) $r->estimated_time, 0, 5) : '-',
                            $r->patient?->name ?? '-',
                            $r->patient?->medical_record_number ?? '-',
                            $doc?->name ?? '-',
                            $spec,
                            $r->visit_type?->getLabel() ?? (string) $r->visit_type,
                            $r->status?->getLabel() ?? (string) $r->status,
                            $r->source?->getLabel() ?? (string) $r->source,
                            $r->checked_in_at?->format('H:i:s') ?? '-',
                        ];
                    }

                    return $excelService->exportGenericTable('Daftar Janji Temu Pasien', $headers, $rows, 'Janji_Temu_'.now()->format('Ymd_His').'.xlsx');
                }),

            Action::make('export_pdf')
                ->label(__('Ekspor PDF'))
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('danger')
                ->action(function (PdfExportService $pdfService) {
                    $records = $this->getFilteredTableQuery()->with(['patient', 'doctor'])->get();
                    $headers = ['No.', 'Kode Booking', 'Tanggal', 'Jam', 'Nama Pasien', 'No. RM', 'Dokter', 'Kunjungan', 'Status'];
                    $rows = [];
                    $no = 1;
                    foreach ($records as $r) {
                        $rows[] = [
                            $no++,
                            $r->booking_code,
                            $r->appointment_date?->format('d/m/Y') ?? '-',
                            $r->estimated_time ? substr((string) $r->estimated_time, 0, 5) : '-',
                            $r->patient?->name ?? '-',
                            $r->patient?->medical_record_number ?? '-',
                            $r->doctor?->name ?? '-',
                            $r->visit_type?->getLabel() ?? (string) $r->visit_type,
                            $r->status?->getLabel() ?? (string) $r->status,
                        ];
                    }

                    return $pdfService->exportGenericTable('Daftar Janji Temu Pasien', $headers, $rows, 'Janji_Temu_'.now()->format('Ymd_His').'.pdf', 'landscape');
                }),

            CreateAction::make(),
        ];
    }
}
