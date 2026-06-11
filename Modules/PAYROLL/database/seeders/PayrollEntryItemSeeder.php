<?php

namespace Modules\PAYROLL\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayrollEntryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        // payroll_entry ids: period 1 emp1=1, emp2=2 ... emp5=5; period 2 emp1=6 ... emp5=10
        // component_id: Basic=1, Housing=2, Transport=3, Responsibility=4
        //               PAYE=6, ZSSF=7, Health=8, Salary Advance=9, Cooperative=10

        $componentSnapshots = [
            1  => 'Basic Salary',
            2  => 'Housing Allowance',
            3  => 'Transport Allowance',
            4  => 'Responsibility Allowance',
            6  => 'PAYE Tax',
            7  => 'ZSSF Contribution',
            8  => 'Health Insurance',
            9  => 'Salary Advance Recovery',
            10 => 'Cooperative Deduction',
        ];

        // [employee_index_1based => [component_id => amount]]
        $salaryData = [
            1 => [1 => 1800000, 2 => 450000, 3 => 80000, 4 => 200000, 6 => 270000, 7 => 180000, 8 => 54000],
            2 => [1 => 1500000, 2 => 375000, 3 => 80000, 6 => 225000, 7 => 150000, 8 => 45000, 9 => 50000],
            3 => [1 => 1600000, 2 => 400000, 3 => 80000, 6 => 240000, 7 => 160000, 8 => 48000],
            4 => [1 => 1200000, 2 => 300000, 3 => 80000, 6 => 180000, 7 => 120000, 8 => 36000, 10 => 30000],
            5 => [1 => 2500000, 2 => 625000, 3 => 80000, 6 => 375000, 7 => 250000, 8 => 75000],
        ];

        $rows = [];
        $entryId = 1; // payroll_entries were inserted in order: period1 emp1..5, period2 emp1..5

        foreach ([1, 2] as $periodIdx) {
            foreach (range(1, 5) as $empId) {
                foreach ($salaryData[$empId] as $compId => $amount) {
                    $rows[] = [
                        'payroll_entry_id'       => $entryId,
                        'component_id'           => $compId,
                        'component_name_snapshot' => $componentSnapshots[$compId],
                        'finalized_amount'        => $amount,
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ];
                }
                $entryId++;
            }
        }

        DB::table('payroll_entry_items')->insert($rows);
    }
}
