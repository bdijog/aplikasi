<?php

namespace App\Filament\Pages;

use App\Enums\AppointmentStatus;
use App\Models\Doctor;
use App\Services\Reports\ExcelExportService;
use App\Services\Reports\PdfExportService;
use App\Services\Reports\ReportDataService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static ?string $navigationLabel = 'Laporan & Ekspor Data';

    protected static ?string $title = 'Laporan & Ekspor Data';

    protected string $view = 'filament.pages.reports-page';

    protected static ?int $navigationSort = 5;

    public string $report_type = 'daily_visits';

    public ?string $start_date = null;

    public ?string $end_date = null;

    public ?int $doctor_id = null;

    public ?string $status = null;

    public int $month = 0;

    public int $year = 0;

    /**
     * @var array<string, mixed>
     */
    public array $previewData = [];

    public static function getNavigationGroup(): ?string
    {
        return __('Patient Services & Queue');
    }

    public function mount(): void
    {
        $this->start_date = now()->subDays(7)->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
        $this->month = (int) now()->format('m');
        $this->year = (int) now()->format('Y');

        $this->generatePreview();
    }

    public function updatedReportType(): void
    {
        $this->generatePreview();
    }

    public function generatePreview(): void
    {
        $dataService = app(ReportDataService::class);

        $this->previewData = match ($this->report_type) {
            'daily_visits' => $dataService->getDailyVisitsData([
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
                'status' => $this->status ?: null,
            ]),
            'monthly_stats' => $dataService->getMonthlyStatsData(
                year: $this->year ?: (int) now()->format('Y'),
                month: $this->month ?: (int) now()->format('m'),
                doctorId: $this->doctor_id ? (int) $this->doctor_id : null,
            ),
            'doctor_performance' => $dataService->getDoctorPerformanceData([
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
            ]),
            'queue_wait_time' => $dataService->getQueueWaitTimeData([
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
            ]),
            'doctor_schedules' => $dataService->getDoctorSchedulesData([
                'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
                'status' => $this->status ?: null,
            ]),
            default => [],
        };
    }

    public function downloadExcel(): ?StreamedResponse
    {
        $dataService = app(ReportDataService::class);
        $excelService = app(ExcelExportService::class);

        return match ($this->report_type) {
            'daily_visits' => $excelService->exportDailyVisits(
                $dataService->getDailyVisitsData([
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
                    'status' => $this->status ?: null,
                ])
            ),
            'monthly_stats' => $excelService->exportMonthlyStats(
                $dataService->getMonthlyStatsData(
                    year: $this->year ?: (int) now()->format('Y'),
                    month: $this->month ?: (int) now()->format('m'),
                    doctorId: $this->doctor_id ? (int) $this->doctor_id : null,
                )
            ),
            'doctor_performance' => $excelService->exportDoctorPerformance(
                $dataService->getDoctorPerformanceData([
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
                ])
            ),
            'queue_wait_time' => $excelService->exportQueueWaitTime(
                $dataService->getQueueWaitTimeData([
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
                ])
            ),
            'doctor_schedules' => $excelService->exportDoctorSchedules(
                $dataService->getDoctorSchedulesData([
                    'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
                    'status' => $this->status ?: null,
                ])
            ),
            default => null,
        };
    }

    public function downloadPdf(): ?StreamedResponse
    {
        $dataService = app(ReportDataService::class);
        $pdfService = app(PdfExportService::class);

        return match ($this->report_type) {
            'daily_visits' => $pdfService->exportDailyVisits(
                $dataService->getDailyVisitsData([
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
                    'status' => $this->status ?: null,
                ])
            ),
            'monthly_stats' => $pdfService->exportMonthlyStats(
                $dataService->getMonthlyStatsData(
                    year: $this->year ?: (int) now()->format('Y'),
                    month: $this->month ?: (int) now()->format('m'),
                    doctorId: $this->doctor_id ? (int) $this->doctor_id : null,
                )
            ),
            'doctor_performance' => $pdfService->exportDoctorPerformance(
                $dataService->getDoctorPerformanceData([
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
                ])
            ),
            'queue_wait_time' => $pdfService->exportQueueWaitTime(
                $dataService->getQueueWaitTimeData([
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
                ])
            ),
            'doctor_schedules' => $pdfService->exportDoctorSchedules(
                $dataService->getDoctorSchedulesData([
                    'doctor_id' => $this->doctor_id ? (int) $this->doctor_id : null,
                    'status' => $this->status ?: null,
                ])
            ),
            default => null,
        };
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Unduh Excel (.xlsx)')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->action(fn () => $this->downloadExcel()),

            Action::make('export_pdf')
                ->label('Unduh PDF (.pdf)')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('danger')
                ->action(fn () => $this->downloadPdf()),

            Action::make('apply_filter')
                ->label('Terapkan Filter & Pratinjau')
                ->icon(Heroicon::OutlinedEye)
                ->color('info')
                ->action(function (): void {
                    $this->generatePreview();
                    Notification::make()
                        ->title('Data Berhasil Diperbarui')
                        ->body('Pratinjau data laporan telah diperbarui sesuai kriteria filter.')
                        ->success()
                        ->send();
                }),
        ];
    }

    /**
     * Daftar dokter untuk opsi select filter.
     *
     * @return array<int, string>
     */
    public function getDoctorsListProperty(): array
    {
        return Doctor::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    /**
     * Opsi status appointment untuk filter.
     *
     * @return array<string, string>
     */
    public function getStatusListProperty(): array
    {
        $statuses = [];
        foreach (AppointmentStatus::cases() as $case) {
            $statuses[$case->value] = $case->getLabel();
        }

        return $statuses;
    }
}
