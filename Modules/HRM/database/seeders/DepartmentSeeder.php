<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRM\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        $departments = [
            [
                'name' => 'Planning',
                'slug' => 'planning',
            ],
            [
                'name' => 'Human Resource Managemeent ',
                'slug' => 'hrm',
            ],
            [
                'name' => 'Real Estate and Marketing',
                'slug' => 'rem',
            ],
            [
                'name' => 'Construction and Investment',
                'slug' => 'ci',
            ],
            [
                'name' => 'Pemba-Office',
                'slug' => 'pemba-office',
            ]
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['slug' => $department['slug']], // Check for existing record
                ['name' => trim($department['name'])] // Ensure name is clean
            );
        }
    }
}
