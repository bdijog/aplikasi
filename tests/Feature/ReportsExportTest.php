<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Enums\VisitType;
use App\Filament\Pages\ReportsPage;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Schedule;
use App\Models\User;
use App\Services\Reports\ExcelExportService;
use App\Services\Reports\PdfExportService;
use App\Services\Reports\ReportDataService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class ReportsExportTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected Doctor $doctor;

    protected Patient $patient;

    protected Schedule $schedule;

    protected Appointment $appointment;

    protected QueueTicket $queueTicket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_report_test@klinik.test'],
            [
                'name' => 'Admin Report Test',
                'password' => bcrypt('password'),
            ]
        );
        $this->admin->assignRole('Super Admin');

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        // Buat data sampel untuk testing laporan
        $this->doctor = Doctor::create([
            'name' => 'dr. Sp. Test Laporan',
            'email' => 'dr.test_report@klinik.test',
            'password' => bcrypt('password'),
            'license_number' => 'STR-REPORT-001',
            'specialty' => ['id' => 'Penyakit Dalam', 'en' => 'Internal Medicine'],
            'is_active' => true,
        ]);

        $this->patient = Patient::create([
            'medical_record_number' => 'RM-REPORT-001',
            'name' => 'Pasien Uji Laporan',
            'email' => 'pasien.report@klinik.test',
            'phone' => '081299990001',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01',
        ]);

        $this->schedule = Schedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 1,
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'max_patients' => 20,
            'status' => ScheduleStatus::Active,
            'type' => ScheduleType::Recurring,
        ]);

        $this->appointment = Appointment::create([
            'booking_code' => 'APT-REPORT-001',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'schedule_id' => $this->schedule->id,
            'appointment_date' => now()->toDateString(),
            'estimated_time' => '08:30:00',
            'visit_type' => VisitType::NewVisit,
            'status' => AppointmentStatus::Completed,
            'source' => 'online',
            'checked_in_at' => now()->subMinutes(30),
        ]);

        $this->queueTicket = QueueTicket::create([
            'appointment_id' => $this->appointment->id,
            'doctor_id' => $this->doctor->id,
            'schedule_id' => $this->schedule->id,
            'queue_date' => now()->toDateString(),
            'queue_number' => 1,
            'prefix' => 'A',
            'display_number' => 'A-001',
            'status' => QueueTicketStatus::Completed,
            'priority' => QueueTicketPriority::Normal,
            'called_at' => now()->subMinutes(15),
            'served_at' => now()->subMinutes(14),
            'completed_at' => now(),
            'call_count' => 1,
            'counter' => 'Poli 1',
        ]);
    }

    public function test_report_data_service_returns_correct_daily_visits_structure(): void
    {
        $service = app(ReportDataService::class);
        $data = $service->getDailyVisitsData([
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'doctor_id' => $this->doctor->id,
        ]);

        $this->assertArrayHasKey('period', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('by_doctor', $data);
        $this->assertArrayHasKey('records', $data);
        $this->assertGreaterThanOrEqual(1, $data['summary']['total']);
        $this->assertGreaterThanOrEqual(1, $data['summary']['completed']);
    }

    public function test_report_data_service_returns_correct_monthly_stats(): void
    {
        $service = app(ReportDataService::class);
        $data = $service->getMonthlyStatsData(
            (int) now()->format('Y'),
            (int) now()->format('m'),
            $this->doctor->id
        );

        $this->assertArrayHasKey('month_name', $data);
        $this->assertArrayHasKey('total_visits', $data);
        $this->assertArrayHasKey('by_specialty', $data);
        $this->assertArrayHasKey('by_doctor', $data);
        $this->assertGreaterThanOrEqual(1, $data['total_visits']);
        $this->assertGreaterThanOrEqual(1, $data['new_visits']);
    }

    public function test_report_data_service_returns_correct_doctor_performance(): void
    {
        $service = app(ReportDataService::class);
        $data = $service->getDoctorPerformanceData([
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'doctor_id' => $this->doctor->id,
        ]);

        $this->assertArrayHasKey('doctors', $data);
        $this->assertArrayHasKey('summary', $data);

        $docStats = collect($data['doctors'])->firstWhere('doctor_id', $this->doctor->id);
        $this->assertNotNull($docStats);
        $this->assertEquals(1, $docStats['completed']);
        $this->assertGreaterThanOrEqual(0, $docStats['avg_consult_minutes']);
    }

    public function test_report_data_service_returns_correct_queue_wait_time(): void
    {
        $service = app(ReportDataService::class);
        $data = $service->getQueueWaitTimeData([
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'doctor_id' => $this->doctor->id,
        ]);

        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('records', $data);
        $this->assertGreaterThanOrEqual(1, $data['summary']['total_tickets']);
        $this->assertGreaterThanOrEqual(1, $data['summary']['completed_tickets']);
    }

    public function test_report_data_service_returns_correct_schedules_roster(): void
    {
        $service = app(ReportDataService::class);
        $data = $service->getDoctorSchedulesData([
            'doctor_id' => $this->doctor->id,
        ]);

        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('records', $data);
        $this->assertGreaterThanOrEqual(1, $data['summary']['total_schedules']);
        $this->assertGreaterThanOrEqual(1, $data['summary']['active_schedules']);
    }

    public function test_excel_export_service_streams_valid_xlsx_for_all_reports(): void
    {
        $dataService = app(ReportDataService::class);
        $excelService = app(ExcelExportService::class);

        // 1. Daily Visits
        $dailyData = $dataService->getDailyVisitsData(['doctor_id' => $this->doctor->id]);
        $resDaily = $excelService->exportDailyVisits($dailyData);
        $this->assertInstanceOf(StreamedResponse::class, $resDaily);
        $this->assertEquals(200, $resDaily->getStatusCode());
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $resDaily->headers->get('Content-Type'));

        // 2. Monthly Stats
        $monthlyData = $dataService->getMonthlyStatsData((int) now()->format('Y'), (int) now()->format('m'), $this->doctor->id);
        $resMonthly = $excelService->exportMonthlyStats($monthlyData);
        $this->assertEquals(200, $resMonthly->getStatusCode());

        // 3. Doctor Performance
        $perfData = $dataService->getDoctorPerformanceData(['doctor_id' => $this->doctor->id]);
        $resPerf = $excelService->exportDoctorPerformance($perfData);
        $this->assertEquals(200, $resPerf->getStatusCode());

        // 4. Queue Wait Time
        $queueData = $dataService->getQueueWaitTimeData(['doctor_id' => $this->doctor->id]);
        $resQueue = $excelService->exportQueueWaitTime($queueData);
        $this->assertEquals(200, $resQueue->getStatusCode());

        // 5. Doctor Schedules
        $schedData = $dataService->getDoctorSchedulesData(['doctor_id' => $this->doctor->id]);
        $resSched = $excelService->exportDoctorSchedules($schedData);
        $this->assertEquals(200, $resSched->getStatusCode());

        // 6. Generic Table
        $resGeneric = $excelService->exportGenericTable('Test Table', ['A', 'B'], [['1', '2']], 'test.xlsx');
        $this->assertEquals(200, $resGeneric->getStatusCode());
    }

    public function test_pdf_export_service_streams_valid_pdf_for_all_reports(): void
    {
        $dataService = app(ReportDataService::class);
        $pdfService = app(PdfExportService::class);

        // 1. Daily Visits PDF
        $dailyData = $dataService->getDailyVisitsData(['doctor_id' => $this->doctor->id]);
        $resDaily = $pdfService->exportDailyVisits($dailyData);
        $this->assertInstanceOf(StreamedResponse::class, $resDaily);
        $this->assertEquals(200, $resDaily->getStatusCode());
        $this->assertStringContainsString('application/pdf', $resDaily->headers->get('Content-Type'));

        // 2. Monthly Stats PDF
        $monthlyData = $dataService->getMonthlyStatsData((int) now()->format('Y'), (int) now()->format('m'), $this->doctor->id);
        $resMonthly = $pdfService->exportMonthlyStats($monthlyData);
        $this->assertEquals(200, $resMonthly->getStatusCode());

        // 3. Doctor Performance PDF
        $perfData = $dataService->getDoctorPerformanceData(['doctor_id' => $this->doctor->id]);
        $resPerf = $pdfService->exportDoctorPerformance($perfData);
        $this->assertEquals(200, $resPerf->getStatusCode());

        // 4. Queue Wait Time PDF
        $queueData = $dataService->getQueueWaitTimeData(['doctor_id' => $this->doctor->id]);
        $resQueue = $pdfService->exportQueueWaitTime($queueData);
        $this->assertEquals(200, $resQueue->getStatusCode());

        // 5. Doctor Schedules PDF
        $schedData = $dataService->getDoctorSchedulesData(['doctor_id' => $this->doctor->id]);
        $resSched = $pdfService->exportDoctorSchedules($schedData);
        $this->assertEquals(200, $resSched->getStatusCode());

        // 6. Generic Table PDF
        $resGeneric = $pdfService->exportGenericTable('Test Table', ['Col1', 'Col2'], [['Val1', 'Val2']], 'test.pdf');
        $this->assertEquals(200, $resGeneric->getStatusCode());
    }

    public function test_admin_can_access_reports_page_in_filament(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports-page');
        $response->assertStatus(200);
        $response->assertSee('Laporan');
        $response->assertSee('Konfigurasi');
        $response->assertSee('Unduh Excel (.xlsx)');
        $response->assertSee('Unduh PDF (.pdf)');
    }

    public function test_reports_page_livewire_component_interactivity(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(ReportsPage::class)
            ->assertSet('report_type', 'daily_visits');

        $this->assertNotEmpty($component->get('previewData'));

        $component->set('report_type', 'monthly_stats')
            ->call('generatePreview')
            ->assertSet('report_type', 'monthly_stats');
        $this->assertNotEmpty($component->get('previewData'));

        $component->set('report_type', 'doctor_performance')
            ->call('generatePreview');
        $this->assertNotEmpty($component->get('previewData'));

        $component->set('report_type', 'queue_wait_time')
            ->call('generatePreview');
        $this->assertNotEmpty($component->get('previewData'));

        $component->set('report_type', 'doctor_schedules')
            ->call('generatePreview');
        $this->assertNotEmpty($component->get('previewData'));

        $component->call('downloadExcel')->assertSuccessful();
        $component->call('downloadPdf')->assertSuccessful();
    }

    public function test_resource_list_pages_render_export_buttons(): void
    {
        // 1. Appointments list page
        $resAppts = $this->actingAs($this->admin)->get('/admin/appointments');
        $resAppts->assertStatus(200);
        $resAppts->assertSee('Ekspor Excel');
        $resAppts->assertSee('Ekspor PDF');

        // 2. Schedules list page
        $resScheds = $this->actingAs($this->admin)->get('/admin/schedules');
        $resScheds->assertStatus(200);
        $resScheds->assertSee('Ekspor Jadwal Excel');
        $resScheds->assertSee('Ekspor Jadwal PDF');

        // 3. Queue tickets list page
        $resQueues = $this->actingAs($this->admin)->get('/admin/queue-tickets');
        $resQueues->assertStatus(200);
        $resQueues->assertSee('Ekspor Antrian Excel');
        $resQueues->assertSee('Ekspor Antrian PDF');
    }
}
