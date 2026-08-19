<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisabilityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Populate the master lookup table
        $types = [
            ['name' => 'Visual Impairment'],
            ['name' => 'Hearing Impairment'],
            ['name' => 'Motor / Physical Disability'],
            ['name' => 'Cognitive / Learning Disability'],
            ['name' => 'Speech / Language Impairment'],
        ];

        foreach ($types as $type) {
            DB::table('disability_types')->updateOrInsert(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
