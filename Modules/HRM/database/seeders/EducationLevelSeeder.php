<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        DB::table('education_levels')->insert([
            [
                'course_name'          => 'Master of Business Administration',
                'certificate_name'     => 'MBA',
                'holder_certificate_no' => 'MBA-2008-0042',
                'certificate_file'     => 'edu/emp1_mba.pdf',
                'employee_id'          => 1,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'course_name'          => 'Bachelor of Commerce - Accounting',
                'certificate_name'     => 'BCom',
                'holder_certificate_no' => 'BCOM-2013-0187',
                'certificate_file'     => 'edu/emp2_bcom.pdf',
                'employee_id'          => 2,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'course_name'          => 'Bachelor of Science in Computer Science',
                'certificate_name'     => 'BSc CS',
                'holder_certificate_no' => 'BSC-2010-0091',
                'certificate_file'     => 'edu/emp3_bsc.pdf',
                'employee_id'          => 3,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'course_name'          => 'Advanced Diploma in Human Resource Management',
                'certificate_name'     => 'Adv. Dip. HRM',
                'holder_certificate_no' => 'DIPHRM-2016-0055',
                'certificate_file'     => 'edu/emp4_adip.pdf',
                'employee_id'          => 4,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'course_name'          => 'Doctor of Philosophy in Law',
                'certificate_name'     => 'PhD Law',
                'holder_certificate_no' => 'PHD-2003-0011',
                'certificate_file'     => 'edu/emp5_phd.pdf',
                'employee_id'          => 5,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
        ]);
    }
}
