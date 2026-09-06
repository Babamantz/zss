<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApprovalTransaction extends Model
{
    //
    protected $fillable = [
        'approval_chain_id',
        'approvable_type',
        'approvable_id',
        'current_step_id',
        'status',
        'initiated_by',
        'completed_at',
    ];

    protected $casts = ['status' => ApprovalStatus::class, 'completed_at' => 'datetime'];

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function chain(): BelongsTo
    {
        return $this->belongsTo(ApprovalChain::class,'approval_chain_id');
    }

    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(ApprovalChainStep::class, 'current_step_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ApprovalAction::class)->orderBy('created_at');
    }

    public function canBeActionedBy(User $user): bool
    {
        if ($this->status !== ApprovalStatus::Pending || !$this->currentStep) {
            return false;
        }

        return $this->currentStep->eligibleApprovers()->contains('id', $user->id);
    }
}
