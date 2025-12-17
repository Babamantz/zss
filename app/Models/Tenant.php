<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Tenant extends Model
{
    //

    protected $guarded = false;

    public function users()
    {
        return $this->hasMany(User::class);
    }


    // protected static function booted()
    // {
    //     // Automatically scope all queries to current tenant
    //     static::addGlobalScope('tenant', function (Builder $builder) {
    //         if ($tenantId = app('tenant.id')) {
    //             $builder->where('tenant_id', $tenantId);
    //         }
    //     });

    //     // Automatically set tenant_id when creating
    //     static::creating(function ($model) {
    //         if ($tenantId = app('tenant.id')) {
    //             $model->tenant_id = $tenantId;
    //         }
    //     });
    // }
}
