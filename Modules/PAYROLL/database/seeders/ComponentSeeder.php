<?php

namespace Modules\PAYROLL\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $components = [
            'PBZ-LOAN',
            'AZANIA-LOAN',
            'PBA-SACCOS',
            'ZNZ-SACCOSS',
            'HOUSING-ALLOWANCE',
            'TRANSPORT-ALLOWANCE',
            'RESPONSIBILITY-ALLOWANCE',
            'OVERTIME-ALLOWANCE',
            'PAYE-TAX',
            'ZSSF-CONTRIBUTION',
            'HEALTH-INSURANCE',
            'COOPERATIVE-DEDUCTION'
        ];

        // Explicitly define types for each specific component item
        $types = [
            'PBZ-LOAN'                 => 'Deduction',
            'AZANIA-LOAN'              => 'Deduction',
            'PBA-SACCOS'               => 'Deduction',
            'ZNZ-SACCOSS'              => 'Deduction',
            'HOUSING-ALLOWANCE'        => 'Earning',
            'TRANSPORT-ALLOWANCE'      => 'Earning',
            'RESPONSIBILITY-ALLOWANCE' => 'Earning',
            'OVERTIME-ALLOWANCE'       => 'Earning',
            'PAYE-TAX'                 => 'Deduction',
            'ZSSF-CONTRIBUTION'        => 'Deduction',
            'HEALTH-INSURANCE'         => 'Deduction',
            'COOPERATIVE-DEDUCTION'    => 'Deduction',
        ];

        $rows = [];

        foreach ($components as $comp) {
            $rows[] = [
                // Str::headline converts 'PBZ-LOAN' into nicely formatted 'PBZ Loan'
                'name'       => \Illuminate\Support\Str::headline(strtolower($comp)),
                'type'       => $types[$comp],
                'code'       => strtoupper($comp), // Keeps codes uniform, unique, and predictable
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('components')->insert($rows);
    }
}
