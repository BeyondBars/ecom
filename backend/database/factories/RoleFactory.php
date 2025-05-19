<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
        ];
    }

    /**
     * Indicate that the role is admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'admin',
            'description' => 'Administrator with full access',
        ]);
    }

    /**
     * Indicate that the role is manager.
     */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'manager',
            'description' => 'Manager with limited administrative access',
        ]);
    }

    /**
     * Indicate that the role is customer.
     */
    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'customer',
            'description' => 'Regular customer',
        ]);
    }
}
