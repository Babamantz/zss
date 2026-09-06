<?php

namespace App\Livewire\Approvals;



use App\Models\ApprovalTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public string $statusFilter = '';


    public function render()
    {
        $transactions = ApprovalTransaction::with(['approvable', 'currentStep', 'chain'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);
        return view('livewire.approvals.transaction-index', compact('transactions'));
    }
}
