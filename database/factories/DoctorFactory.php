<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Doctor>
     */
    protected $model = Doctor::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * List of Indonesian medical specialties with titles.
     *
     * @var array<string, string>
     */
    protected static array $specialties = [
        'Spesialis Anak' => 'Sp.A',
        'Spesialis Penyakit Dalam' => 'Sp.PD',
        'Spesialis Jantung & Pembuluh Darah' => 'Sp.JP',
        'Spesialis Obstetri & Ginekologi' => 'Sp.OG',
        'Spesialis Bedah' => 'Sp.B',
        'Spesialis Mata' => 'Sp.M',
        'Spesialis Kulit & Kelamin' => 'Sp.KK',
        'Spesialis THT-KL' => 'Sp.THT-KL',
        'Spesialis Saraf' => 'Sp.S',
        'Dokter Umum' => '',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specialtyName = array_rand(self::$specialties);
        $title = self::$specialties[$specialtyName];
        $degreeSuffix = $title ? ", {$title}" : '';

        return [
            'name' => 'dr. ' . fake()->firstName() . ' ' . fake()->lastName() . $degreeSuffix,
            'license_number' => 'STR-' . fake()->unique()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'phone' => '08' . fake()->numerify('##########'),
            'photo' => null,
            'bio' => fake()->paragraph(),
            'is_active' => true,
            'specialty' => $specialtyName,
        ];
    }

    /**
     * Indicate that the doctor is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the doctor's email is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Set a specific specialty for the doctor.
     */
    public function withSpecialty(string $specialty, ?string $degreeSuffix = null): static
    {
        return $this->state(function (array $attributes) use ($specialty, $degreeSuffix) {
            $suffix = $degreeSuffix ? ", {$degreeSuffix}" : '';
            return [
                'specialty' => $specialty,
                'name' => 'dr. ' . fake()->firstName() . ' ' . fake()->lastName() . $suffix,
            ];
        });
    }
}
