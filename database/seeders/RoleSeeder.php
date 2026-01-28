<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);

        // Super Admin gets all permissions
        $superAdmin->givePermissionTo(Permission::all());

        // Admin gets most permissions except permission management
        $admin->givePermissionTo([
            'manage users',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles',
            'view roles',
            'view bookings',
            'manage bookings',
            'create bookings',
            'edit bookings',
            'delete bookings',
            'cancel bookings',
            'manage hotels',
            'view hotels',
            'create hotels',
            'edit hotels',
            'delete hotels',
            'manage flights',
            'view flights',
            'create flights',
            'edit flights',
            'delete flights',
        ]);

        // Manager gets limited permissions
        $manager->givePermissionTo([
            'view users',
            'view roles',
            'view bookings',
            'manage bookings',
            'create bookings',
            'edit bookings',
            'view hotels',
            'view flights',
        ]);
    }
}
