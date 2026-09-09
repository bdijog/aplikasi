<?php

namespace App\Filament\Resources\QueueTickets\Pages;

use App\Filament\Resources\QueueTickets\QueueTicketResource;
use App\Services\Reports\ExcelExportService;
use App\Services\Reports\PdfExportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListQueueTickets extends ListRecords
{
    protected static string $resource = QueueTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label(__('Ekspor Antrian Excel'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->action(function (ExcelExportService $excelService) {
                    $records = $this->getFilteredTableQuery()->with(['appointment.patient', 'doctor'])->get();
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

                    return $excelService->exportGenericTable('Daftar Tiket Antrian Layanan', $headers, $rows, 'Antrian_'.now()->format('Ymd_His').'.xlsx');
                }),

            Action::make('export_pdf')
                ->label(__('Ekspor Antrian PDF'))
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('danger')
                ->action(function (PdfExportService $pdfService) {
                    $records = $this->getFilteredTableQuery()->with(['appointment.patient', 'doctor'])->get();
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

                    return $pdfService->exportGenericTable('Daftar Tiket Antrian Layanan', $headers, $rows, 'Antrian_'.now()->format('Ymd_His').'.pdf', 'landscape');
                }),

            CreateAction::make(),
        ];
    }
}
