<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        $designations = [
            'Director General',
            'Director',
            'Manager',
            'Senior Officer',
            'Officer',
            'Assistant Officer',
            'Intern',
        ];

        foreach ($designations as $d) {
            DB::table('designations')->insert([
                'designation_name' => $d,
                'slug'             => Str::slug($d),
            ]);
        }
    }
}
