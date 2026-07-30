<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\Division;
use Modules\HRM\Models\Unit;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        $divisions = [
            ['name' => 'Auditor', 'slug' => 'auditor', 'department_slug' => ''],
            ['name' => 'Public Relations', 'slug' => 'pr', 'department_slug' => ''],
            ['name' => 'Legal Unit', 'slug' => 'legal', 'department_slug' => ''],
            ['name' => 'Information and Communication Technology', 'slug' => 'ict', 'department_slug' => ''],
        ];

        foreach ($divisions as $division) {
            $department = Department::where('slug', $division['department_slug'])->first();

            Division::updateOrCreate(
                ['slug' => $division['slug']],
                [
                    'name' => $division['name'],
                    'department_id' => $department ? $department->id : null, // Corrected logic
                ]
            );
        }
    }
}
