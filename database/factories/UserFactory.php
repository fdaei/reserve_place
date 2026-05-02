<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake('fa_IR')->firstName(),
            'family' => fake('fa_IR')->lastName(),
            'national_code' => fake()->numerify('##########'),
            'birth_day' => fake()->date('Y-m-d', '-18 years'),
            'phone' => '09'.fake()->unique()->numerify('#########'),
            'profile_image' => '',
        ];
    }
}
