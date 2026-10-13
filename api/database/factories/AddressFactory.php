<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => 'Home',
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => '98'.fake()->numerify('########'),
            'line1' => fake()->streetAddress(),
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'postal_code' => '400050',
            'country' => 'India',
            'is_default' => false,
        ];
    }
}
