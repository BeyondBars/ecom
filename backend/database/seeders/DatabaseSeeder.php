<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            InvoiceSeeder::class,
            SettingSeeder::class,
            BannerSeeder::class,
            BlogPostSeeder::class,
            CommentSeeder::class,
            WishlistSeeder::class,
            LikeSeeder::class,
        ]);
    }
}
