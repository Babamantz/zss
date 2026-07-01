{{-- resources/views/chain/livewire/transaction-timeline.blade.php --}}

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('chain.queue') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="fa fa-arrow-left me-1"></i> Back
            </a>
            <span class="text-muted small">Transaction Detail</span>
        </div>
        <span class="font-monospace text-muted small">
            {{ $transaction->reference_no }}
        </span>
    </div>

    <div class="row g-4">

        {{-- ── Left: Transaction Info ───────────────────────────────────────── --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">Transaction Info</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <small class="text-muted">Reference</small>
                        <span class="font-monospace small fw-medium text-primary">
                            {{ $transaction->reference_no }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <small class="text-muted">Status</small>
                        <span class="badge bg-{{ $transaction->statusBadgeColor() }}-subtle
                            text-{{ $transaction->statusBadgeColor() }}">
                            {{ $transaction->statusLabel() }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <small class="text-muted">Module</small>
                        <span class="small fw-medium">
                            {{ $transaction->chainModule->name }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <small class="text-muted">Current Level</small>
                        <span class="badge bg-warning-subtle text-warning">
                            Level {{ $transaction->current_level }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <small class="text-muted">Created By</small>
                        <span class="small">
                            {{ $transaction->createdBy?->first_name }}
                            {{ $transaction->createdBy?->last_name }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <small class="text-muted">Created</small>
                        <span class="small text-muted">
                            {{ $transaction->created_at->format('d M Y H:i') }}
                        </span>
                    </div>
                    @if ($transaction->completed_at)
                        <div class="d-flex justify-content-between py-2">
                            <small class="text-muted">Completed</small>
                            <span class="small text-success">
                                {{ $transaction->completed_at->format('d M Y H:i') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            @if ($transaction->description)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0">Description</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-0">{{ $transaction->description }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Right: Approval Timeline ─────────────────────────────────────── --}}
        <div class="col-md-8">

            {{-- Workflow progress bar --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        {{-- Created step --}}
                        <div class="d-flex align-items-center gap-1">
                            <span class="rounded-circle d-flex align-items-center
                                justify-content-center bg-primary"
                                style="width:24px;height:24px;color:white;font-size:11px;">
                                <i class="fa fa-check" style="font-size:9px;"></i>
                            </span>
                            <small class="fw-medium">Created</small>
                        </div>

                        @foreach ($transaction->chainModule->activeFlows as $flow)
                                            @php
                                                $approval = $transaction->approvals
                                                    ->firstWhere('level_no', $flow->level_no);
                                                $isDone = $approval && $approval->isApproved();
                                                $isCurrent = $transaction->current_level === $flow->level_no
                                                    && $transaction->isPending();
                                                $isRejected = $approval && $approval->isRejected();
                                            @endphp
                                            <i class="fa fa-chevron-right text-muted" style="font-size:9px;"></i>
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="rounded-circle d-flex align-items-center
                                                        justify-content-center" style="width:24px;height:24px;color:white;font-size:11px;
                                                        background:{{ $isDone ? '#198754'
                            : ($isRejected ? '#dc3545'
                                : ($isCurrent ? '#fd7e14' : '#dee2e6')) }};">
                                                    @if ($isDone)
                                                        <i class="fa fa-check" style="font-size:9px;"></i>
                                                    @elseif ($isRejected)
                                                        <i class="fa fa-times" style="font-size:9px;"></i>
                                                    @else
                                                        {{ $flow->level_no }}
                                                    @endif
                                                </span>
                                                <small class="{{ $isCurrent ? 'fw-medium text-warning' : '' }}">
                                                    {{ $flow->level_name }}
                                                </small>
                                            </div>
                        @endforeach

                        <i class="fa fa-chevron-right text-muted" style="font-size:9px;"></i>
                        <div class="d-flex align-items-center gap-1">
                            <span class="rounded-circle d-flex align-items-center
                                justify-content-center" style="width:24px;height:24px;color:white;font-size:11px;
                                background:{{ $transaction->isCompleted() ? '#198754' : '#dee2e6' }};">
                                @if ($transaction->isCompleted())
                                    <i class="fa fa-check" style="font-size:9px;"></i>
                                @else
                                    <i class="fa fa-flag" style="font-size:9px;color:#6c757d;"></i>
                                @endif
                            </span>
                            <small class="{{ $transaction->isCompleted() ? 'fw-medium text-success' : 'text-muted' }}">
                                Completed
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Approval history timeline --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">Approval History</h6>
                </div>
                <div class="card-body">

                    {{-- Created entry --}}
                    <div class="d-flex gap-3 mb-4">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary d-flex align-items-center
                                justify-content-center" style="width:36px;height:36px;">
                                <i class="fa fa-plus text-white" style="font-size:12px;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium small">Transaction Created</span>
                                <span class="text-muted small">
                                    {{ $transaction->created_at->format('d M Y H:i') }}
                                </span>
                            </div>
                            <p class="text-muted small mb-0">
                                Created by
                                {{ $transaction->createdBy?->first_name }}
                                {{ $transaction->createdBy?->last_name }}
                                and submitted for approval.
                            </p>
                        </div>
                    </div>

                    {{-- Approval levels --}}
                    @foreach ($transaction->approvals as $approval)
                                        @php
                                            $statusColor = match ($approval->status) {
                                                'Approved' => ['bg-success', 'text-white', 'fa-check'],
                                                'Rejected' => ['bg-danger', 'text-white', 'fa-times'],
                                                'Returned' => ['bg-warning', 'text-dark', 'fa-rotate-left'],
                                                default => ['bg-secondary', 'text-white', 'fa-clock'],
                                            };
                                        @endphp
                                        <div class="d-flex gap-3 mb-4 position-relative">
                                            {{-- Connector line --}}
                                            @if (!$loop->last)
                                                <div style="position:absolute;left:17px;top:36px;
                                                            bottom:-16px;width:2px;background:#dee2e6;z-index:0;">
                                                </div>
                                            @endif

                                            <div class="flex-shrink-0" style="z-index:1;">
                                                <div class="rounded-circle {{ $statusColor[0] }}
                                                        d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                                    <i class="fa {{ $statusColor[2] }} {{ $statusColor[1] }}" style="font-size:12px;"></i>
                                                </div>
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <span class="fw-medium small">
                                                            Level {{ $approval->level_no }}:
                                                            {{ $approval->flowLevel?->level_name ?? '—' }}
                                                        </span>
                                                        @if ($approval->flowLevel?->role)
                                                            <span class="badge bg-secondary-subtle
                                                                        text-secondary ms-1" style="font-size:10px;">
                                                                {{ $approval->flowLevel->role->name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-{{ match ($approval->status) {
                            'Approved' => 'success',
                            'Rejected' => 'danger',
                            'Returned' => 'warning',
                            default => 'secondary',
                        } }}-subtle text-{{ match ($approval->status) {
                            'Approved' => 'success',
                            'Rejected' => 'danger',
                            'Returned' => 'warning',
                            default => 'secondary',
                        } }}">
                                                            {{ $approval->status }}
                                                        </span>
                                                        @if ($approval->actioned_at)
                                                            <div class="text-muted" style="font-size:11px;">
                                                                {{ $approval->actioned_at->format('d M Y H:i') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if ($approval->isPending())
                                                    <p class="text-warning small mb-0 mt-1">
                                                        <i class="fa fa-clock me-1"></i>
                                                        Awaiting action
                                                        @if ($approval->assignedTo)
                                                            from
                                                            {{ $approval->assignedTo->first_name }}
                                                            {{ $approval->assignedTo->last_name }}
                                                        @endif
                                                    </p>
                                                @else
                                                    @if ($approval->actionedBy)
                                                        <p class="text-muted small mb-1 mt-1">
                                                            <i class="fa fa-user me-1"></i>
                                                            Actioned by
                                                            {{ $approval->actionedBy->first_name }}
                                                            {{ $approval->actionedBy->last_name }}
                                                        </p>
                                                    @endif
                                                    @if ($approval->comments)
                                                        <div class="p-2 bg-light rounded mt-1" style="font-size:12px;">
                                                            <i class="fa fa-comment me-1 text-muted"></i>
                                                            {{ $approval->comments }}
                                                        </div>
                                                    @endif
                                                    @if ($approval->rejection_reason)
                                                        <div class="p-2 bg-danger-subtle rounded mt-1" style="font-size:12px;color:#842029;">
                                                            <i class="fa fa-exclamation-circle me-1"></i>
                                                            {{ $approval->rejection_reason }}
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                    @endforeach

                    {{-- Completed marker --}}
                    @if ($transaction->isCompleted())
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-success d-flex align-items-center
                                        justify-content-center" style="width:36px;height:36px;">
                                    <i class="fa fa-flag-checkered text-white" style="font-size:12px;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-medium small text-success">
                                    Workflow Completed
                                </span>
                                <p class="text-muted small mb-0">
                                    {{ $transaction->completed_at?->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>