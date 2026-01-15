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
            ['name' => 'Auditor', 'slug' => 'auditor', 'department_slug' => ''],
            ['name' => 'Public Relations', 'slug' => 'pr', 'department_slug' => ''],
            ['name' => 'Legal Unit', 'slug' => 'legal', 'department_slug' => ''],
            ['name' => 'Information and Communication Technology', 'slug' => 'ict', 'department_slug' => ''],
        ];

        foreach ($units as $unit) {
            $department = Department::where('slug', $unit['department_slug'])->first();

            Unit::updateOrCreate(
                ['slug' => $unit['slug']],
                [
                    'name' => $unit['name'],
                    'department_id' => $department ? $department->id : null, // Corrected logic
                ]
            );
        }
    }
}
