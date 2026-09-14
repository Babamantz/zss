{{-- resources/views/livewire/approvals/transaction-index.blade.php --}}
<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Approval Transactions</h5>
        <select wire:model.live="statusFilter" class="form-control" style="width: auto;">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Item</th>
                    <th>Chain</th>
                    <th>Current Step</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $txn)
                {{-- @dd($txn) --}}
                    <tr wire:key="txn-{{ $txn->id }}">
                        <td class="font-weight-medium">
                            {{ class_basename($txn->approvable_type) }} #{{ $txn->approvable_id }}
                        </td>
                        <td class="text-muted">{{ $txn->chain->name }}</td>
                        <td class="text-muted">{{ $txn->currentStep?->name ?? 'No pending step' }}</td>
                        <td>
                            @php
                                $badgeClass = match ($txn->status->value) {
                                    'pending' => 'badge-warning',
                                    'approved' => 'badge-success',
                                    'rejected' => 'badge-danger',
                                    'cancelled' => 'badge-secondary',
                                    default => 'badge-light',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ ucfirst($txn->status->value) }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('approvals.transactions.show', $txn) }}" wire:navigate
                                class="btn btn-link btn-sm p-0">
                                View Timeline
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $transactions->links() }}
</div>