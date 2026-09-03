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
        $counterTypes = ['Poli Anak', 'Poli Penyakit Dalam', 'Poli Gigi', 'Poli Kandungan', 'Poli Mata', 'Poli THT', 'Loket Registrasi', 'Loket Pembayaran'];
        $typeName = fake()->randomElement($counterTypes);
        $number = fake()->numberBetween(1, 4);

        return [
            'name' => "{$typeName} {$number}",
            'code' => strtoupper(fake()->unique()->bothify('CTR-??-##')),
            'location' => 'Lantai ' . fake()->numberBetween(1, 3) . ' Gedung ' . fake()->randomElement(['A', 'B', 'Utama']),
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
