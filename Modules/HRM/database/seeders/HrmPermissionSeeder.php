<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class HrmPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Employee records
            'employees',
            'hrm',
            'hrm.employees.view',
            'hrm.employees.index',
            'hrm.employees.view-any',       // list/index access
            'hrm.employees.create',
            'hrm.employees.edit',
            'hrm.employees.delete',
            'hrm.employees.export',

            // Employee approval (chain-specific, distinct from general edit rights)
            'hrm.employees.submit-for-approval',
            'hrm.employees.approve',
            'hrm.employees.reject',
            'hrm.employees.view-approval-history',

            // Cross-tenant visibility (mirrors FilterEmployeeByTenant's bypass logic)
            'hrm.employees.view-cross-tenant',
            'hrm.employees.approve-cross-tenant',

            // Departments / Units / Divisions (referenced throughout EmployeeCreate/Edit)
            'hrm.departments.view',
            'hrm.departments.create',
            'hrm.departments.edit',
            'hrm.departments.delete',

            'hrm.units.view',
            'hrm.units.create',
            'hrm.units.edit',
            'hrm.units.delete',

            'hrm.divisions.view',
            'hrm.divisions.create',
            'hrm.divisions.edit',
            'hrm.divisions.delete',

            // Lookups/config used in the employee form (identification types, certificate types, employment types, designations)
            'hrm.lookups.manage',

            // Approval chain configuration (separate from acting on a transaction)
            'hrm.approval-chains.view',
            'hrm.approval-chains.create',
            'hrm.approval-chains.edit',
            'hrm.approval-chains.delete',
            'hrm.approval-chains.manage-steps',

            // Reports
            'hrm.reports.view',
            'hrm.reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
