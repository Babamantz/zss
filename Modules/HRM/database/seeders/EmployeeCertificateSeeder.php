<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeCertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {



        DB::table('employee_certificates')->insert([
            ['employee_id' => 1, 'certificate_id' => 2], // MBA
            ['employee_id' => 2, 'certificate_id' => 3], // CPA
            ['employee_id' => 3, 'certificate_id' => 1], // BSc CS
            ['employee_id' => 4, 'certificate_id' => 4], // Adv Diploma HRM
            ['employee_id' => 5, 'certificate_id' => 5], // LLB
        ]);
    }
}
