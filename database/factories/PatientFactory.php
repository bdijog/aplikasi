<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Patient>
     */
    protected $model = Patient::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Common drug / allergen list in Indonesian clinics.
     *
     * @var list<string>
     */
    protected static array $allergens = [
        'Paracetamol',
        'Penicillin',
        'Amoxicillin',
        'Aspirin',
        'Ibuprofen',
        'Sulfa',
        'Antibiotik Sefalosporin',
        'Debu',
        'Seafood (Udang/Kepiting)',
        'Telur',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement([Gender::Male, Gender::Female]);
        $firstName = $gender === Gender::Male ? fake()->firstNameMale() : fake()->firstNameFemale();
        $lastName = fake()->lastName();

        $hasAllergies = fake()->boolean(25);
        $allergiesList = $hasAllergies
            ? fake()->randomElements(self::$allergens, fake()->numberBetween(1, 2))
            : null;

        return [
            'medical_record_number' => 'RM-' . date('Y') . fake()->unique()->numerify('######'),
            'name' => $firstName . ' ' . $lastName,
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'date_of_birth' => fake()->dateTimeBetween('-65 years', '-1 years')->format('Y-m-d'),
            'gender' => $gender,
            'national_id' => '32' . fake()->unique()->numerify('##############'),
            'phone' => '08' . fake()->numerify('##########'),
            'address' => fake()->address(),
            'blood_type' => fake()->randomElement(['A', 'B', 'AB', 'O']),
            'allergies' => $allergiesList,
            'photo' => null,
        ];
    }

    /**
     * Set gender to Male.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => Gender::Male,
            'name' => fake()->firstNameMale() . ' ' . fake()->lastName(),
        ]);
    }

    /**
     * Set gender to Female.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => Gender::Female,
            'name' => fake()->firstNameFemale() . ' ' . fake()->lastName(),
        ]);
    }

    /**
     * Indicate that the patient's email is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
