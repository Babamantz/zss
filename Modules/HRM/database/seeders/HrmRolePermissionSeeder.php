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
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all()); // unrestricted

        $directorGeneral = Role::firstOrCreate(['name' => 'director-general', 'guard_name' => 'web']);
        $directorGeneral->givePermissionTo([
            'hrm.employees.view',
            'hrm.employees.view-any',
            'hrm.employees.view-cross-tenant',
            // 'hrm.employees.approve',
            // 'hrm.employees.reject',
            'hrm.employees.approve-cross-tenant',
            // 'hrm.employees.view-approval-history',
            'hrm.reports.view',
            // 'hrm.reports.export',
        ]);

        $directorHr = Role::firstOrCreate(['name' => 'director-hr', 'guard_name' => 'web']);
        $directorHr->givePermissionTo([
            'hrm.employees.view',
            'hrm.employees.view-any',
            'hrm.employees.view-cross-tenant',
            'hrm.employees.edit',
            'hrm.employees.approve',
            'hrm.employees.reject',
            // 'hrm.employees.approve-cross-tenant',
            // 'hrm.employees.view-approval-history',
            // 'hrm.approval-chains.view',
            // 'hrm.approval-chains.create',
            // 'hrm.approval-chains.edit',
            // 'hrm.approval-chains.manage-steps',
            'hrm.departments.view',
            'hrm.units.view',
            'hrm.divisions.view',
            'hrm.reports.view',
            'hrm.reports.export',
        ]);

        $hrOfficer = Role::firstOrCreate(['name' => 'hr-officer', 'guard_name' => 'web']);
        $hrOfficer->givePermissionTo([
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
