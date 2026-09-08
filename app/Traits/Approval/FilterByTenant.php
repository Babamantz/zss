<?php

namespace App\Traits\Approval;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait FilterByTenant
{
    //
    protected static function booted(): void
    {
        // $currentTenantId = auth()->user()->tenants->first()->id;
        $currentTenantId = auth()->user()->tenant_id;

        static::addGlobalScope(function (Builder $builder) use ($currentTenantId) {
            $builder->where('tenant_id', $currentTenantId);
        });
    }
}
