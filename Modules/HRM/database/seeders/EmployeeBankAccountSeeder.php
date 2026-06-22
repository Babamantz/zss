<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeBankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        // employee_id 1-5, bank_id 1-7 (from BankSeeder)
        $accounts = [
            ['account_no' => '1000-2024-0001', 'employee_id' => 1, 'bank_id' => 1],
            ['account_no' => '1000-2024-0002', 'employee_id' => 2, 'bank_id' => 2],
            ['account_no' => '1000-2024-0003', 'employee_id' => 3, 'bank_id' => 3],
            ['account_no' => '1000-2024-0004', 'employee_id' => 4, 'bank_id' => 1],
            ['account_no' => '1000-2024-0005', 'employee_id' => 5, 'bank_id' => 4],
        ];

        DB::table('employee_bank_accounts')->insert($accounts);
    }
}
