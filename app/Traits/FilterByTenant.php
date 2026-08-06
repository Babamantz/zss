<?php

namespace App\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait FilterByTenant
{
    //
    protected static function booted(): void
    {
        // $currentTenantId = auth()->user()->tenants->first()->id;
        $currentTenantId = auth()->user()->tenant_id;

        static::creating(function (Model $model) use ($currentTenantId) {
            $model->user_id = auth()->id();
            $model->tenant_id = $currentTenantId;
        });

        static::addGlobalScope(function (Builder $builder) use ($currentTenantId) {
            $builder->where('tenant_id', $currentTenantId);
        });
    }
}
