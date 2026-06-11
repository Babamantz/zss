<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IdentificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        $ids = [
            ['name' => 'National ID (NIDA)'],
            ['name' => 'Zanzibar Residents ID (ZRCS)'],
            ['name' => 'Passport'],                    
            ['name' => 'Driving Licence'],             
            ['name' => 'Birth Certificate'],           
        ];

        foreach ($ids as $item) {
            DB::table('identifications')->insert([
                'identification_name'      => $item['name'],
                'slug'                     => Str::slug($item['name']),
            ]);
        }
    }
}
