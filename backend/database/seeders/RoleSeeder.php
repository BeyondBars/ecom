<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Administrator with full access to all features',
            ],
            [
                'name' => 'Manager',
                'description' => 'Manager with access to most features except user management',
            ],
            [
                'name' => 'Editor',
                'description' => 'Editor with access to content management',
            ],
            [
                'name' => 'Customer Support',
                'description' => 'Customer support with access to orders and customers',
            ],
            [
                'name' => 'Analyst',
                'description' => 'Analyst with read-only access to reports and data',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
