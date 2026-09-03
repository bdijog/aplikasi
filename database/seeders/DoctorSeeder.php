<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        $doctors = [
            [
                'name' => 'dr. Sarah Wijaya, Sp.A',
                'license_number' => 'STR-3101890123',
                'email' => 'sarah.wijaya@klinik.test',
                'phone' => '081234567801',
                'specialty' => 'Spesialis Anak',
                'bio' => 'Dokter spesialis anak dengan keahlian tumbuh kembang balita dan imunisasi dasar lengkap.',
                'is_active' => true,
            ],
            [
                'name' => 'dr. Budi Santoso, Sp.PD',
                'license_number' => 'STR-3101890124',
                'email' => 'budi.santoso@klinik.test',
                'phone' => '081234567802',
                'specialty' => 'Spesialis Penyakit Dalam',
                'bio' => 'Dokter spesialis penyakit dalam dengan fokus pada manajemen diabetes, hipertensi, dan gangguan metabolisme.',
                'is_active' => true,
            ],
            [
                'name' => 'dr. Maya Kartika, Sp.OG',
                'license_number' => 'STR-3101890125',
                'email' => 'maya.kartika@klinik.test',
                'phone' => '081234567803',
                'specialty' => 'Spesialis Obstetri & Ginekologi',
                'bio' => 'Melayani pemeriksaan antenatal (ANC), USG 4D, konsultasi program hamil, dan kesehatan reproduksi wanita.',
                'is_active' => true,
            ],
            [
                'name' => 'dr. Hendra Setiawan, Sp.JP',
                'license_number' => 'STR-3101890126',
                'email' => 'hendra.setiawan@klinik.test',
                'phone' => '081234567804',
                'specialty' => 'Spesialis Jantung & Pembuluh Darah',
                'bio' => 'Keahlian dalam pemeriksaan EKG, echocardiography, konsultasi jantung koroner, dan rehabilitasi jantung.',
                'is_active' => true,
            ],
            [
                'name' => 'dr. Rina Anggraini, Sp.M',
                'license_number' => 'STR-3101890127',
                'email' => 'rina.anggraini@klinik.test',
                'phone' => '081234567805',
                'specialty' => 'Spesialis Mata',
                'bio' => 'Melayani pemeriksaan refraksi mata, katarak, glaukoma, serta gangguan penglihatan pada anak dan dewasa.',
                'is_active' => true,
            ],
            [
                'name' => 'dr. Dimas Wicaksono',
                'license_number' => 'STR-3101890128',
                'email' => 'dimas.wicaksono@klinik.test',
                'phone' => '081234567806',
                'specialty' => 'Dokter Umum',
                'bio' => 'Dokter umum untuk pelayanan rawat jalan tingkat pertama, konsultasi kesehatan umum, dan medical check-up.',
                'is_active' => true,
            ],
        ];

        foreach ($doctors as $doctorData) {
            Doctor::updateOrCreate(
                ['email' => $doctorData['email']],
                array_merge($doctorData, [
                    'password' => $defaultPassword,
                    'email_verified_at' => now(),
                ])
            );
        }

        // Create 4 additional random doctors via factory
        Doctor::factory(4)->create();
    }
}
