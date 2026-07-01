<?php

// App/Services/ChainWorkflowService.php

namespace App\Services;

use App\Enums\ChainStatus;
use App\Models\ChainModule;
use App\Models\ChainTransaction;
use App\Models\CustomerFlow;
use App\Models\TransactionApproval;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ChainWorkflowService
{
    // ── Create a new chain transaction ────────────────────────────────────────

    public function create(
        string  $moduleCode,
        string  $title,
        ?string $description    = null,
        ?object $transactionable = null
    ): ChainTransaction {

        $module = ChainModule::findByCode($moduleCode);

        if (!$module) {
            throw new RuntimeException("Chain module [{$moduleCode}] not found or inactive.");
        }

        $firstLevel = $module->activeFlows()->orderBy('order')->first();

        if (!$firstLevel) {
            throw new RuntimeException("Chain module [{$moduleCode}] has no configured flow levels.");
        }

        return DB::transaction(function () use ($module, $firstLevel, $title, $description, $transactionable) {

            $transaction = ChainTransaction::create([
                'chain_module_id'       => $module->id,
                'transactionable_type'  => $transactionable ? get_class($transactionable) : null,
                'transactionable_id'    => $transactionable?->id,
                'reference_no'          => $this->generateReference($module->code),
                'title'                 => $title,
                'description'           => $description,
                'status'                => ChainStatus::PENDING,
                'current_level'         => $firstLevel->level_no,
                'created_by'            => auth()->id(),
            ]);

            // Create first pending approval
            $this->createApprovalRecord($transaction, $firstLevel);

            return $transaction;
        });
    }

    // ── Approve current level ─────────────────────────────────────────────────

    public function approve(
        ChainTransaction $transaction,
        string           $comments = ''
    ): ChainTransaction {

        $this->assertCanAction($transaction);

        return DB::transaction(function () use ($transaction, $comments) {

            // Action the current approval
            $approval = $transaction->currentApproval();
            $approval->update([
                'status'      => ChainStatus::APPROVED,
                'actioned_by' => auth()->id(),
                'comments'    => $comments,
                'actioned_at' => now(),
            ]);

            // Check if this was the last level
            if ($transaction->isAtLastLevel()) {
                $transaction->update([
                    'status'       => ChainStatus::COMPLETED,
                    'completed_at' => now(),
                ]);
            } else {
                // Advance to next level
                $nextLevel = $this->getNextLevel($transaction);

                $transaction->update([
                    'current_level' => $nextLevel->level_no,
                ]);

                $this->createApprovalRecord($transaction->fresh(), $nextLevel);
            }

            return $transaction->fresh();
        });
    }

    // ── Reject ────────────────────────────────────────────────────────────────

    public function reject(
        ChainTransaction $transaction,
        string           $reason,
        string           $comments = ''
    ): ChainTransaction {

        if (empty(trim($reason))) {
            throw new RuntimeException('A rejection reason is required.');
        }

        $this->assertCanAction($transaction);

        return DB::transaction(function () use ($transaction, $reason, $comments) {

            $approval = $transaction->currentApproval();
            $approval->update([
                'status'           => ChainStatus::REJECTED,
                'actioned_by'      => auth()->id(),
                'comments'         => $comments,
                'rejection_reason' => $reason,
                'actioned_at'      => now(),
            ]);

            $transaction->update([
                'status' => ChainStatus::REJECTED,
            ]);

            return $transaction->fresh();
        });
    }

    // ── Return for correction ─────────────────────────────────────────────────

    public function returnForCorrection(
        ChainTransaction $transaction,
        string           $reason,
        string           $comments = ''
    ): ChainTransaction {

        $this->assertCanAction($transaction);

        return DB::transaction(function () use ($transaction, $reason, $comments) {

            $approval = $transaction->currentApproval();
            $approval->update([
                'status'           => ChainStatus::RETURNED,
                'actioned_by'      => auth()->id(),
                'comments'         => $comments,
                'rejection_reason' => $reason,
                'actioned_at'      => now(),
            ]);

            // Reset to level 1 for correction
            $firstLevel = CustomerFlow::where('chain_module_id', $transaction->chain_module_id)
                ->where('is_active', true)
                ->orderBy('order')
                ->first();

            $transaction->update([
                'status'        => ChainStatus::RETURNED,
                'current_level' => $firstLevel->level_no,
            ]);

            // Create a new pending approval at level 1
            $this->createApprovalRecord($transaction->fresh(), $firstLevel);

            return $transaction->fresh();
        });
    }

    // ── Resubmit after correction ─────────────────────────────────────────────

    public function resubmit(
        ChainTransaction $transaction,
        string           $comments = ''
    ): ChainTransaction {

        if ($transaction->status !== ChainStatus::RETURNED) {
            throw new RuntimeException('Only returned transactions can be resubmitted.');
        }

        if (auth()->id() !== $transaction->created_by) {
            throw new RuntimeException('Only the transaction creator can resubmit.');
        }

        return DB::transaction(function () use ($transaction, $comments) {

            // Mark current returned approval as resolved
            $pending = $transaction->approvals()
                ->where('status', ChainStatus::RETURNED)
                ->latest()
                ->first();

            if ($pending) {
                $pending->update([
                    'status'      => ChainStatus::APPROVED,
                    'actioned_by' => auth()->id(),
                    'comments'    => 'Resubmitted: ' . $comments,
                    'actioned_at' => now(),
                ]);
            }

            $transaction->update(['status' => ChainStatus::PENDING]);

            return $transaction->fresh();
        });
    }

    // ── Internal helpers ──────────────────────────────────────────────────────

    protected function createApprovalRecord(
        ChainTransaction $transaction,
        CustomerFlow     $level
    ): TransactionApproval {

        // Assign to specific user if configured, otherwise leave to role
        $assignedTo = $level->user_id;

        return TransactionApproval::create([
            'chain_transaction_id' => $transaction->id,
            'flow_level_id'        => $level->id,
            'level_no'             => $level->level_no,
            'status'               => ChainStatus::PENDING,
            'assigned_to'          => $assignedTo,
        ]);
    }

    protected function getNextLevel(ChainTransaction $transaction): CustomerFlow
    {
        $next = CustomerFlow::where('chain_module_id', $transaction->chain_module_id)
            ->where('is_active', true)
            ->where('level_no', '>', $transaction->current_level)
            ->orderBy('order')
            ->first();

        if (!$next) {
            throw new RuntimeException('No next level found in the workflow.');
        }

        return $next;
    }

    protected function assertCanAction(ChainTransaction $transaction): void
    {
        if (!$transaction->isPending() && $transaction->status !== ChainStatus::RETURNED) {
            throw new RuntimeException(
                'This transaction is not in a state that can be actioned.'
            );
        }

        $level = $transaction->currentFlowLevel();

        if (!$level) {
            throw new RuntimeException('No active flow level found for this transaction.');
        }

        if (!$level->canBeActionedBy(auth()->user())) {
            throw new RuntimeException(
                'You are not authorised to action this approval level.'
            );
        }
    }

    protected function generateReference(string $moduleCode): string
    {
        $prefix = strtoupper(Str::limit($moduleCode, 4, ''));
        $year   = now()->format('Y');
        $seq    = str_pad(
            ChainTransaction::whereYear('created_at', $year)->count() + 1,
            5,
            '0',
            STR_PAD_LEFT
        );
        return "{$prefix}-{$year}-{$seq}";
    }
}
