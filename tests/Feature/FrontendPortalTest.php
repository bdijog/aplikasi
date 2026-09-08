<?php

namespace Tests\Feature;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Livewire\Frontend\AppointmentBooking;
use App\Models\Announcement;
use App\Models\Doctor;
use App\Models\Schedule;
use Livewire\Livewire;
use Tests\TestCase;

class FrontendPortalTest extends TestCase
{
    protected Doctor $doctor;

    protected Schedule $schedule;

    protected Announcement $announcement;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test doctor if none exists
        $this->doctor = Doctor::firstOrCreate(
            ['email' => 'testdoctor@ayosehat.id'],
            [
                'name' => 'dr. Test Dokter, Sp.PD',
                'license_number' => 'STR-12345678',
                'password' => bcrypt('password'),
                'is_active' => true,
                'specialty' => ['id' => 'Spesialis Penyakit Dalam', 'en' => 'Internal Medicine'],
                'bio' => ['id' => 'Bio dokter tes', 'en' => 'Test doctor bio'],
            ]
        );

        // Create test schedule
        $this->schedule = Schedule::firstOrCreate(
            [
                'doctor_id' => $this->doctor->id,
                'day_of_week' => 1,
            ],
            [
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'max_patients' => 20,
                'status' => ScheduleStatus::Active,
                'type' => ScheduleType::Recurring,
                'notes' => 'Poli Pagi',
            ]
        );

        // Create test announcement
        $this->announcement = Announcement::firstOrCreate(
            ['slug' => 'pengumuman-tes-portal-frontend'],
            [
                'title' => ['id' => 'Pengumuman Tes Portal', 'en' => 'Portal Test Announcement'],
                'content' => ['id' => 'Konten pengumuman tes', 'en' => 'Test announcement content'],
                'summary' => ['id' => 'Ringkasan pengumuman', 'en' => 'Announcement summary'],
                'is_active' => true,
                'published_at' => now()->subDay(),
            ]
        );
    }

    public function test_home_page_can_be_rendered(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Klinik Ayo Sehat', false);
    }

    public function test_doctor_schedule_page_can_be_rendered(): void
    {
        $response = $this->get(route('doctors.index'));
        $response->assertStatus(200);
        $response->assertSee('Klinik Ayo Sehat', false);
    }

    public function test_booking_page_can_be_rendered(): void
    {
        $response = $this->get(route('booking.index'));
        $response->assertStatus(200);
        $response->assertSee('Klinik Ayo Sehat', false);
    }

    public function test_patient_queue_page_can_be_rendered(): void
    {
        $response = $this->get(route('queue.index'));
        $response->assertStatus(200);
        $response->assertSee('Klinik Ayo Sehat', false);
    }

    public function test_queue_display_tv_page_can_be_rendered(): void
    {
        $response = $this->get(route('queue.display'));
        $response->assertStatus(200);
        $response->assertSee('KLINIK AYO SEHAT', false);
    }

    public function test_self_checkin_page_can_be_rendered(): void
    {
        $response = $this->get(route('checkin.index'));
        $response->assertStatus(200);
        $response->assertSee('Klinik Ayo Sehat', false);
    }

    public function test_announcements_index_page_can_be_rendered(): void
    {
        $response = $this->get(route('announcements.index'));
        $response->assertStatus(200);
        $response->assertSee('Klinik Ayo Sehat', false);
    }

    public function test_announcement_detail_page_can_be_rendered(): void
    {
        $response = $this->get(route('announcements.show', $this->announcement->slug));
        $response->assertStatus(200);
        $response->assertSee('Klinik Ayo Sehat', false);
    }

    public function test_locale_switcher_works(): void
    {
        $response = $this->get(route('locale.switch', 'en'));
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');

        $responseId = $this->get(route('locale.switch', 'id'));
        $responseId->assertRedirect();
        $responseId->assertSessionHas('locale', 'id');
    }

    public function test_livewire_booking_can_register_new_patient(): void
    {
        $uniqueNik = '3201'.rand(100000000000, 999999999999);

        Livewire::test(AppointmentBooking::class)
            ->set('patientMode', 'new')
            ->set('national_id', $uniqueNik)
            ->set('name', 'Budi Pasien Baru')
            ->set('date_of_birth', '1995-05-15')
            ->set('gender', 'male')
            ->set('phone', '081298765432')
            ->set('email', 'budi.'.$uniqueNik.'@test.com')
            ->set('password', 'secret123')
            ->call('registerAndProceed')
            ->assertHasNoErrors()
            ->assertSet('step', 2);

        $this->assertDatabaseHas('patients', [
            'national_id' => $uniqueNik,
            'name' => 'Budi Pasien Baru',
        ]);
    }
}
