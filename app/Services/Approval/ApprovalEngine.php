<?php

namespace App\Services\Approval;

use App\Contracts\HasApprovalStatus;
use App\Enums\ApprovalDecision;
use App\Enums\ApprovalStatus;
use App\Models\ApprovalChainStep;
use App\Models\ApprovalTransaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ApprovalEngine
{
    public function decide(ApprovalTransaction $transaction, User $user, ApprovalDecision $decision, ?string $remarks = null): void
    {
        throw_unless($transaction->canBeActionedBy($user), \RuntimeException::class, 'Not authorized to action this step.');

        DB::transaction(function () use ($transaction, $user, $decision, $remarks) {
            $transaction->actions()->create([
                'approval_chain_step_id' => $transaction->current_step_id,
                'actioned_by' => $user->id,
                'decision' => $decision,
                'remarks' => $remarks,
            ]);

            if ($decision === ApprovalDecision::Rejected) {
                $transaction->update([
                    'status' => ApprovalStatus::Rejected,
                    'completed_at' => now(),
                ]);
                $this->syncApprovable($transaction, rejected: true);
                return;
            }

            $currentStep = $transaction->currentStep;

            if ($currentStep->requires_all) {
                $approversCount = $currentStep->eligibleApprovers()->count();
                $approvedCount = $transaction->actions()
                    ->where('approval_chain_step_id', $currentStep->id)
                    ->where('decision', ApprovalDecision::Approved)
                    ->count();

                if ($approvedCount < $approversCount) {
                    return;
                }
            }

            $next = $currentStep->nextStep();

            if ($next) {
                $transaction->update(['current_step_id' => $next->id]);
            } else {
                $transaction->update([
                    'status' => ApprovalStatus::Approved,
                    'current_step_id' => null,
                    'completed_at' => now(),
                ]);
                $this->syncApprovable($transaction, rejected: false);
            }
        });
    }




    protected function eligibleApprovers(ApprovalChainStep $step): Collection
    {
        return match ($step->approver_type->value) {
            'user'             => User::where('id', $step->approver_value)->get(),
            'role'             => User::role($step->approver_value)->get(), // Spatie
            'department_head'  => User::where('department_id', $step->approver_value)
                ->where('is_department_head', true)->get(),
            default            => collect(),
        };
    }

    protected function syncApprovable(ApprovalTransaction $transaction, bool $rejected): void
    {
        $approvable = $transaction->approvable;

        if (!$approvable instanceof HasApprovalStatus) {
            return;
        }

        $rejected ? $approvable->onApprovalRejected() : $approvable->onApprovalCompleted();
    }
}
