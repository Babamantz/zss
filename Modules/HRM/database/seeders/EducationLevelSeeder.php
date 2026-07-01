<?php

namespace Modules\HRM\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationLevels = [
            'certificate',
            'form-iv',
            'diploma',
            'advance_diploma',
            'bachelor',
            'master',
            'phd',
        ];

        // Prepare the data with timestamps
        $data = array_map(function ($level) {
            return [
                'name' => $level,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $educationLevels);

        // Bulk insert into the database
        DB::table('education_levels')->insert($data);
    }
}
