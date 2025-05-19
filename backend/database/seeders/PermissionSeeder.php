<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            ['name' => 'view_users', 'display_name' => 'View Users', 'description' => 'Can view users'],
            ['name' => 'create_users', 'display_name' => 'Create Users', 'description' => 'Can create users'],
            ['name' => 'update_users', 'display_name' => 'Update Users', 'description' => 'Can update users'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'description' => 'Can delete users'],

            // Role permissions
            ['name' => 'view_roles', 'display_name' => 'View Roles', 'description' => 'Can view roles'],
            ['name' => 'create_roles', 'display_name' => 'Create Roles', 'description' => 'Can create roles'],
            ['name' => 'update_roles', 'display_name' => 'Update Roles', 'description' => 'Can update roles'],
            ['name' => 'delete_roles', 'display_name' => 'Delete Roles', 'description' => 'Can delete roles'],

            // Product permissions
            ['name' => 'view_products', 'display_name' => 'View Products', 'description' => 'Can view products'],
            ['name' => 'create_products', 'display_name' => 'Create Products', 'description' => 'Can create products'],
            ['name' => 'update_products', 'display_name' => 'Update Products', 'description' => 'Can update products'],
            ['name' => 'delete_products', 'display_name' => 'Delete Products', 'description' => 'Can delete products'],

            // Category permissions
            ['name' => 'view_categories', 'display_name' => 'View Categories', 'description' => 'Can view categories'],
            ['name' => 'create_categories', 'display_name' => 'Create Categories', 'description' => 'Can create categories'],
            ['name' => 'update_categories', 'display_name' => 'Update Categories', 'description' => 'Can update categories'],
            ['name' => 'delete_categories', 'display_name' => 'Delete Categories', 'description' => 'Can delete categories'],

            // Brand permissions
            ['name' => 'view_brands', 'display_name' => 'View Brands', 'description' => 'Can view brands'],
            ['name' => 'create_brands', 'display_name' => 'Create Brands', 'description' => 'Can create brands'],
            ['name' => 'update_brands', 'display_name' => 'Update Brands', 'description' => 'Can update brands'],
            ['name' => 'delete_brands', 'display_name' => 'Delete Brands', 'description' => 'Can delete brands'],

            // Order permissions
            ['name' => 'view_orders', 'display_name' => 'View Orders', 'description' => 'Can view orders'],
            ['name' => 'create_orders', 'display_name' => 'Create Orders', 'description' => 'Can create orders'],
            ['name' => 'update_orders', 'display_name' => 'Update Orders', 'description' => 'Can update orders'],
            ['name' => 'delete_orders', 'display_name' => 'Delete Orders', 'description' => 'Can delete orders'],

            // Blog post permissions
            ['name' => 'view_blog_posts', 'display_name' => 'View Blog Posts', 'description' => 'Can view blog posts'],
            ['name' => 'create_blog_posts', 'display_name' => 'Create Blog Posts', 'description' => 'Can create blog posts'],
            ['name' => 'update_blog_posts', 'display_name' => 'Update Blog Posts', 'description' => 'Can update blog posts'],
            ['name' => 'delete_blog_posts', 'display_name' => 'Delete Blog Posts', 'description' => 'Can delete blog posts'],

            // Comment permissions
            ['name' => 'view_comments', 'display_name' => 'View Comments', 'description' => 'Can view comments'],
            ['name' => 'create_comments', 'display_name' => 'Create Comments', 'description' => 'Can create comments'],
            ['name' => 'update_comments', 'display_name' => 'Update Comments', 'description' => 'Can update comments'],
            ['name' => 'delete_comments', 'display_name' => 'Delete Comments', 'description' => 'Can delete comments'],

            // Setting permissions
            ['name' => 'view_settings', 'display_name' => 'View Settings', 'description' => 'Can view settings'],
            ['name' => 'update_settings', 'display_name' => 'Update Settings', 'description' => 'Can update settings'],

            // Invoice permissions
            ['name' => 'view_invoices', 'display_name' => 'View Invoices', 'description' => 'Can view invoices'],
            ['name' => 'create_invoices', 'display_name' => 'Create Invoices', 'description' => 'Can create invoices'],
            ['name' => 'update_invoices', 'display_name' => 'Update Invoices', 'description' => 'Can update invoices'],
            ['name' => 'delete_invoices', 'display_name' => 'Delete Invoices', 'description' => 'Can delete invoices'],
            
            // Wishlist permissions
            ['name' => 'view_wishlists', 'display_name' => 'View Wishlists', 'description' => 'Can view wishlists'],
            ['name' => 'create_wishlists', 'display_name' => 'Create Wishlists', 'description' => 'Can create wishlists'],
            ['name' => 'update_wishlists', 'display_name' => 'Update Wishlists', 'description' => 'Can update wishlists'],
            ['name' => 'delete_wishlists', 'display_name' => 'Delete Wishlists', 'description' => 'Can delete wishlists'],
            
            // Like permissions
            ['name' => 'view_likes', 'display_name' => 'View Likes', 'description' => 'Can view likes'],
            ['name' => 'delete_likes', 'display_name' => 'Delete Likes', 'description' => 'Can delete likes'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
