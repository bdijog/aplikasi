<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\QueueTicketStatus;
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
        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Hari Ini');
        $response->assertSee('Efisiensi Layanan');
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

    public function test_widgets_have_correct_sort_and_polling_intervals(): void
    {
        $todayWidget = new TodayStatsOverview;
        $this->assertSame(-2, TodayStatsOverview::getSort());
        $this->assertSame('15s', invade($todayWidget)->getPollingInterval());

        $efficiencyWidget = new ServiceEfficiencyOverview;
        $this->assertSame(-1, ServiceEfficiencyOverview::getSort());
        $this->assertSame('60s', invade($efficiencyWidget)->getPollingInterval());
    }
}
