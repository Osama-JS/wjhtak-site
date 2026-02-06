<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::firstOrCreate(
            ['email' => 'info@wjhtak.com'],
            [
                'name' => 'وجهتك للسياحة',
                'phone' => '0555555555',
                'notes' => 'الرياض - الملز',
                'active' => true,
            ]
        );

        Company::firstOrCreate(
            ['email' => 'contact@sauditravel.com'],
            [
                'name' => 'سفريات المملكة',
                'phone' => '0544444444',
                'notes' => 'جدة - التحلية',
                'active' => true,
            ]
        );

        Company::firstOrCreate(
            ['email' => 'elite@trips.com'],
            [
                'name' => 'رحلات النخبة',
                'phone' => '0533333333',
                'notes' => 'الدمام - الشاطئ',
                'active' => true,
            ]
        );
    }
}
