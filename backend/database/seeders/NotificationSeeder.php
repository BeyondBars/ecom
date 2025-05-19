<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Create 5-15 notifications for each user
            $count = rand(5, 15);
            
            // Create info notifications
            Notification::factory()
                ->count(rand(1, 5))
                ->info()
                ->for($user)
                ->create();
            
            // Create success notifications
            Notification::factory()
                ->count(rand(1, 3))
                ->success()
                ->for($user)
                ->create();
            
            // Create warning notifications
            Notification::factory()
                ->count(rand(1, 3))
                ->warning()
                ->for($user)
                ->create();
            
            // Create error notifications
            Notification::factory()
                ->count(rand(1, 3))
                ->error()
                ->for($user)
                ->create();
            
            // Ensure some notifications are unread
            Notification::factory()
                ->count(rand(3, 7))
                ->unread()
                ->for($user)
                ->create();
        }
    }
}
