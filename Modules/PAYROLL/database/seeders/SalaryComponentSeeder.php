<?php

namespace Modules\PAYROLL\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalaryComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
        // type: 'Earning' or 'Deduction'
        // is_global: true = applies to all employees automatically
        $components = [
            // Earnings
            ['name' => 'Basic Salary',              'type' => 'Earning',   'is_global' => true],
            ['name' => 'Housing Allowance',         'type' => 'Earning',   'is_global' => true],
            ['name' => 'Transport Allowance',       'type' => 'Earning',   'is_global' => true],
            ['name' => 'Responsibility Allowance',  'type' => 'Earning',   'is_global' => false],
            ['name' => 'Overtime Pay',              'type' => 'Earning',   'is_global' => false],
            // Deductions
            ['name' => 'PAYE Tax',                  'type' => 'Deduction', 'is_global' => true],
            ['name' => 'ZSSF Contribution',         'type' => 'Deduction', 'is_global' => true],
            ['name' => 'Health Insurance',          'type' => 'Deduction', 'is_global' => true],
            ['name' => 'Salary Advance Recovery',   'type' => 'Deduction', 'is_global' => false],
            ['name' => 'Cooperative Deduction',     'type' => 'Deduction', 'is_global' => false],
        ];

        foreach ($components as $comp) {
            DB::table('salary_components')->insert(array_merge($comp, [
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
