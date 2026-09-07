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
                'specialty' => [
                    'id' => 'Spesialis Anak',
                    'en' => 'Pediatrician',
                ],
                'bio' => [
                    'id' => 'Dokter spesialis anak dengan keahlian tumbuh kembang balita dan imunisasi dasar lengkap.',
                    'en' => 'Pediatrician specializing in toddler growth, child development, and comprehensive childhood immunization.',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'dr. Budi Santoso, Sp.PD',
                'license_number' => 'STR-3101890124',
                'email' => 'budi.santoso@klinik.test',
                'phone' => '081234567802',
                'specialty' => [
                    'id' => 'Spesialis Penyakit Dalam',
                    'en' => 'Internal Medicine Specialist',
                ],
                'bio' => [
                    'id' => 'Dokter spesialis penyakit dalam dengan fokus pada manajemen diabetes, hipertensi, dan gangguan metabolisme.',
                    'en' => 'Internal medicine specialist focusing on diabetes management, hypertension, and metabolic disorders.',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'dr. Maya Kartika, Sp.OG',
                'license_number' => 'STR-3101890125',
                'email' => 'maya.kartika@klinik.test',
                'phone' => '081234567803',
                'specialty' => [
                    'id' => 'Spesialis Obstetri & Ginekologi',
                    'en' => 'Obstetrician & Gynecologist',
                ],
                'bio' => [
                    'id' => 'Melayani pemeriksaan antenatal (ANC), USG 4D, konsultasi program hamil, dan kesehatan reproduksi wanita.',
                    'en' => 'Providing antenatal care (ANC), 4D ultrasound, fertility program consultation, and women reproductive health services.',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'dr. Hendra Setiawan, Sp.JP',
                'license_number' => 'STR-3101890126',
                'email' => 'hendra.setiawan@klinik.test',
                'phone' => '081234567804',
                'specialty' => [
                    'id' => 'Spesialis Jantung & Pembuluh Darah',
                    'en' => 'Cardiologist & Vascular Specialist',
                ],
                'bio' => [
                    'id' => 'Keahlian dalam pemeriksaan EKG, echocardiography, konsultasi jantung koroner, dan rehabilitasi jantung.',
                    'en' => 'Expertise in ECG examinations, echocardiography, coronary artery disease consultation, and cardiac rehabilitation.',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'dr. Rina Anggraini, Sp.M',
                'license_number' => 'STR-3101890127',
                'email' => 'rina.anggraini@klinik.test',
                'phone' => '081234567805',
                'specialty' => [
                    'id' => 'Spesialis Mata',
                    'en' => 'Ophthalmologist',
                ],
                'bio' => [
                    'id' => 'Melayani pemeriksaan refraksi mata, katarak, glaukoma, serta gangguan penglihatan pada anak dan dewasa.',
                    'en' => 'Providing eye refraction examinations, cataract, glaucoma management, and vision care for children and adults.',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'dr. Dimas Wicaksono',
                'license_number' => 'STR-3101890128',
                'email' => 'dimas.wicaksono@klinik.test',
                'phone' => '081234567806',
                'specialty' => [
                    'id' => 'Dokter Umum',
                    'en' => 'General Practitioner',
                ],
                'bio' => [
                    'id' => 'Dokter umum untuk pelayanan rawat jalan tingkat pertama, konsultasi kesehatan umum, dan medical check-up.',
                    'en' => 'General practitioner providing primary outpatient care, general health consultation, and medical check-ups.',
                ],
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
