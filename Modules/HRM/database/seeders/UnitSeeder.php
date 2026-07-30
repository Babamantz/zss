<?php

namespace Modules\HRM\Database\Seeders;

use Modules\HRM\Models\Unit;
use Illuminate\Database\Seeder;
use Modules\HRM\Models\Department;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        $units = [
            ['name' => 'Auditor', 'slug' => 'auditor'],
            ['name' => 'Public Relations', 'slug' => 'pr'],
            ['name' => 'Legal Unit', 'slug' => 'legal'],
            ['name' => 'Information and Communication Technology', 'slug' => 'ict'],
        ];

        foreach ($units as $unit) {

            Unit::updateOrCreate(
                ['slug' => $unit['slug']],
                [
                    'name' => $unit['name'],
                ]
            );
        }
    }
}
