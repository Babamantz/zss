<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Payroll Entries</h4>
            <small class="text-muted">
                @if ($filterStatus)
                    Showing
                    <span class="badge bg-primary-subtle text-primary">
                        {{ str_replace('_', ' ', $filterStatus) }}
                    </span>
                    entries
                @else
                    All payroll entries across all periods
                @endif
            </small>
        </div>

        {{-- Clear filters button — only shown when something is active --}}
        @if ($filterStatus || $pay_period_id || $search)
            <button class="btn btn-sm btn-outline-secondary"
                wire:click="clearFilters">
                <i class="fa fa-times me-1"></i> Clear Filters
            </button>
        @endif
    </div>

    {{-- ── Totals Summary ───────────────────────────────────────────────────── --}}
    @if ($totals && ($totals->employee_count > 0))
        <div class="row g-3 mb-4">
            @foreach ([
                ['Employees',       $totals->employee_count, null,    'primary', 'fa-users'],
                ['Total Gross',     $totals->gross,          'TZS ',  'info',    'fa-arrow-up-circle'],
                ['Total Deductions',$totals->deductions,     'TZS ',  'danger',  'fa-arrow-down-circle'],
                ['Total Net Pay',   $totals->net,            'TZS ',  'success', 'fa-check-circle'],
            ] as [$label, $value, $prefix, $color, $icon])
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-{{ $color }}-subtle p-3 flex-shrink-0">
                                <i class="fa {{ $icon }} text-{{ $color }}"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-muted small mb-0">{{ $label }}</p>
                                <h5 class="mb-0 text-truncate">
                                    {{ $prefix }}{{ number_format($value ?? 0, $prefix ? 0 : 0) }}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Filters ──────────────────────────────────────────────────────────── --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">

                {{-- Pay Period --}}
                <div class="col-md-4">
                    <select class="form-select form-select-sm"
                        wire:model.live="pay_period_id">
                        <option value="">All Pay Periods</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}">
                                {{ \Carbon\Carbon::parse($period->start_date)->format('d M') }}
                                –
                                {{ \Carbon\Carbon::parse($period->end_date)->format('d M Y') }}
                                ({{ str_replace('_', ' ', $period->status) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="col-md-3">
                    <select class="form-select form-select-sm"
                        wire:model.live="filterStatus">
                        <option value="">All Statuses</option>
                        @foreach ([
                            'Draft'            => 'Draft',
                            'Processing'       => 'Processing',
                            'Pending_Approval' => 'Pending Approval',
                            'Approved'         => 'Approved',
                            'Rejected'         => 'Rejected',
                            'Locked_Completed' => 'Locked / Completed',
                        ] as $val => $label)
                            <option value="{{ $val }}"
                                {{ $filterStatus === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Search --}}
                <div class="col-md-4">
                    <input type="text"
                        class="form-control form-control-sm"
                        placeholder="Search employee name..."
                        wire:model.live.debounce.400ms="search">
                </div>

                {{-- Active filter badges --}}
                <div class="col-md-1 text-end">
                    @if ($filterStatus || $pay_period_id || $search)
                        <span class="badge bg-primary rounded-pill">
                            {{ collect([$filterStatus, $pay_period_id, $search])
                                ->filter()->count() }}
                            active
                        </span>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ── Table ────────────────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>OPF No.</th>
                            <th>Pay Period</th>
                            <th>Status</th>
                            <th class="text-end">Gross (TZS)</th>
                            <th class="text-end">Deductions (TZS)</th>
                            <th class="text-end">Net Pay (TZS)</th>
                            <th>Processed</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entries as $entry)
                            <tr>
                                <td class="text-muted small">
                                    {{ $loop->iteration + ($entries->currentPage() - 1) * $entries->perPage() }}
                                </td>

                                {{-- Employee --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary-subtle d-flex
                                            align-items-center justify-content-center flex-shrink-0"
                                            style="width:30px;height:30px;font-size:11px;
                                                   font-weight:600;color:#1a237e;">
                                            {{ strtoupper(substr($entry->employee->user->first_name, 0, 1)) }}
                                            {{ strtoupper(substr($entry->employee->user->last_name,  0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium small">
                                                {{ $entry->employee->user->first_name }}
                                                {{ $entry->employee->user->last_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- OPF --}}
                                <td class="small text-muted">
                                    {{ $entry->employee->opf_number ?? '—' }}
                                </td>

                                {{-- Pay Period --}}
                                <td class="small">
                                    {{ \Carbon\Carbon::parse($entry->payPeriod->start_date)->format('d M') }}
                                    –
                                    {{ \Carbon\Carbon::parse($entry->payPeriod->end_date)->format('d M Y') }}
                                </td>

                                {{-- Period Status badge --}}
                                <td>
                                    @php
                                        $sc = match($entry->payPeriod->status) {
                                            'Draft'            => ['secondary', 'Draft'],
                                            'Processing'       => ['warning',   'Processing'],
                                            'Pending_Approval' => ['info',      'Pending'],
                                            'Approved'         => ['primary',   'Approved'],
                                            'Rejected'         => ['danger',    'Rejected'],
                                            'Locked_Completed' => ['success',   'Locked'],
                                            default            => ['light',     'Unknown'],
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $sc[0] }}-subtle text-{{ $sc[0] }}">
                                        {{ $sc[1] }}
                                    </span>
                                </td>

                                {{-- Financials --}}
                                <td class="text-end small">
                                    {{ number_format($entry->total_gross, 2) }}
                                </td>
                                <td class="text-end small text-danger">
                                    {{ number_format($entry->total_deductions, 2) }}
                                </td>
                                <td class="text-end fw-bold text-success small">
                                    {{ number_format($entry->net_pay, 2) }}
                                </td>

                                {{-- Processed at --}}
                                <td class="small text-muted">
                                    {{ $entry->processed_at
                                        ? \Carbon\Carbon::parse($entry->processed_at)->format('d M Y H:i')
                                        : '—' }}
                                </td>

                                {{-- Actions --}}
                                <td class="text-end">
                                    <a href="{{ route('payroll.entry.show', $entry->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View Payslip">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="fa fa-list fa-2x d-block mb-2"></i>
                                    No payroll entries found
                                    @if ($filterStatus || $pay_period_id || $search)
                                        for the current filters.
                                        <a href="javascript:void(0)"
                                            wire:click="clearFilters"
                                            class="d-block mt-1 small">
                                            Clear filters
                                        </a>
                                    @else
                                        . Run payroll to generate entries.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- Running totals footer --}}
                    @if ($entries->count())
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end small text-muted">
                                    Page totals
                                </th>
                                <th class="text-end small text-info">
                                    {{ number_format($entries->sum('total_gross'), 2) }}
                                </th>
                                <th class="text-end small text-danger">
                                    {{ number_format($entries->sum('total_deductions'), 2) }}
                                </th>
                                <th class="text-end small text-success">
                                    {{ number_format($entries->sum('net_pay'), 2) }}
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($entries->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Showing {{ $entries->firstItem() }}–{{ $entries->lastItem() }}
                    of {{ $entries->total() }} entries
                </small>
                {{ $entries->links() }}
            </div>
        @endif
    </div>

</div>