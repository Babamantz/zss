<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\HRM\Database\Seeders\BankSeeder;
use Modules\HRM\Database\Seeders\CertificateSeeder;
use Modules\HRM\Database\Seeders\DepartmentSeeder;
use Modules\HRM\Database\Seeders\DesignationSeeder;
use Modules\HRM\Database\Seeders\DivisionSeeder;
use Modules\HRM\Database\Seeders\EducationLevelSeeder;
use Modules\HRM\Database\Seeders\EmployeeBankAccountSeeder;
use Modules\HRM\Database\Seeders\EmployeeCertificateSeeder;
use Modules\HRM\Database\Seeders\EmployeeEducationLevelSeeder;
use Modules\HRM\Database\Seeders\EmployeeIdentificationSeeder;
use Modules\HRM\Database\Seeders\EmployeeSeeder;
use Modules\HRM\Database\Seeders\EmploymentTypeSeeder;
use Modules\HRM\Database\Seeders\IdentificationSeeder;
use Modules\HRM\Database\Seeders\UnitSeeder;
use Modules\PAYROLL\Database\Seeders\ComponentSeeder;
use Modules\PAYROLL\Database\Seeders\EmployeeComponentSeeder;
use Modules\PAYROLL\Database\Seeders\EmployeeFinanceProfileSeeder;
use Modules\PAYROLL\Database\Seeders\PayPeriodSeeder;
use Modules\PAYROLL\Database\Seeders\PayrollEntryItemSeeder;
use Modules\PAYROLL\Database\Seeders\PayrollEntrySeeder;
use Modules\PAYROLL\Database\Seeders\SalaryComponentSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'zhc-hq',
                'origin' => 'unguja',
                'slug' => 'zhc-unguja',
            ],
            [
                'name' => 'zhc-pemba',
                'origin' => 'pemba',
                'slug' => 'zhc-pemba',
            ],
        ];

        foreach ($tenants as $tenant) {
            Tenant::create([
                'name'   => $tenant['name'],
                'origin' => $tenant['origin'],
                'slug'   => $tenant['slug'],
            ]);
        };
        $users = [
            ['first_name' => 'Othman ', 'last_name' => 'Abdulla',   'email' => 'test@example.com',   'password' => Hash::make('password'), 'tenant_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['first_name' => 'Seif', 'last_name' => 'Ali',    'email' => 'hr@hrms.go.tz',      'password' => Hash::make('password'), 'tenant_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['first_name' => 'Mohd', 'last_name' => 'Juma', 'email' => 'finance@hrms.go.tz', 'password' => Hash::make('password'), 'tenant_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['first_name' => 'Bakathiri', 'last_name' => 'Muambe', 'email' => 'payroll@hrms.go.tz', 'password' => Hash::make('password'), 'tenant_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['first_name' => 'Ameir', 'last_name' => 'Kificho',   'email' => 'it@hrms.go.tz',      'password' => Hash::make('password'), 'tenant_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('users')->insert($users);
        $this->call([
            // UserSeeder::class,
            BankSeeder::class,
            DepartmentSeeder::class,
            DivisionSeeder::class,
            UnitSeeder::class,
            DesignationSeeder::class,
            CertificateSeeder::class,
            IdentificationSeeder::class,
            EducationLevelSeeder::class,
            EmploymentTypeSeeder::class,
            EmployeeSeeder::class,
            EmployeeEducationLevelSeeder::class,
            EmployeeBankAccountSeeder::class,
            EmployeeCertificateSeeder::class,
            EmployeeIdentificationSeeder::class,
            ComponentSeeder::class,
            // SalaryComponentSeeder::class,
            // EmployeeComponentSeeder::class,
            // EmployeeFinanceProfileSeeder::class,
            // PayPeriodSeeder::class,
            // PayrollEntrySeeder::class,
            // PayrollEntryItemSeeder::class,
            // TenantSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class
        ]);


        // }

        // User::factory()->create([
        //     'email' => 'test@example.com',
        // ]);
    }
}
