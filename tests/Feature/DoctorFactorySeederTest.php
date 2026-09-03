<?php

namespace Tests\Feature;

use App\Models\Doctor;
use Database\Seeders\DoctorSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DoctorFactorySeederTest extends TestCase
{
    use DatabaseTransactions;

    public function test_doctor_factory_creates_valid_doctor(): void
    {
        $doctor = Doctor::factory()->create();

        $this->assertNotNull($doctor->id);
        $this->assertStringStartsWith('dr. ', $doctor->name);
        $this->assertStringStartsWith('STR-', $doctor->license_number);
        $this->assertNotEmpty($doctor->email);
        $this->assertNotEmpty($doctor->specialty);
        $this->assertTrue($doctor->is_active);
        $this->assertNotNull($doctor->email_verified_at);
    }

    public function test_doctor_factory_states(): void
    {
        $inactive = Doctor::factory()->inactive()->create();
        $this->assertFalse($inactive->is_active);

        $unverified = Doctor::factory()->unverified()->create();
        $this->assertNull($unverified->email_verified_at);

        $specialist = Doctor::factory()->withSpecialty('Spesialis Anak', 'Sp.A')->create();
        $this->assertEquals('Spesialis Anak', $specialist->specialty);
        $this->assertStringEndsWith('Sp.A', $specialist->name);
    }

    public function test_doctor_seeder_seeds_doctors_successfully(): void
    {
        $this->seed(DoctorSeeder::class);

        $this->assertDatabaseHas('doctors', [
            'email' => 'sarah.wijaya@klinik.test',
            'specialty' => 'Spesialis Anak',
        ]);

        $this->assertDatabaseHas('doctors', [
            'email' => 'budi.santoso@klinik.test',
            'specialty' => 'Spesialis Penyakit Dalam',
        ]);

        $this->assertDatabaseHas('doctors', [
            'email' => 'maya.kartika@klinik.test',
            'specialty' => 'Spesialis Obstetri & Ginekologi',
        ]);

        $this->assertDatabaseHas('doctors', [
            'email' => 'dimas.wicaksono@klinik.test',
            'specialty' => 'Dokter Umum',
        ]);

        // At least 6 curated + 4 random doctors
        $this->assertGreaterThanOrEqual(10, Doctor::count());
    }
}
