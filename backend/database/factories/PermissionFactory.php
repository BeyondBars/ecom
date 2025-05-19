<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = ['view', 'create', 'update', 'delete'];
        $resources = ['users', 'roles', 'products', 'categories', 'orders', 'invoices', 'blog-posts', 'comments'];
        
        $action = $this->faker->randomElement($actions);
        $resource = $this->faker->randomElement($resources);
        
        return [
            'name' => "{$action}-{$resource}",
            'description' => "Ability to {$action} {$resource}",
        ];
    }
}
