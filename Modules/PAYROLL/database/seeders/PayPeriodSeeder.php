<?php

namespace Modules\PAYROLL\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
        $periods = [
            ['start_date' => '2024-01-01', 'end_date' => '2024-01-31', 'status' => 'Locked_Completed'],
            ['start_date' => '2024-02-01', 'end_date' => '2024-02-29', 'status' => 'Locked_Completed'],
            ['start_date' => '2024-03-01', 'end_date' => '2024-03-31', 'status' => 'Locked_Completed'],
            ['start_date' => '2024-04-01', 'end_date' => '2024-04-30', 'status' => 'Locked_Completed'],
            ['start_date' => '2024-05-01', 'end_date' => '2024-05-31', 'status' => 'Processing'],
            ['start_date' => '2024-06-01', 'end_date' => '2024-06-30', 'status' => 'Draft'],
        ];

        foreach ($periods as $period) {
            DB::table('pay_periods')->insert(array_merge($period, [
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
