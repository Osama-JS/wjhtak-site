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
            // User & Access Management
            'manage users', 'view users', 'create users', 'edit users', 'delete users',
            'manage roles', 'view roles', 'create roles', 'edit roles', 'delete roles',
            'manage permissions', 'view permissions',

            // Trip Management
            'manage trips', 'view trips', 'create trips', 'edit trips', 'delete trips',
            'manage trip_categories', 'manage trip_itinerary',

            // Booking Management
            'manage bookings', 'view bookings', 'edit bookings', 'delete bookings',
            'cancel bookings', 'upload tickets', 'send tickets',

            // Financial Management
            'view payments', 'manage payments',
            'view bank_transfers', 'approve bank_transfers', 'reject bank_transfers',

            // Content Management
            'manage banners', 'manage pages',
            'manage locations', 'manage countries', 'manage cities',
            'manage companies', 'manage company_codes',

            // Communication & Feedback
            'view subscribers', 'manage subscribers',
            'view notifications', 'send notifications', 'delete notifications',
            'view questions', 'manage questions',

            // System Settings
            'manage settings', 'view dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
