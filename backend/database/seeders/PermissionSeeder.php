<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'view users',
            'create users',
            'update users',
            'delete users',
            'view roles',
            'create roles',
            'update roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'update permissions',
            'delete permissions',
            'view products',
            'create products',
            'update products',
            'delete products',
            'view categories',
            'create categories',
            'update categories',
            'delete categories',
            'view orders',
            'create orders',
            'update orders',
            'delete orders',
            'view coupons',
            'create coupons',
            'update coupons',
            'delete coupons',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'customer']);
        $role->givePermissionTo([
            'view products',
            'view categories',
            'create orders',
            'view orders',
        ]);
    }
}
