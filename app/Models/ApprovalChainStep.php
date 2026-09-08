<?php

namespace App\Models;

use App\Enums\ApproverType;
use App\Models\ApprovalChain;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ApprovalChainStep extends Model
{
    //

    protected $fillable = ['approval_chain_id', 'order', 'name', 'approver_type', 'approver_value', 'requires_all'];

    protected $casts = ['approver_type' => ApproverType::class, 'requires_all' => 'boolean'];

    public function chain(): BelongsTo
    {
        return $this->belongsTo(ApprovalChain::class, 'approval_chain_id');
    }

    public function nextStep(): ?self
    {
        return static::where('approval_chain_id', $this->approval_chain_id)
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }

    /** Resolve which users can act on this step */
    // public function eligibleApprovers(): Collection
    // {
    //     return match ($this->approver_type) {
    //         ApproverType::User => User::where('id', $this->approver_value)->get(),
    //         ApproverType::Role => User::role($this->approver_value)->get(), // Spatie
    //         ApproverType::DepartmentHead => User::where('department_id', $this->approver_value)
    //             ->where('is_department_head', true)->get(),
    //     };
    // }

    // // Inside your ApprovalChainStep model
    // public function eligibleApprovers(int $resourceTenantId): Collection
    // {
    //     // dd('nafika');
    //     $users = match ($this->approver_type) {
    //         ApproverType::User => User::where('id', $this->approver_value)->get(),
    //         ApproverType::Role => User::role($this->approver_value)->get(),
    //         ApproverType::DepartmentHead => User::where('department_id', $this->approver_value)
    //             ->where('is_department_head', true)->get(),
    //     };

    //     // Strictly enforce multi-tenancy bounds
    //     return $users->filter(
    //         fn(User $u) => $u->hasAnyRole($globalRoles) || $u->tenant_id === $resourceTenantId
    //     );
    // }

    public function eligibleApprovers(int $resourceTenantId): Collection
    {
        return match ($this->approver_type) {
            ApproverType::User =>
            User::where('id', $this->approver_value)
                ->where('tenant_id', $resourceTenantId)
                ->get(),

            ApproverType::Role =>
            User::role($this->approver_value)
                ->where('tenant_id', $resourceTenantId)
                ->get(),

            ApproverType::DepartmentHead =>
            User::where('department_id', $this->approver_value)
                ->where('is_department_head', true)
                ->where('tenant_id', $resourceTenantId)
                ->get(),
        };
    }
}
