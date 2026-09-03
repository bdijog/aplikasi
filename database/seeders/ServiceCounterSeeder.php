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
            ['name' => 'Loket Pendaftaran 1', 'code' => 'LOKET-01', 'location' => 'Lantai 1 - Lobby Utama', 'is_active' => true],
            ['name' => 'Loket Pendaftaran 2', 'code' => 'LOKET-02', 'location' => 'Lantai 1 - Lobby Utama', 'is_active' => true],
            ['name' => 'Poli Anak 1', 'code' => 'POLI-ANAK-01', 'location' => 'Lantai 2 - Sayap Timur Gedung A', 'is_active' => true],
            ['name' => 'Poli Penyakit Dalam 1', 'code' => 'POLI-PD-01', 'location' => 'Lantai 2 - Sayap Barat Gedung A', 'is_active' => true],
            ['name' => 'Poli Kandungan & Kebidanan', 'code' => 'POLI-OBGYN-01', 'location' => 'Lantai 3 - Gedung B', 'is_active' => true],
            ['name' => 'Poli Jantung & Pembuluh Darah', 'code' => 'POLI-JP-01', 'location' => 'Lantai 3 - Gedung B', 'is_active' => true],
            ['name' => 'Poli Mata', 'code' => 'POLI-MATA-01', 'location' => 'Lantai 2 - Gedung B', 'is_active' => true],
            ['name' => 'Poli Umum', 'code' => 'POLI-UMUM-01', 'location' => 'Lantai 1 - Gedung A', 'is_active' => true],
        ];

        foreach ($counters as $counter) {
            ServiceCounter::updateOrCreate(
                ['code' => $counter['code']],
                $counter
            );
        }
    }
}
