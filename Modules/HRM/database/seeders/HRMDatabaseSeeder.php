<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;

class HRMDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            BankSeeder::class,
            DepartmentSeeder::class,
            // DesignationSeeder::class,
            UnitSeeder::class
        ]);
    }
}
