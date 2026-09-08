<?php

namespace App\Services\Approval;

use App\Contracts\HasApprovalStatus;
use App\Enums\ApprovalDecision;
use App\Enums\ApprovalStatus;
use App\Enums\ApproverType;
use App\Models\ApprovalChainStep;
use App\Models\ApprovalTransaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ApprovalEngine
{
    protected static array $globalApprovalRoles = ['super-admin', 'director-hr', 'director-general'];

    // public function decide(ApprovalTransaction $transaction, User $user, ApprovalDecision $decision, ?string $remarks = null): void
    // {
    //     // throw_unless($transaction->canBeActionedBy($user), \RuntimeException::class, 'Not authorized to action this step.');

    //     DB::transaction(function () use ($transaction, $user, $decision, $remarks) {
    //         // dd('transaction '.$transaction,'User '.$user,$decision, 'Remark   '.$remarks);
    //         $transaction->actions()->create([
    //             'approval_chain_step_id' => $transaction->current_step_id,
    //             'actioned_by' => $user->id,
    //             'decision' => $decision,
    //             'remarks' => $remarks,
    //         ]);

    //         if ($decision === ApprovalDecision::Rejected) {
    //             $transaction->update([
    //                 'status' => ApprovalStatus::Rejected,
    //                 'completed_at' => now(),
    //             ]);
    //             $this->syncApprovable($transaction, rejected: true);
    //             return;
    //         }

    //         $currentStep = $transaction->currentStep;

    //         // Inside ApprovalEngine class -> decide() method
    //         if ($currentStep->requires_all) {
    //             // Call the model method directly, passing the tenant_id
    //             $approversCount = $currentStep->eligibleApprovers($transaction->tenant_id)->count();

    //             $approvedCount = $transaction->actions()
    //                 ->where('approval_chain_step_id', $currentStep->id)
    //                 ->where('decision', ApprovalDecision::Approved)
    //                 ->count();

    //             if ($approvedCount < $approversCount) {
    //                 session()->flash('success', 'Approval recorded. Awaiting other eligible approvers.');

    //                 return;
    //             }
    //         }



    //         $next = $currentStep->nextStep();
    //         dd($next);

    //         if ($next) {
    //             $transaction->update(['current_step_id' => $next->id]);
    //         } else {
    //             $transaction->update([
    //                 'status' => ApprovalStatus::Approved,
    //                 'current_step_id' => null,
    //                 'completed_at' => now(),
    //             ]);
    //             $this->syncApprovable($transaction, rejected: false);
    //         }
    //     });
    // }


    public function decide(
        ApprovalTransaction $transaction,
        User $user,
        ApprovalDecision $decision,
        ?string $remarks = null
    ): void {
        DB::transaction(function () use (
            $transaction,
            $user,
            $decision,
            $remarks
        ) {

            // $transaction->lockForUpdate()->refresh();

            $transaction = ApprovalTransaction::where('id', $transaction->id)->lockForUpdate()->first();

            throw_unless(
                $transaction->canBeActionedBy($user),
                \RuntimeException::class,
                'Not authorized to action this step.'
            );

            $currentStep = $transaction->currentStep;

            throw_unless(
                $currentStep,
                \RuntimeException::class,
                'Approval transaction has no current step.'
            );

            $transaction->actions()->create([
                'approval_chain_step_id' => $currentStep->id,
                'actioned_by' => $user->id,
                'decision' => $decision,
                'remarks' => $remarks,
            ]);

            /*
         * REJECTION
         */
            if ($decision === ApprovalDecision::Rejected) {

                $transaction->update([
                    'status' => ApprovalStatus::Rejected,
                    'current_step_id' => null,
                    'completed_at' => now(),
                ]);

                $this->syncApprovable(
                    $transaction,
                    rejected: true
                );

                return;
            }

            /*
         * REQUIRES ALL
         */
            if ($currentStep->requires_all) {

                $approversCount = $this
                    ->eligibleApprovers(
                        $currentStep,
                        $transaction->tenant_id
                    )
                    ->count();

                $approvedCount = $transaction->actions()
                    ->where(
                        'approval_chain_step_id',
                        $currentStep->id
                    )
                    ->where(
                        'decision',
                        ApprovalDecision::Approved
                    )
                    ->distinct('actioned_by')
                    ->count('actioned_by');

                if ($approvedCount < $approversCount) {
                    return;
                }
            }

            /*
         * MOVE TO NEXT STEP
         */
            $next = $currentStep->nextStep();

            if ($next) {

                $transaction->update([
                    'current_step_id' => $next->id,
                ]);

                return;
            }

            /*
         * COMPLETED
         */
            $transaction->update([
                'status' => ApprovalStatus::Approved,
                'current_step_id' => null,
                'completed_at' => now(),
            ]);

            $this->syncApprovable(
                $transaction,
                rejected: false
            );
            $transaction->unsetRelations();
        });
    }




    protected function eligibleApprovers(ApprovalChainStep $step, int $resourceTenantId): Collection
    {
        $users = match ($step->approver_type) {
            ApproverType::User => User::where('id', $step->approver_value)->get(),
            ApproverType::Role => User::whereHas('roles', fn($q) => $q->where('name', $step->approver_value))->get(),
            ApproverType::DepartmentHead => User::where('department_id', $step->approver_value)
                ->where('is_department_head', true)->get(),
        };

        // dd($users);



        // return $users->filter(
        //     fn(User $u) => $u->hasAnyRole(static::$globalApprovalRoles) || $u->tenant_id === $resourceTenantId
        // );


        return $users->filter(
            fn(User $u) => $u->hasPermissionTo('approve_any_employee')
        );
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
