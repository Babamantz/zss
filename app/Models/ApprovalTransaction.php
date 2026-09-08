<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Traits\Approval\FilterApprovalTransactionByTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApprovalTransaction extends Model
{
    //
    use FilterApprovalTransactionByTenant;
    protected $fillable = [
        'approval_chain_id',
        'approvable_type',
        'approvable_id',
        'current_step_id',
        'status',
        'initiated_by',
        'completed_at',
        'tenant_id'
    ];

    protected $casts = ['status' => ApprovalStatus::class, 'completed_at' => 'datetime'];

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function chain(): BelongsTo
    {
        return $this->belongsTo(ApprovalChain::class, 'approval_chain_id');
    }

    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(ApprovalChainStep::class, 'current_step_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ApprovalAction::class)->orderBy('created_at');
    }

    // public function canBeActionedBy(User $user): bool
    // {
    //     if ($this->status !== ApprovalStatus::Pending || !$this->currentStep) {
    //         return false;
    //     }

    //     return $this->currentStep->eligibleApprovers()->contains('id', $user->id);
    // }

    // Inside your ApprovalTransaction model
    // public function canBeActionedBy(User $user): bool
    // {
    //     if ($this->status !== ApprovalStatus::Pending || !$this->currentStep) {
    //         return false;
    //     }

    //     // FIX: Pass the transaction's tenant_id down to the filtering logic
    //     return $this->currentStep->eligibleApprovers($this->tenant_id)->contains('id', $user->id);
    // }

    // Inside your ApprovalTransaction model
    // public function canBeActionedBy(User $user): bool
    // {
    //     // 1. Ensure the transaction is pending and has an active step
    //     if ($this->status !== ApprovalStatus::Pending || !$this->currentStep) {
    //         return false;
    //     }

    //     // 2. Prevent double-voting if the step requires all approvers to sign off
    //     if ($this->currentStep->requires_all) {
    //         $hasAlreadyVoted = $this->actions()
    //             ->where('approval_chain_step_id', $this->currentStep->id)
    //             ->where('actioned_by', $user->id)
    //             ->exists();

    //         if ($hasAlreadyVoted) {
    //             return false;
    //         }
    //     }

    //     dd('nafika');

    //     // 3. Confirm the user is globally or tenant-eligible for this step
    //     return $this->currentStep->eligibleApprovers($this->tenant_id)->contains('id', $user->id);
    // }

    public function canBeActionedBy(User $user): bool
    {
        if (
            $this->status !== ApprovalStatus::Pending ||
            !$this->currentStep
        ) {
            return false;
        }

        /*
     * Prevent the same user from voting twice
     * on the same approval step.
     */
        $hasAlreadyVoted = $this->actions()
            ->where('approval_chain_step_id', $this->current_step_id)
            ->where('actioned_by', $user->id)
            ->exists();

        if ($hasAlreadyVoted) {
            return false;
        }

        /*
     * Global approval roles can action any step.
     */
        if ($user->hasAnyRole([
            'super-admin',
            'director-hr',
            'director-general',
        ])) {
            return true;
        }

        /*
     * Otherwise, user must be an eligible approver
     * for the current step and belong to the
     * transaction's tenant.
     */
        return $this->currentStep
            ->eligibleApprovers($this->tenant_id)
            ->contains('id', $user->id);
    }
}
