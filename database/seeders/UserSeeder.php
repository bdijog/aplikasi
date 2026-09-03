<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@klinik.test',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Petugas Loket 1',
                'email' => 'loket1@klinik.test',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Direktur Klinik',
                'email' => 'direktur@klinik.test',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
