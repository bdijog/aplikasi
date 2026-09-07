<?php

namespace Database\Factories;

use App\Models\ServiceCounter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceCounter>
 */
class ServiceCounterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ServiceCounter>
     */
    protected $model = ServiceCounter::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $counterTypes = [
            'Poli Anak' => 'Pediatric Clinic',
            'Poli Penyakit Dalam' => 'Internal Medicine Clinic',
            'Poli Gigi' => 'Dental Clinic',
            'Poli Kandungan' => 'Obstetrics Clinic',
            'Poli Mata' => 'Eye Clinic',
            'Poli THT' => 'ENT Clinic',
            'Loket Registrasi' => 'Registration Counter',
            'Loket Pembayaran' => 'Cashier Counter',
        ];

        $typeNameId = array_rand($counterTypes);
        $typeNameEn = $counterTypes[$typeNameId];
        $number = fake()->numberBetween(1, 4);

        $floor = fake()->numberBetween(1, 3);
        $building = fake()->randomElement(['A', 'B', 'Utama']);
        $buildingEn = $building === 'Utama' ? 'Main' : $building;

        return [
            'name' => [
                'id' => "{$typeNameId} {$number}",
                'en' => "{$typeNameEn} {$number}",
            ],
            'code' => strtoupper(fake()->unique()->bothify('CTR-??-##')),
            'location' => [
                'id' => "Lantai {$floor} Gedung {$building}",
                'en' => "Floor {$floor} Building {$buildingEn}",
            ],
            'is_active' => true,
        ];
    }

    /**
     * State for inactive service counter.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
