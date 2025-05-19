<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User permissions
            'view_users',
            'create_users',
            'update_users',
            'delete_users',
            
            // Role permissions
            'view_roles',
            'create_roles',
            'update_roles',
            'delete_roles',
            
            // Permission permissions
            'view_permissions',
            'create_permissions',
            'update_permissions',
            'delete_permissions',
            
            // Product permissions
            'view_products',
            'create_products',
            'update_products',
            'delete_products',
            
            // Category permissions
            'view_categories',
            'create_categories',
            'update_categories',
            'delete_categories',
            
            // Brand permissions
            'view_brands',
            'create_brands',
            'update_brands',
            'delete_brands',
            
            // Order permissions
            'view_orders',
            'create_orders',
            'update_orders',
            'delete_orders',
            
            // Blog post permissions
            'view_blog_posts',
            'create_blog_posts',
            'update_blog_posts',
            'delete_blog_posts',
            
            // Comment permissions
            'view_comments',
            'create_comments',
            'update_comments',
            'delete_comments',
            'moderate_comments',
            
            // Setting permissions
            'view_settings',
            'update_settings',
            
            // Invoice permissions
            'view_invoices',
            'create_invoices',
            'update_invoices',
            'delete_invoices',
            'generate_invoice_pdf',
            
            // Wishlist permissions
            'view_wishlists',
            'create_wishlists',
            'update_wishlists',
            'delete_wishlists',
            
            // Like permissions
            'view_likes',
            'create_likes',
            'update_likes',
            'delete_likes',
            
            // Notification permissions
            'view_notifications',
            'create_notifications',
            'update_notifications',
            'delete_notifications',
            'view_all_notifications',
            'update_all_notifications',
            'delete_all_notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $roles = Role::all();

        // Admin role - all permissions
        $adminRole = $roles->where('name', 'admin')->first();
        $adminRole->permissions()->attach(Permission::all());

        // Manager role - most permissions except critical ones
        $managerRole = $roles->where('name', 'manager')->first();
        $managerPermissions = Permission::whereNotIn('name', [
            'delete_users',
            'delete_roles',
            'create_permissions',
            'update_permissions',
            'delete_permissions',
        ])->get();
        $managerRole->permissions()->attach($managerPermissions);

        // Editor role - content-related permissions
        $editorRole = $roles->where('name', 'editor')->first();
        $editorPermissions = Permission::whereIn('name', [
            'view_products',
            'create_products',
            'update_products',
            'view_categories',
            'create_categories',
            'update_categories',
            'view_brands',
            'create_brands',
            'update_brands',
            'view_blog_posts',
            'create_blog_posts',
            'update_blog_posts',
            'view_comments',
            'create_comments',
            'update_comments',
            'moderate_comments',
            'view_wishlists',
            'view_likes',
            'view_notifications',
        ])->get();
        $editorRole->permissions()->attach($editorPermissions);

        // Customer role - basic permissions
        $customerRole = $roles->where('name', 'customer')->first();
        $customerPermissions = Permission::whereIn('name', [
            'view_products',
            'view_categories',
            'view_brands',
            'view_blog_posts',
            'view_comments',
            'create_comments',
            'update_comments',
            'view_wishlists',
            'create_wishlists',
            'update_wishlists',
            'delete_wishlists',
            'view_likes',
            'create_likes',
            'update_likes',
            'delete_likes',
            'view_notifications',
        ])->get();
        $customerRole->permissions()->attach($customerPermissions);
    }
}
