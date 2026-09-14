<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HrmRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
    |--------------------------------------------------------------------------
    | Super Admin
    |--------------------------------------------------------------------------
    */

        $superAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );


        /*
    |--------------------------------------------------------------------------
    | Director General
    |--------------------------------------------------------------------------
    */

        $directorGeneral = Role::firstOrCreate([
            'name' => 'director-general',
            'guard_name' => 'web',
        ]);

        $directorGeneral->syncPermissions([
            'hrm.employees.view',
            'hrm.employees.view-any',
            'hrm.employees.view-cross-tenant',

            'hrm.employees.approve',
            'hrm.employees.reject',
            'hrm.employees.approve-cross-tenant',

            'hrm.employees.view-approval-history',

            'hrm.reports.view',
            'hrm.reports.export',
        ]);


        /*
    |--------------------------------------------------------------------------
    | Director HR
    |--------------------------------------------------------------------------
    */

        $directorHr = Role::firstOrCreate([
            'name' => 'director-hr',
            'guard_name' => 'web',
        ]);

        $directorHr->syncPermissions([
            'hrm.employees.view',
            'hrm.employees.view-any',
            'hrm.employees.view-cross-tenant',

            'hrm.employees.edit',

            'hrm.employees.approve',
            'hrm.employees.reject',
            'hrm.employees.approve-cross-tenant',

            'hrm.employees.view-approval-history',

            'hrm.approval-chains.view',
            'hrm.approval-chains.create',
            'hrm.approval-chains.edit',
            'hrm.approval-chains.manage-steps',

            'hrm.departments.view',
            'hrm.units.view',
            'hrm.divisions.view',

            'hrm.reports.view',
            'hrm.reports.export',
        ]);


        /*
    |--------------------------------------------------------------------------
    | HR Officer
    |--------------------------------------------------------------------------
    */

        $hrOfficer = Role::firstOrCreate([
            'name' => 'hr-officer',
            'guard_name' => 'web',
        ]);

        $hrOfficer->syncPermissions([
            'hrm.employees.view',
            'hrm.employees.view-any',

            'hrm.employees.create',
            'hrm.employees.edit',

            'hrm.employees.submit-for-approval',

            'hrm.employees.view-approval-history',

            'hrm.departments.view',
            'hrm.units.view',
            'hrm.divisions.view',

            'hrm.reports.view',
        ]);
    }
}
