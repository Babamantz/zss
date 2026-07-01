<?php

namespace App\Models;

use App\Enums\ChainStatus;
use App\Models\TransactionApproval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChainTransaction extends Model
{
    //

    use SoftDeletes;

    protected $fillable = [
        'chain_module_id',
        'transactionable_type',
        'transactionable_id',
        'reference_no',
        'title',
        'description',
        'status',
        'current_level',
        'created_by',
        'assigned_to_user',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'current_level' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function chainModule()
    {
        return $this->belongsTo(ChainModule::class);
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function approvals()
    {
        return $this->hasMany(TransactionApproval::class)->orderBy('level_no');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user');
    }

    // ── Status helpers ────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === ChainStatus::PENDING;
    }
    public function isApproved(): bool
    {
        return $this->status === ChainStatus::APPROVED;
    }
    public function isRejected(): bool
    {
        return $this->status === ChainStatus::REJECTED;
    }
    public function isCompleted(): bool
    {
        return $this->status === ChainStatus::COMPLETED;
    }

    public function statusLabel(): string
    {
        return ChainStatus::label($this->status);
    }

    public function statusBadgeColor(): string
    {
        return ChainStatus::badgeColor($this->status);
    }

    // ── Level helpers ─────────────────────────────────────────────────────────

    public function currentFlowLevel(): ?CustomerFlow
    {
        return CustomerFlow::where('chain_module_id', $this->chain_module_id)
            ->where('level_no', $this->current_level)
            ->where('is_active', true)
            ->first();
    }

    public function currentApproval(): ?TransactionApproval
    {
        return $this->approvals()
            ->where('level_no', $this->current_level)
            ->where('status', ChainStatus::PENDING)
            ->first();
    }

    public function isAtLastLevel(): bool
    {
        $maxLevel = CustomerFlow::where('chain_module_id', $this->chain_module_id)
            ->where('is_active', true)
            ->max('level_no');

        return $this->current_level >= $maxLevel;
    }

    public function canBeActionedBy(User $user): bool
    {
        if (!$this->isPending()) return false;

        $level = $this->currentFlowLevel();
        return $level?->canBeActionedBy($user) ?? false;
    }
}
