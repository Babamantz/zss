<?php

namespace App\Livewire\Approvals;

use App\Enums\ApprovalDecision;
use App\Models\ApprovalTransaction;
use App\Services\Approval\ApprovalEngine;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class TransactionShow extends Component
{
    public ApprovalTransaction $transaction;
    public string $remarks = '';

    public function mount(ApprovalTransaction $transaction)
    {
        $this->transaction = $transaction->load(['chain.steps', 'actions.actionedBy', 'currentStep', 'approvable']);
    }

    public function approve(ApprovalEngine $engine)
    {
        $engine->decide($this->transaction, auth()->user(), ApprovalDecision::Approved, $this->remarks);
        $this->remarks = '';
        $this->transaction->refresh()->load(['currentStep', 'actions.actionedBy']);
        session()->flash('success', 'Approved.');
    }

    public function reject(ApprovalEngine $engine)
    {
        try {
            $this->validate(['remarks' => 'required|min:3'], [], ['remarks' => 'rejection reason']);
        } catch (ValidationException $e) {
            throw $e;
        }

        $engine->decide($this->transaction, auth()->user(), ApprovalDecision::Rejected, $this->remarks);
        $this->remarks = '';
        $this->transaction->refresh()->load(['currentStep', 'actions.actionedBy']);
        session()->flash('success', 'Rejected.');
    }

    public function render()
    {
        return view('livewire.approvals.transaction-show');
    }
}
