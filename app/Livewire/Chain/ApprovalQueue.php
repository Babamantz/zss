<?php

namespace App\Livewire\Chain;

use App\Enums\ChainStatus;
use App\Models\ChainTransaction;
use App\Services\ChainWorkflowService;
use Livewire\Component;
use Livewire\WithPagination;

class ApprovalQueue extends Component
{
    use WithPagination;

    public string $filterStatus  = '';
    public string $filterModule  = '';
    public string $search        = '';

    // Action modal state
    public ?int   $actioningId    = null;
    public string $actionType     = '';   // 'approve' | 'reject' | 'return'
    public string $comments       = '';
    public string $reason         = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openAction(int $id, string $type): void
    {
        $this->actioningId = $id;
        $this->actionType  = $type;
        $this->comments    = '';
        $this->reason      = '';
    }

    public function closeAction(): void
    {
        $this->reset(['actioningId', 'actionType', 'comments', 'reason']);
    }

    public function submitAction(): void
    {
        $this->validate([
            'reason' => 'required_if:actionType,reject|required_if:actionType,return|string|min:10|max:500',
        ]);

        $transaction = ChainTransaction::findOrFail($this->actioningId);
        $service     = new ChainWorkflowService();

        try {
            match ($this->actionType) {
                'approve' => $service->approve($transaction, $this->comments),
                'reject'  => $service->reject($transaction, $this->reason, $this->comments),
                'return'  => $service->returnForCorrection($transaction, $this->reason, $this->comments),
                default   => throw new \RuntimeException('Unknown action type.'),
            };

            session()->flash('success', 'Action recorded successfully.');
            $this->closeAction();
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $user = auth()->user();

        // Show transactions where auth user is eligible to action
        $roleIds = $user->roles->pluck('id');

        $pendingLevelIds = \App\Models\CustomerFlow::where('is_active', true)
            ->where(
                fn($q) =>
                $q->where('user_id', $user->id)
                    ->orWhereIn('role_id', $roleIds)
            )->pluck('id');

        $myQueue = ChainTransaction::with([
            'chainModule',
            'approvals.flowLevel',
            'createdBy',
        ])
            ->whereIn('status', [ChainStatus::PENDING, ChainStatus::RETURNED])
            ->whereHas(
                'approvals',
                fn($q) =>
                $q->where('status', ChainStatus::PENDING)
                    ->whereIn('flow_level_id', $pendingLevelIds)
            )
            ->when(
                $this->filterModule,
                fn($q) =>
                $q->whereHas(
                    'chainModule',
                    fn($m) =>
                    $m->where('code', $this->filterModule)
                )
            )
            ->when(
                $this->search,
                fn($q) =>
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('reference_no', 'like', "%{$this->search}%")
            )
            ->latest()
            ->paginate(15);

        // All transactions (for history view)
        $allTransactions = ChainTransaction::with(['chainModule', 'createdBy'])
            ->when(
                $this->filterStatus,
                fn($q) =>
                $q->where('status', $this->filterStatus)
            )
            ->when(
                $this->filterModule,
                fn($q) =>
                $q->whereHas(
                    'chainModule',
                    fn($m) =>
                    $m->where('code', $this->filterModule)
                )
            )
            ->when(
                $this->search,
                fn($q) =>
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('reference_no', 'like', "%{$this->search}%")
            )
            ->latest()
            ->paginate(15);

        $actioningTransaction = $this->actioningId
            ? ChainTransaction::with([
                'chainModule',
                'approvals.flowLevel.role',
                'approvals.actionedBy',
            ])->find($this->actioningId)
            : null;

        $modules = \App\Models\ChainModule::where('is_active', true)->get();

        return view('livewire.chain.approval-queue', compact(
            'myQueue',
            'allTransactions',
            'actioningTransaction',
            'modules',
        ));
    }
}
