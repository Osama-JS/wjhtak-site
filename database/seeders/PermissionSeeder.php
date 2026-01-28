<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'manage users',
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Role Management
            'manage roles',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Permission Management
            'manage permissions',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',

            // Booking Management
            'view bookings',
            'manage bookings',
            'create bookings',
            'edit bookings',
            'delete bookings',
            'cancel bookings',

            // Hotel Management
            'manage hotels',
            'view hotels',
            'create hotels',
            'edit hotels',
            'delete hotels',

            // Flight Management
            'manage flights',
            'view flights',
            'create flights',
            'edit flights',
            'delete flights',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
