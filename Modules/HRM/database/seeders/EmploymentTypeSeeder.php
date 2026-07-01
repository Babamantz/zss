<?php

namespace Modules\HRM\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employmentTypes = ['Permanent', 'Contract', 'Casual', 'Intern'];

        $data = array_map(function ($type) {
            return [
                'name' => $type,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $employmentTypes);

        DB::table('employment_types')->insert($data);
    }
}
