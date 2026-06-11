<?php

namespace Modules\PAYROLL\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeFinanceProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        // component_id: Basic=1, Housing=2, Transport=3, Responsibility=4, Overtime=5
        //               PAYE=6, ZSSF=7, Health=8, Salary Advance=9, Cooperative=10
        // All global components (1,2,3,6,7,8) are assigned to every employee
        // Plus individual non-global components as needed

        $globalComponents = [1, 2, 3, 6, 7, 8]; // is_global = true

        $globalAmounts = [
            1 => [1800000, 1500000, 1600000, 1200000, 2500000], // Basic per employee
            2 => [450000,  375000,  400000,  300000,  625000],  // Housing 25% of basic
            3 => [80000,   80000,   80000,   80000,   80000],   // Transport flat
            6 => [270000,  225000,  240000,  180000,  375000],  // PAYE ~15%
            7 => [180000,  150000,  160000,  120000,  250000],  // ZSSF 10%
            8 => [54000,   45000,   48000,   36000,   75000],   // Health 3%
        ];

        $rows = [];
        foreach (range(1, 5) as $empId) {
            foreach ($globalComponents as $compId) {
                $rows[] = [
                    'employee_id'   => $empId,
                    'component_id'  => $compId,
                    'custom_amount' => $globalAmounts[$compId][$empId - 1],
                    'is_recurring'  => true,
                    'ends_at'       => null,
                    'created_by'    => 1,
                    'updated_by'    => 1,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }

        // Non-global individual components
        $rows[] = ['employee_id' => 1, 'component_id' => 4, 'custom_amount' => 200000, 'is_recurring' => true,  'ends_at' => null,         'created_by' => 1, 'updated_by' => 1, 'created_at' => now(), 'updated_at' => now()]; // Responsibility
        $rows[] = ['employee_id' => 3, 'component_id' => 5, 'custom_amount' => 120000, 'is_recurring' => false, 'ends_at' => '2024-12-31', 'created_by' => 1, 'updated_by' => 1, 'created_at' => now(), 'updated_at' => now()]; // Overtime
        $rows[] = ['employee_id' => 2, 'component_id' => 9, 'custom_amount' => 50000,  'is_recurring' => true,  'ends_at' => '2025-06-30', 'created_by' => 1, 'updated_by' => 1, 'created_at' => now(), 'updated_at' => now()]; // Salary advance
        $rows[] = ['employee_id' => 4, 'component_id' => 10, 'custom_amount' => 30000,  'is_recurring' => true,  'ends_at' => null,         'created_by' => 2, 'updated_by' => 2, 'created_at' => now(), 'updated_at' => now()]; // Cooperative

        DB::table('employee_components')->insert($rows);
    }
}
