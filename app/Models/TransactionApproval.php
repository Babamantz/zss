<?php

namespace App\Models;

use App\Enums\ChainStatus;
use Illuminate\Database\Eloquent\Model;

class TransactionApproval extends Model
{
    //
    protected $fillable = [
        'chain_transaction_id',
        'flow_level_id',
        'level_no',
        'status',
        'assigned_to',
        'actioned_by',
        'comments',
        'rejection_reason',
        'actioned_at',
    ];

    protected $casts = [
        'actioned_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function transaction()
    {
        return $this->belongsTo(ChainTransaction::class, 'chain_transaction_id');
    }

    public function flowLevel()
    {
        return $this->belongsTo(CustomerFlow::class, 'flow_level_id');
    }

    public function actionedBy()
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPending()
    {
        return $this->status === ChainStatus::PENDING;
    }
    public function isApproved()
    {
        return $this->status === ChainStatus::APPROVED;
    }
    public function isRejected(): bool
    {
        return $this->status === ChainStatus::REJECTED;
    }
    public function isReturned(): bool
    {
        return $this->status === ChainStatus::RETURNED;
    }
}
