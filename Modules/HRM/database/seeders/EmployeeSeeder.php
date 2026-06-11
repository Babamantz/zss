<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        $employees = [
            [
                'opf_number'     => 'OPF-2024-001',
                'is_disable' =>  false,
                'is_active'      => 'active',
                'education'      => 'Master',
                'gender'         => 'male',
                'marital_status' => 'married',
                'contacts'   => json_encode(['phone' => null, 'alt_phone' => null, 'address' => null]),
                'dob'            => '1980-03-15',
                'hired_date'     => '2010-01-05',
                'retiring_date'  => '2040-03-15',
                'is_hr_registered'  => true,
                'department_id'  => 1,
                'unit_id'        => 1,
                'designation_id' => 1,
                'user_id'        => 1,
                'created_by'     => 1,
                'updated_by'     => 1,
            ],
            [
                'opf_number'     => 'OPF-2024-002',
                'is_disable'   =>  false,
                'is_active'      => 'active',
                'education'      => 'Bachelor',
                'gender'         => 'female',
                'marital_status' => 'single',
                'contacts'   => json_encode(['phone' => null, 'alt_phone' => null, 'address' => null]),
                'dob'            => '1990-07-22',
                'hired_date'     => '2015-06-01',
                'retiring_date'  => '2050-07-22',
                'is_hr_registered'  => true,
                'department_id'  => 2,
                'unit_id'        => 3,
                'designation_id' => 2,
                'user_id'        => 2,
                'created_by'     => 1,
                'updated_by'     => 1,
            ],
            [
                'opf_number'     => 'OPF-2024-003',
                'is_disable'     => false,
                'is_active'      => 'active',
                'education'      => 'Bachelor',
                'gender'         => 'male',
                'marital_status' => 'married',
                'contacts'   => json_encode(['phone' => null, 'alt_phone' => null, 'address' => null]),
                'dob'            => '1985-11-10',
                'hired_date'     => '2012-03-20',
                'retiring_date'  => '2045-11-10',
                'is_hr_registered'  => true,
                'department_id'  => 3,
                'unit_id'        => 3,
                'designation_id' => 3,
                'user_id'        => 3,
                'created_by'     => 1,
                'updated_by'     => 1,
            ],
            [
                'opf_number'     => 'OPF-2024-004',
                'is_disable'     => false,
                'is_active'      => 'active',
                'education'      => 'Advance Diploma',
                'gender'         => 'female',
                'marital_status' => 'divorced',
                'contacts'   => json_encode(['phone' => null, 'alt_phone' => null, 'address' => null]),
                'dob'            => '1992-05-30',
                'hired_date'     => '2018-09-15',
                'retiring_date'  => '2052-05-30',
                'is_hr_registered'  => true,
                'department_id'  => 4,
                'unit_id'        => 3,
                'designation_id' => 4,
                'user_id'        =>4 ,
                'created_by'     => 2,
                'updated_by'     => 2,
            ],
            [
                'opf_number'     => 'OPF-2024-005',
                'is_disable'     => false,
                'is_active'      => 'in-active',
                'education'      => 'Phd',
                'gender'         => 'male',
                'marital_status' => 'married',
                'contacts'   => json_encode(['phone' => null, 'alt_phone' => null, 'address' => null]),
                'dob'            => '1975-01-18',
                'hired_date'     => '2005-07-01',
                'retiring_date'  => '2035-01-18',
                'is_hr_registered'  => false,
                'department_id'  => 4,
                'unit_id'        => 4,
                'designation_id' => 2,
                'user_id'        => 5,
                'created_by'     => 2,
                'updated_by'     => 2,
            ],
        ];

        foreach ($employees as $emp) {
            DB::table('employees')->insert(array_merge($emp, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
