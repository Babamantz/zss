<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            // =====================================================
            // ATTENDANCE
            // =====================================================
            'attendaces.index',
            'attendaces.create',
            'attendaces.view',
            'attendaces.edit',
            'attendaces.delete',

            // =====================================================
            // USERS
            // =====================================================
            'users.view',
            'users.view-any',
            'users.create',
            'users.edit',
            'users.delete',
            'users.export',
            'users.index',
            'users.profile',

            // Account state
            'users.activate',
            'users.deactivate',
            'users.reset-password',
            'users.impersonate',

            // Roles & permissions
            'users.assign-roles',
            'users.assign-permissions',
            'users.view-roles',

            // Tenant
            'users.assign-tenant',
            'users.view-cross-tenant',

            // Audit
            'users.view-activity-log',

            // =====================================================
            // DOCUMENTS
            // =====================================================
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.index',
            'documents.delete',

            // =====================================================
            // REPORTS
            // =====================================================
            'reports',
            'reports.index',
            'reports.users.index',

            // =====================================================
            // APPROVALS
            // =====================================================
            'transactions',
            'approvals.transaction.index',

            // =====================================================
            // APPROVAL CHAINS
            // =====================================================
            'chains',
            'approvals.chains.index',

            // =====================================================
            // MANAGEMENT
            // =====================================================
            'management',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
