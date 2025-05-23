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
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            BlogPostSeeder::class,
            CommentSeeder::class,
            SettingSeeder::class,
            InvoiceSeeder::class,
            WishlistSeeder::class,
            LikeSeeder::class,
            NotificationSeeder::class,
            ReviewSeeder::class,
            CouponSeeder::class,
            EmailTemplateSeeder::class,
        ]);
    }
}
