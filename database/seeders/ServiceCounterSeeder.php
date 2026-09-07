<?php

namespace Database\Seeders;

use App\Models\ServiceCounter;
use Illuminate\Database\Seeder;

class ServiceCounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $counters = [
            [
                'code' => 'LOKET-01',
                'name' => [
                    'id' => 'Loket Pendaftaran 1',
                    'en' => 'Registration Counter 1',
                ],
                'location' => [
                    'id' => 'Lantai 1 - Lobby Utama',
                    'en' => '1st Floor - Main Lobby',
                ],
                'is_active' => true,
            ],
            [
                'code' => 'LOKET-02',
                'name' => [
                    'id' => 'Loket Pendaftaran 2',
                    'en' => 'Registration Counter 2',
                ],
                'location' => [
                    'id' => 'Lantai 1 - Lobby Utama',
                    'en' => '1st Floor - Main Lobby',
                ],
                'is_active' => true,
            ],
            [
                'code' => 'POLI-ANAK-01',
                'name' => [
                    'id' => 'Poli Anak',
                    'en' => 'Pediatric Clinic',
                ],
                'location' => [
                    'id' => 'Lantai 2 - Sayap Timur Gedung A',
                    'en' => '2nd Floor - East Wing Building A',
                ],
                'is_active' => true,
            ],
            [
                'code' => 'POLI-PD-01',
                'name' => [
                    'id' => 'Poli Penyakit Dalam',
                    'en' => 'Internal Medicine Clinic',
                ],
                'location' => [
                    'id' => 'Lantai 2 - Sayap Barat Gedung A',
                    'en' => '2nd Floor - West Wing Building A',
                ],
                'is_active' => true,
            ],
            [
                'code' => 'POLI-OBGYN-01',
                'name' => [
                    'id' => 'Poli Kandungan & Kebidanan',
                    'en' => 'Obstetrics & Gynecology Clinic',
                ],
                'location' => [
                    'id' => 'Lantai 3 - Gedung B',
                    'en' => '3rd Floor - Building B',
                ],
                'is_active' => true,
            ],
            [
                'code' => 'POLI-JP-01',
                'name' => [
                    'id' => 'Poli Jantung & Pembuluh Darah',
                    'en' => 'Cardiology & Vascular Clinic',
                ],
                'location' => [
                    'id' => 'Lantai 3 - Gedung B',
                    'en' => '3rd Floor - Building B',
                ],
                'is_active' => true,
            ],
            [
                'code' => 'POLI-MATA-01',
                'name' => [
                    'id' => 'Poli Mata',
                    'en' => 'Eye Clinic (Ophthalmology)',
                ],
                'location' => [
                    'id' => 'Lantai 2 - Gedung B',
                    'en' => '2nd Floor - Building B',
                ],
                'is_active' => true,
            ],
            [
                'code' => 'POLI-UMUM-01',
                'name' => [
                    'id' => 'Poli Umum',
                    'en' => 'General Outpatient Clinic',
                ],
                'location' => [
                    'id' => 'Lantai 1 - Gedung A',
                    'en' => '1st Floor - Building A',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($counters as $counter) {
            ServiceCounter::updateOrCreate(
                ['code' => $counter['code']],
                $counter
            );
        }
    }
}
