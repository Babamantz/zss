{{-- resources/views/payroll/livewire/payroll/run-payroll.blade.php --}}

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Payroll Runs</h4>
            <small class="text-muted">
                Manage pay periods — process, submit for approval and lock payroll
            </small>
        </div>
        <a href="{{ route('payroll.pay-periods') }}"
            class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-calendar me-1"></i> Manage Pay Periods
        </a>
    </div>

    {{-- ── Flash ───────────────────────────────────────────────────────────── --}}
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

    {{-- ── Filters ──────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fa fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text"
                            class="form-control form-control-sm border-start-0 ps-0"
                            placeholder="Search by year or month..."
                            wire:model.live.debounce.400ms="search">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm"
                        wire:model.live="filterStatus">
                        <option value="">All Statuses</option>
                        {{-- @foreach (\Modules\PAYROLL\Enums\PayPeriodStatus::all() as $s)
                            <option value="{{ $s }}">
                                {{ \Modules\PAYROLL\Enums\PayPeriodStatus::label($s) }}
                            </option>
                        @endforeach --}}
                    </select>
                </div>
                <div class="col-md-5 text-end">
                    <small class="text-muted">
                        {{ $periods->total() }} period(s) found
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main Table ───────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:11px;padding:11px 14px;white-space:nowrap;">
                                Period
                            </th>
                            <th style="font-size:11px;padding:11px 14px;">Status</th>
                            <th style="font-size:11px;padding:11px 14px;" class="text-end">
                                Employees
                            </th>
                            <th style="font-size:11px;padding:11px 14px;" class="text-end">
                                Gross (TZS)
                            </th>
                            <th style="font-size:11px;padding:11px 14px;" class="text-end">
                                Allowances (TZS)
                            </th>
                            <th style="font-size:11px;padding:11px 14px;" class="text-end">
                                Deductions (TZS)
                            </th>
                            <th style="font-size:11px;padding:11px 14px;" class="text-end">
                                Net Pay (TZS)
                            </th>
                            <th style="font-size:11px;padding:11px 14px;" class="text-end">
                                Cost (TZS)
                            </th>
                            <th style="font-size:11px;padding:11px 14px;" class="text-end">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($periods as $period)
                                                        <tr wire:key="period-{{ $period->id }}">

                                                            {{-- Period dates --}}
                                                            <td style="padding:11px 14px;">
                                                                <div class="fw-medium small">
                                                                    {{ $period->start_date->format('d M Y') }}
                                                                    &ndash;
                                                                    {{ $period->end_date->format('d M Y') }}
                                                                </div>
                                                                <div class="text-muted" style="font-size:11px;">
                                                                    {{ $period->start_date->format('F Y') }}
                                                                </div>
                                                            </td>

                                                            {{-- Status --}}
                                                            <td style="padding:11px 14px;">
                                                                <span class="badge bg-{{ $period->statusBadgeColor() }}-subtle
                                                                    text-{{ $period->statusBadgeColor() }}"
                                                                    style="font-size:10px;">
                                                                    {{ $period->statusLabel() }}
                                                                </span>

                                                                {{-- Chain approval indicator --}}
                                                                @if ($period->chainTransaction)
                                                                    @php
                                                                        $lastApproval = $period->chainTransaction->approvals
                                                                            ->sortByDesc('level_no')->first();
                                                                    @endphp
                                                                    @if ($lastApproval)
                                                                        <div class="text-muted mt-1" style="font-size:10px;">
                                                                            L{{ $lastApproval->level_no }}:
                                                                            {{ $lastApproval->status }}
                                                                            @if ($lastApproval->actioned_at)
                                                                                · {{ \Carbon\Carbon::parse($lastApproval->actioned_at)->format('d M') }}
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            </td>

                                                            {{-- Employee count --}}
                                                            <td style="padding:11px 14px;" class="text-end small fw-medium">
                                                                {{ number_format($period->employee_count) }}
                                                            </td>

                                                            {{-- Gross --}}
                                                            <td style="padding:11px 14px;" class="text-end small">
                                                                @if ($period->total_gross > 0)
                                                                    {{ number_format($period->total_gross, 0) }}
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>

                                                            {{-- Allowances --}}
                                                            <td style="padding:11px 14px;" class="text-end small text-info">
                                                                @if ($period->total_allowances > 0)
                                                                    {{ number_format($period->total_allowances, 0) }}
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>

                                                            {{-- Deductions --}}
                                                            <td style="padding:11px 14px;" class="text-end small text-danger">
                                                                @if ($period->total_deductions > 0)
                                                                    {{ number_format($period->total_deductions, 0) }}
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>

                                                            {{-- Net --}}
                                                            <td style="padding:11px 14px;" class="text-end small fw-bold text-success">
                                                                @if ($period->total_net > 0)
                                                                    {{ number_format($period->total_net, 0) }}
                                                                @else
                                                                    <span class="text-muted fw-normal">—</span>
                                                                @endif
                                                            </td>

                                                            {{-- Cost --}}
                                                            <td style="padding:11px 14px;" class="text-end small text-primary">
                                                                @if ($period->total_cost > 0)
                                                                    {{ number_format($period->total_cost, 0) }}
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>

                                                            {{-- Actions --}}

                                                                    {{-- Table row actions --}}
                            <td style="padding:11px 14px;" class="text-end">
                                <div class="d-flex gap-1 justify-content-end">

                                    {{-- Open modal (check / process) --}}
                                    <button class="btn btn-sm btn-outline-primary"
                                        wire:click="openPeriod({{ $period->id }})"
                                        title="View & Process">
                                        <i class="fa fa-play"></i>
                                    </button>

                                    {{-- Check — payroll list PDF (viewable in new tab) --}}
                                    <a class="btn btn-sm btn-outline-info"
                                        href="{{ route('payroll.period.list-pdf', $period->id) }}"
                                        target="_blank"
                                        title="View Payroll List (PDF)">
                                        <i class="fa fa-file"></i>
                                    </a>

                                    {{-- Entries link --}}
                                    <a class="btn btn-sm btn-outline-secondary"
                                        href="{{ route('payroll.entries', ['pay_period_id' => $period->id]) }}"
                                        title="View Entries">
                                        <i class="fa fa-list"></i>
                                    </a>

                                </div>
                            </td>



                                                                    {{-- Download / payslip check --}}
                                                                    {{-- @if ($period->isLocked())
                                                                        <a class="btn btn-sm btn-outline-success"
                                                                            href="{{ route('payroll.reports.payslips', ['period' => $period->id]) }}"
                                                                            title="Download Payslips">
                                                                            <i class="fa fa-download"></i>
                                                                        </a>
                                                                    @endif --}}

                                                                </div>
                                                            </td>

                                                        </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fa fa-calendar fa-2x d-block mb-2 opacity-25"></i>
                                    No pay periods found.
                                    <a href="{{ route('payroll.pay-periods') }}"
                                        class="d-block mt-1 small text-primary">
                                        Create a pay period first
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- Page totals footer --}}
                    @if ($periods->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" style="padding:10px 14px;"
                                    class="small text-muted fw-medium">
                                    Page totals
                                </td>
                                <td style="padding:10px 14px;"
                                    class="text-end small fw-medium">
                                    {{ number_format($periods->sum('employee_count')) }}
                                </td>
                                <td style="padding:10px 14px;"
                                    class="text-end small fw-medium">
                                    {{ number_format($periods->sum('total_gross'), 0) }}
                                </td>
                                <td style="padding:10px 14px;"
                                    class="text-end small fw-medium text-info">
                                    {{ number_format($periods->sum('total_allowances'), 0) }}
                                </td>
                                <td style="padding:10px 14px;"
                                    class="text-end small fw-medium text-danger">
                                    {{ number_format($periods->sum('total_deductions'), 0) }}
                                </td>
                                <td style="padding:10px 14px;"
                                    class="text-end small fw-bold text-success">
                                    {{ number_format($periods->sum('total_net'), 0) }}
                                </td>
                                <td style="padding:10px 14px;"
                                    class="text-end small fw-medium text-primary">
                                    {{ number_format($periods->sum('total_cost'), 0) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        @if ($periods->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2">
                <small class="text-muted">
                    Showing {{ $periods->firstItem() }}–{{ $periods->lastItem() }}
                    of {{ $periods->total() }} periods
                </small>
                {{ $periods->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         PERIOD MODAL
    ══════════════════════════════════════════════════════════════════════ --}}
    @if ($showModal && $selectedPeriod)
        <div class="modal fade show d-block" tabindex="-1"
            style="background:rgba(0,0,0,.5);">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">

                    {{-- ── Modal Header ─────────────────────────────────────────── --}}
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h5 class="modal-title mb-0">
                                {{ $selectedPeriod->start_date->format('d M Y') }}
                                &ndash;
                                {{ $selectedPeriod->end_date->format('d M Y') }}
                            </h5>
                            <small class="text-muted">
                                {{ $selectedPeriod->start_date->format('F Y') }} Payroll
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $selectedPeriod->statusBadgeColor() }} px-3 py-2"
                                style="font-size:12px;">
                                {{ $selectedPeriod->statusLabel() }}
                            </span>
                            <button type="button" class="btn-close"
                                wire:click="closeModal"></button>
                        </div>
                    </div>

                    <div class="modal-body">

                        {{-- ── Status Notice Banner ─────────────────────────────── --}}
                        @if ($selectedPeriod->isDraft())
                            <div class="alert alert-warning py-2 px-3 small mb-4 d-flex
                                align-items-center gap-2">
                                <i class="fa fa-circle-info"></i>
                                This period is in <strong>Draft</strong> status.
                                Confirm and click <strong>Process Payroll</strong> to run it.
                            </div>
                        @elseif ($selectedPeriod->isProcessing())
                            <div class="alert alert-info py-2 px-3 small mb-4 d-flex
                                align-items-center gap-2">
                                <i class="fa fa-circle-check"></i>
                                This period has been <strong>processed</strong>.
                                Showing existing payroll data below.
                                You can re-process or lock to complete.
                            </div>
                        @elseif ($selectedPeriod->isLocked())
                            <div class="alert alert-success py-2 px-3 small mb-4 d-flex
                                align-items-center gap-2">
                                <i class="fa fa-lock"></i>
                                This period is <strong>Locked & Completed</strong>.
                                No further changes are allowed.
                            </div>
                        @endif

                        {{-- ── Summary Cards ────────────────────────────────────── --}}
                        <div class="row g-3 mb-4">
                            @foreach ([
                                    ['Employees', $selectedPeriod->employee_count, null, 'primary', 'fa-users'],
                                    ['Gross Salary', $selectedPeriod->total_gross, 'TZS ', 'info', 'fa-arrow-up-circle'],
                                    ['Allowances', $selectedPeriod->total_allowances, 'TZS ', 'warning', 'fa-plus-circle'],
                                    ['Deductions', $selectedPeriod->total_deductions, 'TZS ', 'danger', 'fa-minus-circle'],
                                    ['Net Pay', $selectedPeriod->total_net, 'TZS ', 'success', 'fa-check-circle'],
                                    ['Total Cost', $selectedPeriod->total_cost, 'TZS ', 'secondary', 'fa-calculator'],
                                ] as [$label, $val, $pfx, $color, $icon])
                                    <div class="col-md-4 col-sm-6">
                                        <div class="card border-0 bg-{{ $color }}-subtle">
                                            <div class="card-body d-flex align-items-center gap-3 py-3">
                                                <div class="rounded-circle bg-white p-2 flex-shrink-0"
                                                    style="width:36px;height:36px;display:flex;
                                                           align-items:center;justify-content:center;">
                                                    <i class="fa {{ $icon }} text-{{ $color }}"></i>
                                                </div>
                                                <div>
                                                    <p class="text-muted small mb-0">{{ $label }}</p>
                                                    <h5 class="mb-0 text-{{ $color }}">
                                                        @if ($pfx)
                                                            {{ $pfx }}{{ number_format($val ?? 0, 0) }}
                                                        @else
                                                            {{ number_format($val ?? 0) }}
                                                        @endif
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                        </div>

                        {{-- ── Workflow Steps ────────────────────────────────────── --}}
                        @php
                            $statusOrder = ['Draft' => 0, 'Processing' => 1, 'Locked_Completed' => 2];
                            $current = $statusOrder[$selectedPeriod->status] ?? 0;
                            $steps = [
                                ['Draft', 'secondary'],
                                ['Processing', 'info'],
                                ['Locked', 'success'],
                            ];
                        @endphp
                        <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
                            @foreach ($steps as $i => [$label, $color])
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="rounded-circle d-flex align-items-center
                                                        justify-content-center"
                                                        style="width:24px;height:24px;font-size:10px;
                                                               color:white;font-weight:600;
                                                               background:{{ $i <= $current
                                ? "var(--bs-{$color})"
                                : '#dee2e6' }};">
                                                        @if ($i < $current)
                                                            <i class="fa fa-check" style="font-size:9px;"></i>
                                                        @else
                                                            {{ $i + 1 }}
                                                        @endif
                                                    </span>
                                                    <small class="{{ $i <= $current ? 'fw-medium' : 'text-muted' }}"
                                                        style="font-size:12px;">
                                                        {{ $label }}
                                                    </small>
                                                    @if (!$loop->last)
                                                        <i class="fa fa-chevron-right text-muted"
                                                            style="font-size:9px;margin:0 4px;"></i>
                                                    @endif
                                                </div>
                            @endforeach
                        </div>

                        {{-- ── Action Panel ─────────────────────────────────────── --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-2">
                                <h6 class="mb-0 small fw-medium">
                                    <i class="fa fa-bolt me-1 text-primary"></i>
                                    Actions
                                </h6>
                            </div>
                            <div class="card-body">

                                {{-- Confirmation checkbox — only for Draft --}}
                                {{-- @if ($selectedPeriod->isDraft()) --}}
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model.live="confirmed"
                                            id="confirm_run_modal">
                                        <label class="form-check-label small"
                                            for="confirm_run_modal">
                                            I confirm all employee finance profiles are configured.
                                            Proceeding will generate payroll entries for all
                                            active employees.
                                        </label>
                                    </div>
                                {{-- @endif --}}

                                <div class="d-flex flex-wrap gap-2">

                                    {{-- PROCESS — only for Draft --}}
                                    {{-- @if ($selectedPeriod->isDraft()) --}}
                                        <button class="btn btn-primary btn-sm"
                                            wire:click="process"
                                            wire:loading.attr="disabled"
                                            x-bind:disabled="!$wire.confirmed">
                                            <span wire:loading.remove wire:target="process">
                                                <i class="fa fa-play me-1"></i> Process Payroll
                                            </span>
                                            <span wire:loading wire:target="process">
                                                <span class="spinner-border spinner-border-sm me-1"></span>
                                                Processing...
                                            </span>
                                        </button>
                                    {{-- @endif --}}

                                    {{-- RE-PROCESS — only for Processing status --}}
                                    {{-- @if ($selectedPeriod->isProcessing()) --}}
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model.live="confirmed"
                                                    id="confirm_reprocess_modal">
                                                <label class="form-check-label small text-warning"
                                                    for="confirm_reprocess_modal">
                                                    I confirm re-processing will
                                                    <strong>overwrite existing entries</strong>.
                                                </label>
                                            </div>
                                            <button class="btn btn-warning btn-sm text-dark"
                                                wire:click="process"
                                                wire:loading.attr="disabled"
                                                x-bind:disabled="!$wire.confirmed">
                                                <span wire:loading.remove wire:target="process">
                                                    <i class="fa fa-rotate-right me-1"></i>
                                                    Re-process Payroll
                                                </span>
                                                <span wire:loading wire:target="process">
                                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                                    Re-processing...
                                                </span>
                                            </button>
                                        </div>
                                    {{-- @endif --}}

                                    {{-- LOCK — only for Processing --}}
                                    {{-- @if (
                                            $selectedPeriod->isProcessing()
                                            && $selectedPeriod->entries()->exists()
                                        ) --}}
                                            <button class="btn btn-dark btn-sm"
                                                wire:click="lock"
                                                wire:confirm="Lock this period permanently? This cannot be undone."
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="lock">
                                                    <i class="fa fa-lock me-1"></i> Lock & Complete
                                                </span>
                                                <span wire:loading wire:target="lock">
                                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                                    Locking...
                                                </span>
                                            </button>
                                    {{-- @endif --}}

                                    {{-- VIEW ENTRIES --}}
                                    {{-- @if ($selectedPeriod->isProcessing() || $selectedPeriod->isLocked()) --}}
                                        <a class="btn btn-outline-secondary btn-sm"
                                            href="{{ route('payroll.entries', ['pay_period_id' => $selectedPeriod->id]) }}">
                                            <i class="fa fa-list me-1"></i> View Entries
                                        </a>
                                    {{-- @endif --}}

                                    {{-- VIEW PAYROLL LIST PDF --}}
                                    {{-- @if ($selectedPeriod->isProcessing() || $selectedPeriod->isLocked()) --}}
                                        <a class="btn btn-outline-info btn-sm"
                                            href="{{ route('payroll.period.list-pdf', $selectedPeriod->id) }}"
                                            target="_blank">
                                            <i class="fa fa-file-pdf me-1"></i> View Payroll List
                                        </a>
                                    {{-- @endif --}}

                                    {{-- DOWNLOAD PAYSLIPS — locked only --}}
                                    {{-- @if ($selectedPeriod->isLocked()) --}}
                                        {{-- <a class="btn btn-outline-success btn-sm"
                                            href="{{ route('payroll.reports.payslips', ['period' => $selectedPeriod->id]) }}">
                                            <i class="fa fa-download me-1"></i> Download Payslips
                                        </a> --}}
                                    {{-- @endif --}}

                                </div>
                            </div>
                        </div>

                        {{-- ── Payroll Results Table ─────────────────────────────── --}}
                        {{-- Shows when: (a) just processed, or (b) loaded from existing entries --}}
                        @if ($processed && count($results))
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent py-2
                                    d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 small fw-medium">
                                        @if ($selectedPeriod->isProcessing())
                                            <i class="fa fa-circle-check text-info me-1"></i>
                                            Existing Payroll Data
                                        @else
                                            <i class="fa fa-circle-check text-success me-1"></i>
                                            Processing Results
                                        @endif
                                        <span class="badge bg-primary-subtle text-primary ms-1">
                                            {{ count($results) }} employees
                                        </span>
                                    </h6>
                                    <small class="text-muted">
                                        @if ($selectedPeriod->isProcessing())
                                            Loaded from existing entries — lock to finalize
                                        @else
                                            Review before locking
                                        @endif
                                    </small>
                                </div>

                                {{-- Results summary bar --}}
                                @php
                                    $rGross = collect($results)->sum('gross');
                                    $rDeductions = collect($results)->sum('deductions');
                                    $rNet = collect($results)->sum('net_pay');
                                @endphp
                                <div class="card-body border-bottom py-2">
                                    <div class="row text-center g-0">
                                        <div class="col-3">
                                            <p class="text-muted small mb-0" style="font-size:11px;">
                                                Employees
                                            </p>
                                            <span class="fw-bold">{{ count($results) }}</span>
                                        </div>
                                        <div class="col-3">
                                            <p class="text-muted small mb-0" style="font-size:11px;">
                                                Total Gross
                                            </p>
                                            <span class="fw-bold text-info small">
                                                TZS {{ number_format($rGross, 0) }}
                                            </span>
                                        </div>
                                        <div class="col-3">
                                            <p class="text-muted small mb-0" style="font-size:11px;">
                                                Total Deductions
                                            </p>
                                            <span class="fw-bold text-danger small">
                                                TZS {{ number_format($rDeductions, 0) }}
                                            </span>
                                        </div>
                                        <div class="col-3">
                                            <p class="text-muted small mb-0" style="font-size:11px;">
                                                Total Net Pay
                                            </p>
                                            <span class="fw-bold text-success small">
                                                TZS {{ number_format($rNet, 0) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Results table --}}
                                <div class="table-responsive"
                                    style="max-height:320px;overflow-y:auto;">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light"
                                            style="position:sticky;top:0;z-index:1;">
                                            <tr>
                                                <th style="font-size:10px;padding:7px 10px;">#</th>
                                                <th style="font-size:10px;padding:7px 10px;">
                                                    Employee
                                                </th>
                                                <th style="font-size:10px;padding:7px 10px;">
                                                    Type
                                                </th>
                                                <th style="font-size:10px;padding:7px 10px;"
                                                    class="text-end">Gross</th>
                                                <th style="font-size:10px;padding:7px 10px;"
                                                    class="text-end">Deductions</th>
                                                <th style="font-size:10px;padding:7px 10px;"
                                                    class="text-end">Net Pay</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($results as $i => $row)
                                                                            <tr>
                                                                                <td style="padding:7px 10px;"
                                                                                    class="text-muted small">
                                                                                    {{ $i + 1 }}
                                                                                </td>
                                                                                <td style="padding:7px 10px;">
                                                                                    <div class="fw-medium"
                                                                                        style="font-size:12px;">
                                                                                        {{ $row['employee_name'] }}
                                                                                    </div>
                                                                                    <div class="text-muted"
                                                                                        style="font-size:10px;">
                                                                                        {{ $row['opf_number'] }}
                                                                                        @if (!empty($row['tenant']))
                                                                                            &bull; {{ $row['tenant'] }}
                                                                                        @endif
                                                                                    </div>
                                                                                </td>
                                                                                <td style="padding:7px 10px;">
                                                                                    <span class="badge {{
                                                ($row['employment_type'] ?? '') === 'permanent'
                                                ? 'bg-primary-subtle text-primary'
                                                : 'bg-warning-subtle text-warning'
                                                                                    }}" style="font-size:9px;">
                                                                                        {{ ucfirst($row['employment_type'] ?? '—') }}
                                                                                    </span>
                                                                                </td>
                                                                                <td style="padding:7px 10px;"
                                                                                    class="text-end small">
                                                                                    {{ number_format($row['gross'], 0) }}
                                                                                </td>
                                                                                <td style="padding:7px 10px;"
                                                                                    class="text-end small text-danger">
                                                                                    {{ number_format($row['deductions'], 0) }}
                                                                                </td>
                                                                                <td style="padding:7px 10px;"
                                                                                    class="text-end small fw-bold text-success">
                                                                                    {{ number_format($row['net_pay'], 0) }}
                                                                                </td>
                                                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="3"
                                                    style="padding:7px 10px;font-size:10px;"
                                                    class="text-end">
                                                    Totals
                                                </th>
                                                <th style="padding:7px 10px;font-size:10px;"
                                                    class="text-end text-info">
                                                    TZS {{ number_format($rGross, 0) }}
                                                </th>
                                                <th style="padding:7px 10px;font-size:10px;"
                                                    class="text-end text-danger">
                                                    TZS {{ number_format($rDeductions, 0) }}
                                                </th>
                                                <th style="padding:7px 10px;font-size:10px;"
                                                    class="text-end text-success">
                                                    TZS {{ number_format($rNet, 0) }}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                        @elseif ($selectedPeriod->isDraft() && !$processed)
                            {{-- Empty state for Draft --}}
                            <div class="text-center text-muted py-4">
                                <i class="fa fa-circle-play fa-3x d-block mb-2 opacity-25"></i>
                                <h6 class="text-muted">No payroll data yet</h6>
                                <p class="small">
                                    Confirm above and click
                                    <strong>Process Payroll</strong> to begin.
                                </p>
                            </div>
                        @endif

                    </div>

                    {{-- ── Modal Footer ──────────────────────────────────────────── --}}
                    <div class="modal-footer border-0 py-2">
                        <button class="btn btn-secondary btn-sm"
                            wire:click="closeModal">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif


</div>