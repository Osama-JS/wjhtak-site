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

        // Admin gets most permissions except role/permission management
        $admin->givePermissionTo([
            'view dashboard',
            'manage users', 'view users', 'create users', 'edit users',
            'view roles',
            'manage trips', 'view trips', 'create trips', 'edit trips',
            'manage trip_categories', 'manage trip_itinerary',
            'manage bookings', 'view bookings', 'edit bookings', 'cancel bookings', 'upload tickets', 'send tickets',
            'view payments', 'view bank_transfers', 'approve bank_transfers', 'reject bank_transfers',
            'manage banners', 'manage pages', 'manage locations', 'manage countries', 'manage cities',
            'manage companies', 'manage company_codes',
            'view subscribers', 'manage subscribers',
            'view notifications', 'send notifications',
            'view questions', 'manage questions',
            'manage settings',
        ]);

        // Manager gets operational permissions
        $manager->givePermissionTo([
            'view dashboard',
            'view trips', 'manage trips',
            'view bookings', 'manage bookings', 'upload tickets',
            'view bank_transfers',
            'view subscribers',
            'send notifications',
        ]);
    }
}
