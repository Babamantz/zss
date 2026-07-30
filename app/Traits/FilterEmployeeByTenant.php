<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterEmployeeByTenant
{
    protected static function booted(): void
    {
        static::addGlobalScope('tenant_employee', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // 1. Load the user's current tenant to check its slug
                // (Assumes a 'currentTenant' relationship exists on your User model)
                $tenantSlug = $user->tenant?->slug;

                // 2. Define the privileged roles
                $privilegedRoles = ['super-admin', 'director-hr', 'director-general'];

                // 3. If they have a privileged role AND belong to the HQ tenant, BYPASS the filter
                if ($user->hasAnyRole($privilegedRoles)) {
                    return; // Exit early, allowing them to see all employees across all tenants
                }

                if ($user->hasRole(['hr-officer']) && $tenantSlug === 'zhc-hq') {
                    return;
                }

                // 4. Otherwise, strictly restrict them to their own tenant's employees
                $builder->whereHas('user', function ($query) use ($user) {
                    $query->where('tenant_id', $user->tenant_id);
                });
            }
        });
    }
    // //
    // protected static function booted(): void
    // {
    //     static::addGlobalScope('tenant_employee', function (Builder $builder) {
    //         if (auth()->check()) {
    //             $currentTenantId = auth()->user()->current_tenant_id;

    //             // Filter employees through their relationship to the users table
    //             $builder->whereHas('user', function ($query) use ($currentTenantId) {
    //                 $query->where('tenant_id', $currentTenantId);
    //             });
    //         }
    //     });
    // }
}
