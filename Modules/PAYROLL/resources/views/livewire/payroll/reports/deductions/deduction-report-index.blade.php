{{-- Modules/PAYROLL/resources/views/livewire/reports/deduction-report.blade.php --}}

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Deductions Report</h4>
            <small class="text-muted">
                Actual deductions applied across payroll runs
            </small>
        </div>
        {{-- <a href="{{ $this->getPdfUrl() }}"
            target="_blank"
            class="btn btn-sm btn-outline-danger">
            <i class="fa fa-file-pdf me-1"></i> Export PDF
        </a> --}}
    </div>

    {{-- ── Stat Cards ───────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @foreach ([
            ['Total Deducted',  'TZS ' . number_format($totals->total_amount  ?? 0, 0), 'fa-arrow-down-circle', 'danger'],
            ['Avg per Employee','TZS ' . number_format($totals->avg_amount    ?? 0, 0), 'fa-calculator',        'warning'],
            ['Highest',         'TZS ' . number_format($totals->max_amount    ?? 0, 0), 'fa-arrow-up',          'info'],
            ['Records',         number_format($totals->item_count ?? 0),                'fa-list',              'primary'],
        ] as [$label, $value, $icon, $color])
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-circle bg-{{ $color }}-subtle p-3 flex-shrink-0">
                            <i class="fa {{ $icon }} text-{{ $color }}"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">{{ $label }}</p>
                            <h5 class="mb-0">{{ $value }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">

        {{-- ── Left: Filters + Component Breakdown ────────────────────────── --}}
        <div class="col-md-12">

            {{-- Filters card --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent py-2 d-flex
                    justify-content-between align-items-center">
                    <h6 class="mb-0 small fw-medium">
                        <i class="fa fa-filter me-1 text-muted"></i> Filters
                    </h6>
                    @if ($filterComponentId || $filterEmployeeId || $filterPeriodId || $search)
                        <button class="btn btn-link btn-sm p-0 text-decoration-none
                            text-danger small" wire:click="clearFilters">
                            Clear all
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-3">

                    {{-- Search --}}
                    <div class="col-md-4">
                        <label class="form-label small fw-medium">Search</label>
                        <input type="text" class="form-control form-control-sm"
                            placeholder="Component or employee..."
                            wire:model.live.debounce.400ms="search">
                    </div>

                    {{-- Component (Entity) --}}
                    <div class="col-md-4">
                        <label class="form-label small fw-medium">
                            Deduction Component
                            <span class="text-muted">(Entity)</span>
                        </label>
                        <select class="form-select form-select-sm"
                            wire:model.live="filterComponentId">
                            <option value="">All Components</option>
                            @foreach ($components as $comp)
                                <option value="{{ $comp->component_id }}">
                                    {{ $comp->component->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Employee --}}
                    <div class="col-md-4">
                        <label class="form-label small fw-medium">Employee</label>
                        <select class="form-select form-select-sm"
                            wire:model.live="filterEmployeeId">
                            <option value="">All Employees</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->user->first_name }}
                                    {{ $emp->user->last_name }}
                                    ({{ $emp->opf_number ?? 'No OPF' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pay Period --}}

                    <div class="row align-items-end">
    <div class="col-md-4">
        <label class="form-label small fw-medium">Pay Period</label>
        <select class="form-select form-select-sm" wire:model.live="filterPeriodId">
            <option value="">All Periods</option>
            @foreach ($periods as $period)
                <option value="{{ $period->id }}">
                    {{ $period->start_date->format('d M') }}
                    –
                    {{ $period->end_date->format('d M Y') }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="col-md-4">
        <!-- Bootstrap 5 Form Check Wrapper -->
        <div class="form-check pb-1  mt-3">
            <input class="form-check-input" type="checkbox" id="componentTotal" wire:model.live="isComponentTotal">
            <label class="form-check-label small fw-medium" for="componentTotal">
                Component Total
            </label>
        </div>
    </div>
</div>

                </div>
            </div>

         

        </div>
        </div>
        {{-- ── Right: Results Table ─────────────────────────────────────────── --}}
        <div class="col-md-12">

            {{-- Active filter chips --}}
            @if ($filterComponentId || $filterEmployeeId || $filterPeriodId || $search)
                <div class="d-flex flex-wrap gap-1 mb-3 align-items-center">
                    <small class="text-muted me-1">Active filters:</small>

                    @if ($filterComponentId)
                        @php $fc = $components->find($filterComponentId); @endphp
                        <span class="badge bg-danger-subtle text-danger">
                            {{ $fc?->name }}
                            <a href="javascript:void(0)"
                                wire:click="$set('filterComponentId', null)"
                                class="ms-1 text-danger text-decoration-none">×</a>
                        </span>
                    @endif

                    @if ($filterEmployeeId)
                        @php $fe = $employees->find($filterEmployeeId); @endphp
                        <span class="badge bg-primary-subtle text-primary">
                            {{ $fe?->user->first_name }} {{ $fe?->user->last_name }}
                            <a href="javascript:void(0)"
                                wire:click="$set('filterEmployeeId', null)"
                                class="ms-1 text-primary text-decoration-none">×</a>
                        </span>
                    @endif

                    @if ($filterPeriodId)
                        @php $fp = $periods->find($filterPeriodId); @endphp
                        <span class="badge bg-secondary-subtle text-secondary">
                            {{ $fp?->start_date->format('M Y') }}
                            <a href="javascript:void(0)"
                                wire:click="$set('filterPeriodId', null)"
                                class="ms-1 text-secondary text-decoration-none">×</a>
                        </span>
                    @endif

                    @if ($search)
                        <span class="badge bg-info-subtle text-info">
                            "{{ $search }}"
                            <a href="javascript:void(0)"
                                wire:click="$set('search', '')"
                                class="ms-1 text-info text-decoration-none">×</a>
                        </span>
                    @endif
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size:11px;padding:11px 14px;">#</th>

                                    {{-- Sortable: Employee --}}
                                    <th style="font-size:11px;padding:11px 14px;">
                                        Employee
                                    </th>

                                    {{-- Sortable: Component --}}
                                    <th style="font-size:11px;padding:11px 14px;">
                                        <button class="btn p-0 border-0 small fw-semibold
                                            text-muted d-flex align-items-center gap-1"
                                            wire:click="sortBy('component_name_snapshot')">
                                            Component
                                            <i class="fa fa-sort{{
                                                $sortField === 'component_name_snapshot'
                                                    ? ($sortDir === 'asc' ? '-up' : '-down')
                                                    : ''
                                            }} opacity-{{ $sortField === 'component_name_snapshot' ? '100' : '30' }}"
                                                style="font-size:10px;"></i>
                                        </button>
                                    </th>

                                    <th style="font-size:11px;padding:11px 14px;">
                                        Pay Period
                                    </th>

                                    <th style="font-size:11px;padding:11px 14px;">
                                        Department
                                    </th>

                                    {{-- Sortable: Amount --}}
                                    <th style="font-size:11px;padding:11px 14px;"
                                        class="text-end">
                                        <button class="btn p-0 border-0 small fw-semibold
                                            text-muted d-flex align-items-center gap-1
                                            ms-auto"
                                            wire:click="sortBy('finalized_amount')">
                                            Amount (TZS)
                                            <i class="fa fa-sort{{
                                                $sortField === 'finalized_amount'
                                                    ? ($sortDir === 'asc' ? '-up' : '-down')
                                                    : ''
                                            }} opacity-{{ $sortField === 'finalized_amount' ? '100' : '30' }}"
                                                style="font-size:10px;"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr wire:key="ded-{{ $item->id }}">

                                        {{-- # --}}
                                        <td style="padding:11px 14px;"
                                            class="text-muted small">
                                            {{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                                        </td>

                                        {{-- Employee --}}
                                        <td style="padding:11px 14px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-primary-subtle
                                                    d-flex align-items-center
                                                    justify-content-center flex-shrink-0"
                                                    style="width:30px;height:30px;
                                                           font-size:10px;font-weight:600;
                                                           color:#1a237e;">
                                                    {{ strtoupper(substr($item->entry->employee->user->first_name, 0, 1)) }}
                                                    {{ strtoupper(substr($item->entry->employee->user->last_name,  0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-medium small">
                                                        {{ $item->entry->employee->user->first_name }}
                                                        {{ $item->entry->employee->user->last_name }}
                                                    </div>
                                                    <div class="text-muted"
                                                        style="font-size:10px;">
                                                        {{ $item->entry->employee->opf_number ?? '—' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Component --}}
                                        <td style="padding:11px 14px;">
                                            <span class="badge bg-danger-subtle text-danger">
                                                {{ $item->component_name_snapshot }}
                                            </span>
                                        </td>

                                        {{-- Pay Period --}}
                                        <td style="padding:11px 14px;" class="small text-muted">
                                            {{ $item->entry->payPeriod->start_date->format('d M') }}
                                            –
                                            {{ $item->entry->payPeriod->end_date->format('d M Y') }}
                                        </td>

                                        {{-- Department --}}
                                        <td style="padding:11px 14px;">
                                            @if ($item->entry->employee->department)
                                                <span class="badge bg-secondary-subtle text-secondary"
                                                    style="font-size:10px;">
                                                    {{ $item->entry->employee->department->name }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        {{-- Amount --}}
                                        <td style="padding:11px 14px;"
                                            class="text-end fw-bold text-danger small">
                                            {{ number_format($item->finalized_amount, 2) }}
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="text-center text-muted py-5">
                                            <i class="fa fa-minus-circle fa-2x d-block
                                                mb-2 opacity-25"></i>
                                            No deduction records found.
                                            @if ($filterComponentId || $filterEmployeeId
                                                || $filterPeriodId || $search)
                                                <a href="javascript:void(0)"
                                                    wire:click="clearFilters"
                                                    class="d-block mt-1 small text-primary">
                                                    Clear filters
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            {{-- Page totals --}}
                            @if ($items->count())
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5"
                                            style="padding:10px 14px;font-size:11px;"
                                            class="text-end text-muted">
                                            Page total
                                        </th>
                                        <th style="padding:10px 14px;font-size:11px;"
                                            class="text-end text-danger">
                                            TZS {{ number_format($items->sum('finalized_amount'), 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                @if ($items->hasPages())
                    <div class="card-footer d-flex justify-content-between
                        align-items-center py-2">
                        <small class="text-muted">
                            Showing {{ $items->firstItem() }}–{{ $items->lastItem() }}
                            of {{ $items->total() }} records
                            &bull;
                            <strong class="text-danger">
                                Grand total: TZS {{ number_format($totals->total_amount ?? 0, 2) }}
                            </strong>
                        </small>
                        {{ $items->links() }}
                    </div>
                @else
                    <div class="card-footer py-2">
                        <small class="text-muted">
                            {{ $items->total() }} record(s)
                            &bull;
                            <strong class="text-danger">
                                Total: TZS {{ number_format($totals->total_amount ?? 0, 2) }}
                            </strong>
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>