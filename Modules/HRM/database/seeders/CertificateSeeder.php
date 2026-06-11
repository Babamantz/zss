<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        $certificates = [
            ['name' => 'Bachelor of Science in Computer Science', 'path' => 'certificates/bsc_cs.pdf'],
            ['name' => 'Master of Business Administration',       'path' => 'certificates/mba.pdf'],
            ['name' => 'Certified Public Accountant (CPA)',       'path' => 'certificates/cpa.pdf'],
            ['name' => 'Advanced Diploma in HRM',                 'path' => 'certificates/adip_hrm.pdf'],
            ['name' => 'Bachelor of Laws (LLB)',                  'path' => 'certificates/llb.pdf'],
        ];

        foreach ($certificates as $cert) {
            DB::table('certificates')->insert([
                'certificate_name'      => $cert['name'],
                'slug'                  => Str::slug($cert['name']),
            ]);
        }
    }
}
