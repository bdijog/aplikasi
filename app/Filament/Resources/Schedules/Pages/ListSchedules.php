<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Services\Reports\ExcelExportService;
use App\Services\Reports\PdfExportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        $dayNames = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        return [
            Action::make('export_excel')
                ->label(__('Ekspor Jadwal Excel'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->action(function (ExcelExportService $excelService) use ($dayNames) {
                    $records = $this->getFilteredTableQuery()->with('doctor')->get();
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

                    return $excelService->exportGenericTable('Roster Jadwal Praktik Dokter', $headers, $rows, 'Jadwal_Dokter_'.now()->format('Ymd_His').'.xlsx');
                }),

            Action::make('export_pdf')
                ->label(__('Ekspor Jadwal PDF'))
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('danger')
                ->action(function (PdfExportService $pdfService) use ($dayNames) {
                    $records = $this->getFilteredTableQuery()->with('doctor')->get();
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

                    return $pdfService->exportGenericTable('Roster Jadwal Praktik Dokter', $headers, $rows, 'Jadwal_Dokter_'.now()->format('Ymd_His').'.pdf', 'portrait');
                }),

            CreateAction::make(),
        ];
    }
}
