<?php

namespace App\Traits\Approval;

use App\Enums\ApprovalDecision;
use App\Enums\ApprovalStatus;
use App\Models\ApprovalChain;
use App\Models\ApprovalTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphOne;
// app/Concerns/Approvable.php
trait Approvable
{
    public function approvalTransaction(): MorphOne
    {
        return $this->morphOne(ApprovalTransaction::class, 'approvable')
            ->latestOfMany();
    }

    public function submitForApproval(ApprovalChain $chain, User $initiator): ApprovalTransaction
    {
        return $this->approvalTransaction()->create([
            'approval_chain_id' => $chain->id,
            'current_step_id' => $chain->firstStep()?->id,
            'status' => ApprovalStatus::Pending,
            'initiated_by' => $initiator->id,
        ]);
    }
}


?>