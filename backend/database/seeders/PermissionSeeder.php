<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User permissions
            ['name' => 'view_users', 'description' => 'View users'],
            ['name' => 'create_users', 'description' => 'Create users'],
            ['name' => 'update_users', 'description' => 'Update users'],
            ['name' => 'delete_users', 'description' => 'Delete users'],
            
            // Role permissions
            ['name' => 'view_roles', 'description' => 'View roles'],
            ['name' => 'create_roles', 'description' => 'Create roles'],
            ['name' => 'update_roles', 'description' => 'Update roles'],
            ['name' => 'delete_roles', 'description' => 'Delete roles'],
            
            // Permission permissions
            ['name' => 'view_permissions', 'description' => 'View permissions'],
            ['name' => 'create_permissions', 'description' => 'Create permissions'],
            ['name' => 'update_permissions', 'description' => 'Update permissions'],
            ['name' => 'delete_permissions', 'description' => 'Delete permissions'],
            
            // Product permissions
            ['name' => 'view_products', 'description' => 'View products'],
            ['name' => 'create_products', 'description' => 'Create products'],
            ['name' => 'update_products', 'description' => 'Update products'],
            ['name' => 'delete_products', 'description' => 'Delete products'],
            
            // Category permissions
            ['name' => 'view_categories', 'description' => 'View categories'],
            ['name' => 'create_categories', 'description' => 'Create categories'],
            ['name' => 'update_categories', 'description' => 'Update categories'],
            ['name' => 'delete_categories', 'description' => 'Delete categories'],
            
            // Brand permissions
            ['name' => 'view_brands', 'description' => 'View brands'],
            ['name' => 'create_brands', 'description' => 'Create brands'],
            ['name' => 'update_brands', 'description' => 'Update brands'],
            ['name' => 'delete_brands', 'description' => 'Delete brands'],
            
            // Order permissions
            ['name' => 'view_orders', 'description' => 'View orders'],
            ['name' => 'create_orders', 'description' => 'Create orders'],
            ['name' => 'update_orders', 'description' => 'Update orders'],
            ['name' => 'delete_orders', 'description' => 'Delete orders'],
            
            // Blog post permissions
            ['name' => 'view_blog_posts', 'description' => 'View blog posts'],
            ['name' => 'create_blog_posts', 'description' => 'Create blog posts'],
            ['name' => 'update_blog_posts', 'description' => 'Update blog posts'],
            ['name' => 'delete_blog_posts', 'description' => 'Delete blog posts'],
            
            // Comment permissions
            ['name' => 'view_comments', 'description' => 'View comments'],
            ['name' => 'create_comments', 'description' => 'Create comments'],
            ['name' => 'update_comments', 'description' => 'Update comments'],
            ['name' => 'delete_comments', 'description' => 'Delete comments'],
            ['name' => 'approve_comments', 'description' => 'Approve comments'],
            
            // Setting permissions
            ['name' => 'view_settings', 'description' => 'View settings'],
            ['name' => 'update_settings', 'description' => 'Update settings'],
            
            // Invoice permissions
            ['name' => 'view_invoices', 'description' => 'View invoices'],
            ['name' => 'create_invoices', 'description' => 'Create invoices'],
            ['name' => 'update_invoices', 'description' => 'Update invoices'],
            ['name' => 'delete_invoices', 'description' => 'Delete invoices'],
            ['name' => 'generate_invoices', 'description' => 'Generate invoices'],
            
            // Wishlist permissions
            ['name' => 'view_wishlists', 'description' => 'View wishlists'],
            ['name' => 'create_wishlists', 'description' => 'Create wishlists'],
            ['name' => 'update_wishlists', 'description' => 'Update wishlists'],
            ['name' => 'delete_wishlists', 'description' => 'Delete wishlists'],
            
            // Like permissions
            ['name' => 'view_likes', 'description' => 'View likes'],
            ['name' => 'create_likes', 'description' => 'Create likes'],
            ['name' => 'delete_likes', 'description' => 'Delete likes'],
            
            // Notification permissions
            ['name' => 'view_notifications', 'description' => 'View notifications'],
            ['name' => 'create_notifications', 'description' => 'Create notifications'],
            ['name' => 'update_notifications', 'description' => 'Update notifications'],
            ['name' => 'delete_notifications', 'description' => 'Delete notifications'],
            
            // Review permissions
            ['name' => 'view_reviews', 'description' => 'View reviews'],
            ['name' => 'create_reviews', 'description' => 'Create reviews'],
            ['name' => 'update_reviews', 'description' => 'Update reviews'],
            ['name' => 'delete_reviews', 'description' => 'Delete reviews'],
            ['name' => 'approve_reviews', 'description' => 'Approve reviews'],
            ['name' => 'feature_reviews', 'description' => 'Feature reviews'],
            ['name' => 'view_unapproved_reviews', 'description' => 'View unapproved reviews'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
