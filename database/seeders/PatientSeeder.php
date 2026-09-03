<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        $curatedPatients = [
            [
                'medical_record_number' => 'RM-2026000001',
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@pasien.test',
                'date_of_birth' => '1990-05-15',
                'gender' => Gender::Male,
                'national_id' => '3201011505900001',
                'phone' => '081298765401',
                'address' => 'Jl. Merdeka No. 12, Kebayoran Baru, Jakarta Selatan',
                'blood_type' => 'O',
                'allergies' => ['Paracetamol'],
            ],
            [
                'medical_record_number' => 'RM-2026000002',
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@pasien.test',
                'date_of_birth' => '1995-11-20',
                'gender' => Gender::Female,
                'national_id' => '3201012011950002',
                'phone' => '081298765402',
                'address' => 'Jl. Melati Blok C3 No. 8, Depok',
                'blood_type' => 'A',
                'allergies' => ['Penicillin', 'Seafood (Udang/Kepiting)'],
            ],
            [
                'medical_record_number' => 'RM-2026000003',
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@pasien.test',
                'date_of_birth' => '1985-03-08',
                'gender' => Gender::Female,
                'national_id' => '3201010803850003',
                'phone' => '081298765403',
                'address' => 'Jl. Cempaka Putih Timur No. 44, Jakarta Pusat',
                'blood_type' => 'B',
                'allergies' => null,
            ],
            [
                'medical_record_number' => 'RM-2026000004',
                'name' => 'Rizky Pratama',
                'email' => 'rizky.pratama@pasien.test',
                'date_of_birth' => '2000-09-12',
                'gender' => Gender::Male,
                'national_id' => '3201011209000004',
                'phone' => '081298765404',
                'address' => 'Komplek Perumahan Indah Blok B5, Bekasi',
                'blood_type' => 'AB',
                'allergies' => ['Amoxicillin'],
            ],
            [
                'medical_record_number' => 'RM-2026000005',
                'name' => 'Bambang Sudibyo',
                'email' => 'bambang.sudibyo@pasien.test',
                'date_of_birth' => '1962-07-25',
                'gender' => Gender::Male,
                'national_id' => '3201012507620005',
                'phone' => '081298765405',
                'address' => 'Jl. Veteran No. 19, Bogor',
                'blood_type' => 'O',
                'allergies' => ['Aspirin'],
            ],
        ];

        foreach ($curatedPatients as $patientData) {
            Patient::updateOrCreate(
                ['email' => $patientData['email']],
                array_merge($patientData, [
                    'password' => $defaultPassword,
                    'email_verified_at' => now(),
                ])
            );
        }

        // 10 additional patients via factory
        Patient::factory(10)->create();
    }
}
