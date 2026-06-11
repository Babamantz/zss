<?php

namespace Modules\PAYROLL\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayrollEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
        $salaries = [
            1 => [1800000, 450000, 80000, 270000, 180000, 54000],
            2 => [1500000, 375000, 80000, 225000, 150000, 45000],
            3 => [1600000, 400000, 80000, 240000, 160000, 48000],
            4 => [1200000, 300000, 80000, 180000, 120000, 36000],
            5 => [2500000, 625000, 80000, 375000, 250000, 75000],
        ];

        // emp 1 also has responsibility 200000; emp 2 has advance deduction 50000; emp 4 has cooperative 30000

        $extras = [
            1 => ['earning' => 200000, 'deduction' => 0],
            2 => ['earning' => 0,      'deduction' => 50000],
            3 => ['earning' => 0,      'deduction' => 0],
            4 => ['earning' => 0,      'deduction' => 30000],
            5 => ['earning' => 0,      'deduction' => 0],
        ];

        foreach ([1, 2] as $payPeriodId) {
            foreach (range(1, 5) as $empId) {
                [$basic, $housing, $transport, $paye, $zssf, $health] = $salaries[$empId];
                $gross      = $basic + $housing + $transport + $extras[$empId]['earning'];
                $deductions = $paye + $zssf + $health + $extras[$empId]['deduction'];
                $net        = $gross - $deductions;

                DB::table('payroll_entries')->insert([
                    'employee_id'   => $empId,
                    'pay_period_id' => $payPeriodId,
                    'total_gross'   => $gross,
                    'total_deductions' => $deductions,
                    'net_pay'       => $net,
                    'processed_at'  => now()->subMonths(6 - $payPeriodId),
                    'processed_by'  => 3, // Finance Officer
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}
