{{-- resources/views/chain/livewire/approval-queue.blade.php --}}

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Approval Queue</h4>
            <small class="text-muted">Review and action pending transactions</small>
        </div>
        <a href="{{ route('chain.transaction.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus me-1"></i> New Transaction
        </a>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── My Queue ─────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between">
            <h6 class="mb-0">
                <i class="fa fa-inbox me-2 text-warning"></i>
                My Approval Queue
                @if ($myQueue->total() > 0)
                    <span class="badge bg-warning-subtle text-warning ms-1">
                        {{ $myQueue->total() }}
                    </span>
                @endif
            </h6>
        </div>

        {{-- Filters --}}
        <div class="card-body border-bottom py-2">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm"
                        placeholder="Search by title or reference..." wire:model.live.debounce.400ms="search">
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" wire:model.live="filterModule">
                        <option value="">All Modules</option>
                        @foreach ($modules as $module)
                            <option value="{{ $module->code }}">{{ $module->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:11px;padding:10px 16px;">Reference</th>
                            <th style="font-size:11px;padding:10px 16px;">Title</th>
                            <th style="font-size:11px;padding:10px 16px;">Module</th>
                            <th style="font-size:11px;padding:10px 16px;">Current Level</th>
                            <th style="font-size:11px;padding:10px 16px;">Submitted By</th>
                            <th style="font-size:11px;padding:10px 16px;">Submitted</th>
                            <th style="font-size:11px;padding:10px 16px;" class="text-end">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($myQueue as $tx)
                            <tr wire:key="queue-{{ $tx->id }}">
                                <td style="padding:11px 16px;">
                                    <span class="font-monospace small fw-medium text-primary">
                                        {{ $tx->reference_no }}
                                    </span>
                                </td>
                                <td style="padding:11px 16px;">
                                    <div class="fw-medium small">{{ $tx->title }}</div>
                                    @if ($tx->description)
                                        <div class="text-muted" style="font-size:11px;">
                                            {{ Str::limit($tx->description, 60) }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding:11px 16px;">
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        {{ $tx->chainModule->name }}
                                    </span>
                                </td>
                                <td style="padding:11px 16px;">
                                    @php $currentFlow = $tx->currentFlowLevel(); @endphp
                                    <span class="badge bg-warning-subtle text-warning">
                                        L{{ $tx->current_level }}:
                                        {{ $currentFlow?->level_name ?? '—' }}
                                    </span>
                                </td>
                                <td style="padding:11px 16px;" class="small text-muted">
                                    {{ $tx->createdBy?->first_name }}
                                    {{ $tx->createdBy?->last_name }}
                                </td>
                                <td style="padding:11px 16px;" class="small text-muted">
                                    {{ $tx->created_at->diffForHumans() }}
                                </td>
                                <td style="padding:11px 16px;" class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('chain.transaction.timeline', $tx->id) }}"
                                            class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-success"
                                            wire:click="openAction({{ $tx->id }}, 'approve')" title="Approve">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning"
                                            wire:click="openAction({{ $tx->id }}, 'return')" title="Return for correction">
                                            <i class="fa fa-rotate-left"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            wire:click="openAction({{ $tx->id }}, 'reject')" title="Reject">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fa fa-check-circle fa-2x d-block mb-2
                                            text-success opacity-50"></i>
                                    Your approval queue is empty.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($myQueue->hasPages())
            <div class="card-footer py-2">{{ $myQueue->links() }}</div>
        @endif
    </div>

    {{-- ── All Transactions History ──────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between">
            <h6 class="mb-0">
                <i class="fa fa-history me-2 text-muted"></i>
                All Transactions
            </h6>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" wire:model.live="filterStatus" style="width:auto;">
                    <option value="">All Statuses</option>
                    @foreach (\App\Enums\ChainStatus::all() as $s)
                        <option value="{{ $s }}">
                            {{ \App\Enums\ChainStatus::label($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:11px;padding:10px 16px;">Reference</th>
                            <th style="font-size:11px;padding:10px 16px;">Title</th>
                            <th style="font-size:11px;padding:10px 16px;">Module</th>
                            <th style="font-size:11px;padding:10px 16px;">Status</th>
                            <th style="font-size:11px;padding:10px 16px;">Level</th>
                            <th style="font-size:11px;padding:10px 16px;">Created By</th>
                            <th style="font-size:11px;padding:10px 16px;">Date</th>
                            <th style="font-size:11px;padding:10px 16px;" class="text-end">
                                Detail
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($allTransactions as $tx)
                            <tr wire:key="all-{{ $tx->id }}">
                                <td style="padding:11px 16px;">
                                    <span class="font-monospace small">
                                        {{ $tx->reference_no }}
                                    </span>
                                </td>
                                <td style="padding:11px 16px;" class="small fw-medium">
                                    {{ $tx->title }}
                                </td>
                                <td style="padding:11px 16px;">
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        {{ $tx->chainModule->name }}
                                    </span>
                                </td>
                                <td style="padding:11px 16px;">
                                    <span class="badge bg-{{ $tx->statusBadgeColor() }}-subtle
                                            text-{{ $tx->statusBadgeColor() }}">
                                        {{ $tx->statusLabel() }}
                                    </span>
                                </td>
                                <td style="padding:11px 16px;" class="small text-muted">
                                    @if ($tx->isPending())
                                        Level {{ $tx->current_level }}
                                    @elseif ($tx->isCompleted())
                                        <i class="fa fa-check-circle text-success"></i> Done
                                    @else
                                        —
                                    @endif
                                </td>
                                <td style="padding:11px 16px;" class="small text-muted">
                                    {{ $tx->createdBy?->first_name }}
                                    {{ $tx->createdBy?->last_name }}
                                </td>
                                <td style="padding:11px 16px;" class="small text-muted">
                                    {{ $tx->created_at->format('d M Y') }}
                                </td>
                                <td style="padding:11px 16px;" class="text-end">
                                    <a href="{{ route('chain.transaction.timeline', $tx->id) }}"
                                        class="btn btn-sm btn-outline-info">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($allTransactions->hasPages())
            <div class="card-footer py-2">{{ $allTransactions->links() }}</div>
        @endif
    </div>

    {{-- ── Action Modal ─────────────────────────────────────────────────────── --}}
    @if ($actioningId && $actioningTransaction)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.45);">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        @php
                            $actionMeta = match ($actionType) {
                                'approve' => ['text-success', 'fa-check-circle', 'Approve Transaction'],
                                'reject' => ['text-danger', 'fa-times-circle', 'Reject Transaction'],
                                'return' => ['text-warning', 'fa-rotate-left', 'Return for Correction'],
                                default => ['text-muted', 'fa-circle', 'Action'],
                            };
                        @endphp
                        <h5 class="modal-title {{ $actionMeta[0] }}">
                            <i class="fa {{ $actionMeta[1] }} me-2"></i>
                            {{ $actionMeta[2] }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeAction"></button>
                    </div>
                    <div class="modal-body">

                        {{-- Transaction summary --}}
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Reference</small>
                                <span class="font-monospace small fw-medium">
                                    {{ $actioningTransaction->reference_no }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Title</small>
                                <span class="small fw-medium">
                                    {{ $actioningTransaction->title }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Current Level</small>
                                <span class="badge bg-warning-subtle text-warning">
                                    L{{ $actioningTransaction->current_level }}:
                                    {{ $actioningTransaction->currentFlowLevel()?->level_name }}
                                </span>
                            </div>
                        </div>

                        {{-- Reason (required for reject/return) --}}
                        @if (in_array($actionType, ['reject', 'return']))
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Reason <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" rows="3" wire:model.defer="reason" placeholder="{{ $actionType === 'reject'
                            ? 'Explain why this is being rejected...'
                            : 'Explain what needs to be corrected...' }}">
                                                </textarea>
                                        @error('reason')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                        @endif

                        {{-- Comments (always optional) --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Comments
                                @if ($actionType === 'approve')
                                    <span class="text-muted small">(optional)</span>
                                @endif
                            </label>
                            <textarea class="form-control" rows="2" wire:model.defer="comments"
                                placeholder="Additional comments..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button class="btn btn-secondary btn-sm" wire:click="closeAction">Cancel</button>
                        <button class="btn btn-sm
                                {{ $actionType === 'approve' ? 'btn-success'
            : ($actionType === 'reject' ? 'btn-danger' : 'btn-warning') }}"
                            wire:click="submitAction" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submitAction">
                                {{ $actionMeta[2] }}
                            </span>
                            <span wire:loading wire:target="submitAction">
                                <span class="spinner-border spinner-border-sm"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>