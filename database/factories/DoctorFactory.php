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
     * List of Indonesian medical specialties with titles and English translations.
     *
     * @var array<string, array{suffix: string, en: string}>
     */
    protected static array $specialties = [
        'Spesialis Anak' => ['suffix' => 'Sp.A', 'en' => 'Pediatrician'],
        'Spesialis Penyakit Dalam' => ['suffix' => 'Sp.PD', 'en' => 'Internal Medicine Specialist'],
        'Spesialis Jantung & Pembuluh Darah' => ['suffix' => 'Sp.JP', 'en' => 'Cardiologist & Vascular Specialist'],
        'Spesialis Obstetri & Ginekologi' => ['suffix' => 'Sp.OG', 'en' => 'Obstetrician & Gynecologist'],
        'Spesialis Bedah' => ['suffix' => 'Sp.B', 'en' => 'General Surgeon'],
        'Spesialis Mata' => ['suffix' => 'Sp.M', 'en' => 'Ophthalmologist'],
        'Spesialis Kulit & Kelamin' => ['suffix' => 'Sp.KK', 'en' => 'Dermatologist & Venereologist'],
        'Spesialis THT-KL' => ['suffix' => 'Sp.THT-KL', 'en' => 'ENT Specialist'],
        'Spesialis Saraf' => ['suffix' => 'Sp.S', 'en' => 'Neurologist'],
        'Dokter Umum' => ['suffix' => '', 'en' => 'General Practitioner'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specialtyName = array_rand(self::$specialties);
        $meta = self::$specialties[$specialtyName];
        $degreeSuffix = $meta['suffix'] ? ", {$meta['suffix']}" : '';

        return [
            'name' => 'dr. '.fake()->firstName().' '.fake()->lastName().$degreeSuffix,
            'license_number' => 'STR-'.fake()->unique()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'phone' => '08'.fake()->numerify('##########'),
            'photo' => null,
            'bio' => [
                'id' => fake('id_ID')->paragraph(),
                'en' => fake('en_US')->paragraph(),
            ],
            'is_active' => true,
            'specialty' => [
                'id' => $specialtyName,
                'en' => $meta['en'],
            ],
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
    public function withSpecialty(string $specialty, ?string $degreeSuffix = null, ?string $enSpecialty = null): static
    {
        return $this->state(function (array $attributes) use ($specialty, $degreeSuffix, $enSpecialty) {
            $suffix = $degreeSuffix ? ", {$degreeSuffix}" : '';

            return [
                'specialty' => [
                    'id' => $specialty,
                    'en' => $enSpecialty ?? (self::$specialties[$specialty]['en'] ?? $specialty),
                ],
                'name' => 'dr. '.fake()->firstName().' '.fake()->lastName().$suffix,
            ];
        });
    }
}
