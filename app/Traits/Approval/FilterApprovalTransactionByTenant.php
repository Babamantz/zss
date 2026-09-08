<?php

namespace App\Traits\Approval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait FilterApprovalTransactionByTenant
{
    protected static array $globalApprovalRoles = ['super-admin', 'director-hr', 'director-general'];

    protected static function booted(): void
    {
        static::creating(function (Model $model) {
            if (!$model->tenant_id) {
                $model->tenant_id = auth()->user()?->tenant_id;
            }
        });

        static::addGlobalScope('tenant_approval', function (Builder $builder) {
            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            if ($user->hasAnyRole(static::$globalApprovalRoles)) {
                return; // bypass — sees/queries transactions across all tenants
            }

            $builder->where('tenant_id', $user->tenant_id);
        });
    }
}