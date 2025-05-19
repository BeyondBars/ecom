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
            ['name' => 'view-users', 'display_name' => 'View Users', 'description' => 'Can view users'],
            ['name' => 'create-users', 'display_name' => 'Create Users', 'description' => 'Can create users'],
            ['name' => 'update-users', 'display_name' => 'Update Users', 'description' => 'Can update users'],
            ['name' => 'delete-users', 'display_name' => 'Delete Users', 'description' => 'Can delete users'],
            
            // Role permissions
            ['name' => 'view-roles', 'display_name' => 'View Roles', 'description' => 'Can view roles'],
            ['name' => 'create-roles', 'display_name' => 'Create Roles', 'description' => 'Can create roles'],
            ['name' => 'update-roles', 'display_name' => 'Update Roles', 'description' => 'Can update roles'],
            ['name' => 'delete-roles', 'display_name' => 'Delete Roles', 'description' => 'Can delete roles'],
            
            // Permission permissions
            ['name' => 'view-permissions', 'display_name' => 'View Permissions', 'description' => 'Can view permissions'],
            
            // Product permissions
            ['name' => 'view-products', 'display_name' => 'View Products', 'description' => 'Can view products'],
            ['name' => 'create-products', 'display_name' => 'Create Products', 'description' => 'Can create products'],
            ['name' => 'update-products', 'display_name' => 'Update Products', 'description' => 'Can update products'],
            ['name' => 'delete-products', 'display_name' => 'Delete Products', 'description' => 'Can delete products'],
            
            // Category permissions
            ['name' => 'view-categories', 'display_name' => 'View Categories', 'description' => 'Can view categories'],
            ['name' => 'create-categories', 'display_name' => 'Create Categories', 'description' => 'Can create categories'],
            ['name' => 'update-categories', 'display_name' => 'Update Categories', 'description' => 'Can update categories'],
            ['name' => 'delete-categories', 'display_name' => 'Delete Categories', 'description' => 'Can delete categories'],
            
            // Brand permissions
            ['name' => 'view-brands', 'display_name' => 'View Brands', 'description' => 'Can view brands'],
            ['name' => 'create-brands', 'display_name' => 'Create Brands', 'description' => 'Can create brands'],
            ['name' => 'update-brands', 'display_name' => 'Update Brands', 'description' => 'Can update brands'],
            ['name' => 'delete-brands', 'display_name' => 'Delete Brands', 'description' => 'Can delete brands'],
            
            // Order permissions
            ['name' => 'view-orders', 'display_name' => 'View Orders', 'description' => 'Can view orders'],
            ['name' => 'create-orders', 'display_name' => 'Create Orders', 'description' => 'Can create orders'],
            ['name' => 'update-orders', 'display_name' => 'Update Orders', 'description' => 'Can update orders'],
            ['name' => 'delete-orders', 'display_name' => 'Delete Orders', 'description' => 'Can delete orders'],
            
            // Blog post permissions
            ['name' => 'view-blog-posts', 'display_name' => 'View Blog Posts', 'description' => 'Can view blog posts'],
            ['name' => 'create-blog-posts', 'display_name' => 'Create Blog Posts', 'description' => 'Can create blog posts'],
            ['name' => 'update-blog-posts', 'display_name' => 'Update Blog Posts', 'description' => 'Can update blog posts'],
            ['name' => 'delete-blog-posts', 'display_name' => 'Delete Blog Posts', 'description' => 'Can delete blog posts'],
            
            // Comment permissions
            ['name' => 'view-comments', 'display_name' => 'View Comments', 'description' => 'Can view comments'],
            ['name' => 'create-comments', 'display_name' => 'Create Comments', 'description' => 'Can create comments'],
            ['name' => 'update-comments', 'display_name' => 'Update Comments', 'description' => 'Can update comments'],
            ['name' => 'delete-comments', 'display_name' => 'Delete Comments', 'description' => 'Can delete comments'],
            ['name' => 'moderate-comments', 'display_name' => 'Moderate Comments', 'description' => 'Can moderate comments'],
            
            // Setting permissions
            ['name' => 'view-settings', 'display_name' => 'View Settings', 'description' => 'Can view settings'],
            ['name' => 'update-settings', 'display_name' => 'Update Settings', 'description' => 'Can update settings'],
            
            // Invoice permissions
            ['name' => 'view-invoices', 'display_name' => 'View Invoices', 'description' => 'Can view invoices'],
            ['name' => 'create-invoices', 'display_name' => 'Create Invoices', 'description' => 'Can create invoices'],
            ['name' => 'update-invoices', 'display_name' => 'Update Invoices', 'description' => 'Can update invoices'],
            ['name' => 'delete-invoices', 'display_name' => 'Delete Invoices', 'description' => 'Can delete invoices'],
            
            // Wishlist permissions
            ['name' => 'view-wishlists', 'display_name' => 'View Wishlists', 'description' => 'Can view wishlists'],
            ['name' => 'create-wishlists', 'display_name' => 'Create Wishlists', 'description' => 'Can create wishlists'],
            ['name' => 'update-wishlists', 'display_name' => 'Update Wishlists', 'description' => 'Can update wishlists'],
            ['name' => 'delete-wishlists', 'display_name' => 'Delete Wishlists', 'description' => 'Can delete wishlists'],
            
            // Like permissions
            ['name' => 'view-likes', 'display_name' => 'View Likes', 'description' => 'Can view likes'],
            ['name' => 'create-likes', 'display_name' => 'Create Likes', 'description' => 'Can create likes'],
            ['name' => 'delete-likes', 'display_name' => 'Delete Likes', 'description' => 'Can delete likes'],
            ['name' => 'like-products', 'display_name' => 'Like Products', 'description' => 'Can like products'],
            ['name' => 'like-blog-posts', 'display_name' => 'Like Blog Posts', 'description' => 'Can like blog posts'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
