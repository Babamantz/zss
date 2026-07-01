<?php

namespace App\Livewire\Chain;

use App\Models\ChainTransaction;
use Livewire\Component;

class TransactionTimeline extends Component
{
    public int $transactionId;

    public function mount(int $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    public function render()
    {
        $transaction = ChainTransaction::with([
            'chainModule.activeFlows.role',
            'approvals.flowLevel.role',
            'approvals.actionedBy',
            'approvals.assignedTo',
            'createdBy',
        ])->findOrFail($this->transactionId);

        return view(
            'chain::livewire.transaction-timeline',
            compact('transaction')
        );
    }
}
