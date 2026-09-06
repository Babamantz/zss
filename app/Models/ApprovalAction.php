<?php

namespace App\Models;

use App\Enums\ApprovalDecision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalAction extends Model
{
    //

    protected $fillable = [
        'approval_transaction_id',
        'approval_chain_step_id',
        'actioned_by',
        'decision',
        'remarks',
    ];

    protected $casts = ['decision' => ApprovalDecision::class];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(ApprovalTransaction::class);
    }

    public function actionedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }
}
