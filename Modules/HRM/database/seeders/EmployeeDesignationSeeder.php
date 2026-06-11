<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeDesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
        // identification_id: NIDA=1, ZanID=2, Passport=3, Driving=4, Birth Cert=5
        DB::table('employee_identifications')->insert([
            ['employee_id' => 1, 'identification_id' => 1],
            ['employee_id' => 1, 'identification_id' => 2],
            ['employee_id' => 2, 'identification_id' => 1],
            ['employee_id' => 2, 'identification_id' => 3],
            ['employee_id' => 3, 'identification_id' => 1],
            ['employee_id' => 4, 'identification_id' => 2],
            ['employee_id' => 5, 'identification_id' => 1],
            ['employee_id' => 5, 'identification_id' => 4],
        ]);
    }
}
