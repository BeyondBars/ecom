<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $adminRole = Role::where('name', 'admin')->first();
        
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        
        $admin->roles()->attach($adminRole);
        
        // Create manager user
        $managerRole = Role::where('name', 'manager')->first();
        
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        
        $manager->roles()->attach($managerRole);
        
        // Create customer users
        $customerRole = Role::where('name', 'customer')->first();
        
        User::factory()->count(20)->create()->each(function ($user) use ($customerRole) {
            $user->roles()->attach($customerRole);
        });
    }
}
