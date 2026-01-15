<?php

namespace Modules\HRM\Database\Seeders;

use Modules\HRM\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);


        $banks = [
            [
                'name' => 'National Microfinance Bank',
                'slug' => 'NMB'
            ],
            [
                'name' => 'Peoples Bank Of Zanzibar',
                'slug' => 'PBZ'
            ],
            [
                'name' => 'National Bank of Commerce',
                'slug' => 'NBC'
            ],
            [
                'name' => 'Cooperative and Rural Development Bank',
                'slug' => 'NMB'
            ],
        ];
        foreach ($banks as $bank) {

            Bank::updateOrCReate(
                [
                    'name' => $bank['name'],
                    'slug' => $bank['slug']
                ]
            );
        }
    }
}
