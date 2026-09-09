<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\QueueTicketStatus;
use App\Filament\Widgets\ActiveDoctorQueuesTableWidget;
use App\Filament\Widgets\AnnualVisitsTrendChart;
use App\Filament\Widgets\AppointmentTrendChart;
use App\Filament\Widgets\DoctorVisitRankTableWidget;
use App\Filament\Widgets\MonthlyComparisonChart;
use App\Filament\Widgets\ServiceEfficiencyOverview;
use App\Filament\Widgets\TodayStatsOverview;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Schedule;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_widget_test@klinik.test'],
            [
                'name' => 'Admin Test Widget',
                'password' => bcrypt('password'),
            ]
        );

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admin_dashboard_can_be_rendered_with_widgets(): void
    {
        $response = $this->actingAs($this->admin)->withSession(['locale' => 'id'])->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Hari Ini');
        $response->assertSee('Efisiensi Layanan');
        $response->assertSee('Antrian Aktif per Dokter');
        $response->assertSee('Tren Appointment 7 Hari Terakhir');
        $response->assertSee('Perbandingan Bulanan');
        $response->assertSee('Tren Kunjungan Tahunan');
        $response->assertSee('Dokter Paling Banyak Kunjungan');
    }

    public function test_today_stats_overview_widget_renders_stats_accurately(): void
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create(['is_active' => true]);
        $schedule = Schedule::factory()->create([
            'doctor_id' => $doctor->id,
            'day_of_week' => now()->dayOfWeek,
        ]);

        // Create appointment today
        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_code' => 'BK-WIDGET-01',
            'appointment_date' => now()->format('Y-m-d'),
            'status' => AppointmentStatus::Confirmed,
            'chief_complaint' => 'Widget test complaint',
        ]);

        // Create waiting queue ticket
        QueueTicket::create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'queue_date' => now()->format('Y-m-d'),
            'queue_number' => 999,
            'prefix' => 'A',
            'display_number' => 'A-999',
            'status' => QueueTicketStatus::Waiting,
        ]);

        Livewire::actingAs($this->admin)
            ->test(TodayStatsOverview::class)
            ->assertSuccessful()
            ->assertSee('Total Janji Temu Hari Ini')
            ->assertSee('Antrean Aktif Sekarang')
            ->assertSee('Pasien Sudah Dilayani')
            ->assertSee('No-Show Hari Ini')
            ->assertSee('Dokter Aktif Hari Ini');
    }

    public function test_service_efficiency_overview_widget_renders_metrics(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ServiceEfficiencyOverview::class)
            ->assertSuccessful()
            ->assertSee('Rata-rata Waktu Tunggu')
            ->assertSee('Rata-rata Durasi Konsultasi')
            ->assertSee('Tingkat No-Show');
    }

    public function test_appointment_trend_chart_widget_renders_and_returns_valid_data(): void
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create(['is_active' => true]);
        $schedule = Schedule::factory()->create(['doctor_id' => $doctor->id]);

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_code' => 'BK-CHART-01',
            'appointment_date' => now()->format('Y-m-d'),
            'status' => AppointmentStatus::Completed,
            'chief_complaint' => 'Chart line test',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AppointmentTrendChart::class)
            ->assertSuccessful()
            ->assertSee('Tren Appointment 7 Hari Terakhir');

        $widget = new AppointmentTrendChart;
        $this->assertSame(1, AppointmentTrendChart::getSort());
        $this->assertSame('line', invade($widget)->getType());

        $data = invade($widget)->getData();
        $this->assertArrayHasKey('datasets', $data);
        $this->assertArrayHasKey('labels', $data);
        $this->assertCount(2, $data['datasets']);
        $this->assertCount(7, $data['labels']);
        $this->assertSame('Total Janji Temu', $data['datasets'][0]['label']);
        $this->assertSame('Hadir / Selesai', $data['datasets'][1]['label']);
        $this->assertGreaterThanOrEqual(1, end($data['datasets'][0]['data']));
    }

    public function test_annual_visits_trend_chart_widget_renders_and_returns_valid_data(): void
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create(['is_active' => true]);
        $schedule = Schedule::factory()->create(['doctor_id' => $doctor->id]);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_code' => 'BK-ANNUAL-01',
            'appointment_date' => now()->format('Y-m-d'),
            'status' => AppointmentStatus::Confirmed,
            'chief_complaint' => 'Annual test',
        ]);

        QueueTicket::create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'queue_date' => now()->format('Y-m-d'),
            'queue_number' => 888,
            'prefix' => 'B',
            'display_number' => 'B-888',
            'status' => QueueTicketStatus::Completed,
        ]);

        Livewire::actingAs($this->admin)
            ->test(AnnualVisitsTrendChart::class)
            ->assertSuccessful()
            ->assertSee('Tren Kunjungan Tahunan');

        $widget = new AnnualVisitsTrendChart;
        $this->assertSame(3, AnnualVisitsTrendChart::getSort());
        $this->assertSame('full', $widget->getColumnSpan());
        $this->assertSame('line', invade($widget)->getType());

        $data = invade($widget)->getData();
        $this->assertArrayHasKey('datasets', $data);
        $this->assertArrayHasKey('labels', $data);
        $this->assertCount(2, $data['datasets']);
        $this->assertCount(12, $data['labels']);
        $this->assertSame('Kunjungan Pasien (Antrean)', $data['datasets'][0]['label']);
        $this->assertSame('Janji Temu Terdaftar', $data['datasets'][1]['label']);
    }

    public function test_monthly_comparison_chart_widget_renders_and_handles_filters(): void
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create(['is_active' => true]);
        $schedule = Schedule::factory()->create(['doctor_id' => $doctor->id]);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_code' => 'BK-MONTHLY-01',
            'appointment_date' => now()->format('Y-m-d'),
            'status' => AppointmentStatus::Confirmed,
            'chief_complaint' => 'Monthly test',
        ]);

        QueueTicket::create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'queue_date' => now()->format('Y-m-d'),
            'queue_number' => 777,
            'prefix' => 'C',
            'display_number' => 'C-777',
            'status' => QueueTicketStatus::Completed,
        ]);

        Livewire::actingAs($this->admin)
            ->test(MonthlyComparisonChart::class)
            ->assertSuccessful()
            ->assertSee('Perbandingan Bulanan')
            ->set('filter', 'queue')
            ->assertSuccessful();

        $widget = new MonthlyComparisonChart;
        $this->assertSame(2, MonthlyComparisonChart::getSort());
        $this->assertSame('bar', invade($widget)->getType());

        $data = invade($widget)->getData();
        $this->assertArrayHasKey('datasets', $data);
        $this->assertArrayHasKey('labels', $data);
        $this->assertCount(2, $data['datasets']);
        $this->assertCount(5, $data['labels']);

        // Test with queue filter
        $widget->filter = 'queue';
        $queueData = invade($widget)->getData();
        $this->assertCount(2, $queueData['datasets']);
        $this->assertCount(5, $queueData['labels']);
    }

    public function test_doctor_visit_rank_table_widget_renders_and_ranks_doctors(): void
    {
        $doctor = Doctor::factory()->create([
            'name' => 'dr. Ranking Specialist Test',
            'is_active' => true,
        ]);
        $patient = Patient::factory()->create();
        $schedule = Schedule::factory()->create(['doctor_id' => $doctor->id]);

        for ($i = 1; $i <= 5; $i++) {
            Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'schedule_id' => $schedule->id,
                'booking_code' => "BK-RANK-0{$i}",
                'appointment_date' => now()->format('Y-m-d'),
                'status' => AppointmentStatus::Completed,
                'chief_complaint' => 'Ranking test visit',
            ]);
        }

        $lastAppointment = Appointment::where('doctor_id', $doctor->id)->first();

        QueueTicket::create([
            'appointment_id' => $lastAppointment->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'queue_date' => now()->format('Y-m-d'),
            'queue_number' => 101,
            'prefix' => 'R',
            'display_number' => 'R-101',
            'status' => QueueTicketStatus::Completed,
            'served_at' => now()->subMinutes(15),
            'completed_at' => now(),
        ]);

        Livewire::actingAs($this->admin)
            ->test(DoctorVisitRankTableWidget::class)
            ->assertSuccessful()
            ->assertSee('Dokter Paling Banyak Kunjungan')
            ->assertSee('dr. Ranking Specialist Test')
            ->assertSee('Pasien')
            ->assertSee('15 mnt');
    }

    public function test_active_doctor_queues_table_widget_renders_live_status(): void
    {
        $doctor = Doctor::factory()->create([
            'name' => 'dr. Queue Active Test',
            'is_active' => true,
        ]);
        $patient = Patient::factory()->create();
        $schedule = Schedule::factory()->create(['doctor_id' => $doctor->id]);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_code' => 'BK-LIVE-01',
            'appointment_date' => now()->format('Y-m-d'),
            'status' => AppointmentStatus::InProgress,
            'chief_complaint' => 'Live queue test',
        ]);

        QueueTicket::create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'queue_date' => now()->format('Y-m-d'),
            'queue_number' => 202,
            'prefix' => 'Q',
            'display_number' => 'Q-202',
            'status' => QueueTicketStatus::Serving,
            'counter' => 'Loket 1',
            'called_at' => now(),
            'served_at' => now(),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ActiveDoctorQueuesTableWidget::class)
            ->assertSuccessful()
            ->assertSee('Antrian Aktif per Dokter')
            ->assertSee('dr. Queue Active Test')
            ->assertSee('Q-202')
            ->assertSee('Sedang Melayani');
    }

    public function test_widgets_have_correct_sort_and_polling_intervals(): void
    {
        $todayWidget = new TodayStatsOverview;
        $this->assertSame(-2, TodayStatsOverview::getSort());
        $this->assertSame('15s', invade($todayWidget)->getPollingInterval());

        $efficiencyWidget = new ServiceEfficiencyOverview;
        $this->assertSame(-1, ServiceEfficiencyOverview::getSort());
        $this->assertSame('60s', invade($efficiencyWidget)->getPollingInterval());

        $activeQueueWidget = new ActiveDoctorQueuesTableWidget;
        $this->assertSame(0, ActiveDoctorQueuesTableWidget::getSort());
        $this->assertSame('10s', invade($activeQueueWidget)->getPollingInterval());

        $appointmentChart = new AppointmentTrendChart;
        $this->assertSame(1, AppointmentTrendChart::getSort());
        $this->assertSame('30s', invade($appointmentChart)->getPollingInterval());

        $monthlyChart = new MonthlyComparisonChart;
        $this->assertSame(2, MonthlyComparisonChart::getSort());
        $this->assertSame('60s', invade($monthlyChart)->getPollingInterval());

        $annualChart = new AnnualVisitsTrendChart;
        $this->assertSame(3, AnnualVisitsTrendChart::getSort());
        $this->assertNull(invade($annualChart)->getPollingInterval());

        $rankWidget = new DoctorVisitRankTableWidget;
        $this->assertSame(4, DoctorVisitRankTableWidget::getSort());
        $this->assertSame('60s', invade($rankWidget)->getPollingInterval());
    }

    public function test_admin_dashboard_can_be_rendered_in_english(): void
    {
        $response = $this->actingAs($this->admin)->withSession(['locale' => 'en'])->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Today Summary');
        $response->assertSee('Service Efficiency');
        $response->assertSee('Active Queues by Doctor');
        $response->assertSee('Appointment Trend Last 7 Days');
        $response->assertSee('Monthly Comparison');
        $response->assertSee('Annual Visits Trend');
        $response->assertSee('Most Visited Doctors');
    }

    public function test_widgets_can_render_content_in_english(): void
    {
        app()->setLocale('en');

        Livewire::actingAs($this->admin)
            ->test(TodayStatsOverview::class)
            ->assertSuccessful()
            ->assertSee('Today Total Appointments')
            ->assertSee('Active Queues Now')
            ->assertSee('Patients Served')
            ->assertSee('Today No-Shows')
            ->assertSee('Active Doctors Today');

        Livewire::actingAs($this->admin)
            ->test(ServiceEfficiencyOverview::class)
            ->assertSuccessful()
            ->assertSee('Average Wait Time')
            ->assertSee('Average Consultation Duration')
            ->assertSee('No-Show Rate');

        Livewire::actingAs($this->admin)
            ->test(AppointmentTrendChart::class)
            ->assertSuccessful()
            ->assertSee('Appointment Trend Last 7 Days');

        Livewire::actingAs($this->admin)
            ->test(MonthlyComparisonChart::class)
            ->assertSuccessful()
            ->assertSee('Monthly Comparison');

        Livewire::actingAs($this->admin)
            ->test(AnnualVisitsTrendChart::class)
            ->assertSuccessful()
            ->assertSee('Annual Visits Trend');

        Livewire::actingAs($this->admin)
            ->test(DoctorVisitRankTableWidget::class)
            ->assertSuccessful()
            ->assertSee('Most Visited Doctors');

        Livewire::actingAs($this->admin)
            ->test(ActiveDoctorQueuesTableWidget::class)
            ->assertSuccessful()
            ->assertSee('Active Queues by Doctor');
    }
}
