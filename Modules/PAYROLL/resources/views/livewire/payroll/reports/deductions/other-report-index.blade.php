{{-- Modules/PAYROLL/resources/views/livewire/reports/other-report-index.blade.php --}}

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Other Reports</h4>
            <small class="text-muted">
                {{ \Modules\PAYROLL\Enums\ReportType::description($reportType) }}
            </small>
        </div>
        {{-- <a href="{{ $this->getPdfUrl() }}"
            target="_blank"
            class="btn btn-sm btn-outline-danger">
            <i class="fa fa-file-pdf me-1"></i> Export PDF
        </a> --}}
    </div>

    {{-- ── Report Type Selector ────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2">
                @foreach ($reportTypes as $type)
                    @php
                        $color   = \Modules\PAYROLL\Enums\ReportType::badgeColor($type);
                        $isActive= $reportType === $type;
                    @endphp
                    <button wire:click="$set('reportType', '{{ $type }}')"
                        class="btn btn-sm {{ $isActive
                            ? "btn-{$color}"
                            : "btn-outline-{$color}" }}">
                        {{ \Modules\PAYROLL\Enums\ReportType::shortLabel($type) }}
                    </button>
                @endforeach
            </div>
            @if ($reportType)
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fa fa-circle-info me-1"></i>
                        {{ \Modules\PAYROLL\Enums\ReportType::description($reportType) }}
                    </small>
                </div>
            @endif
        </div>
    </div>

    {{-- ── SDL Report ───────────────────────────────────────────────────────── --}}
    @if ($reportType === \Modules\PAYROLL\Enums\ReportType::SDL)

        {{-- Stat Cards --}}
        @if ($totals)
            <div class="row g-3 mb-4">
                @foreach ([
                    ['Employees',       number_format($totals->employee_count ?? 0),          'fa-users',           'primary'],
                    ['Total Base Sal.', 'TZS ' . number_format($totals->total_base ?? 0, 0), 'fa-coins',           'info'],
                    ['Total Allowances','TZS ' . number_format($totals->total_allowances ?? 0, 0),'fa-plus-circle', 'warning'],
                    ['SDL Base',        'TZS ' . number_format($totals->total_sdl_base ?? 0, 0), 'fa-calculator',  'secondary'],
                    ['Total SDL (5%)',  'TZS ' . number_format($totals->total_sdl ?? 0, 0),  'fa-percent',         'danger'],
                ] as [$label, $value, $icon, $color])
                    <div class="col-md col-sm-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3 py-3">
                                <div class="rounded-circle bg-{{ $color }}-subtle p-3 flex-shrink-0">
                                    <i class="fa {{ $icon }} text-{{ $color }}"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-muted small mb-0">{{ $label }}</p>
                                    <h5 class="mb-0 text-truncate">{{ $value }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- SDL Formula Banner --}}
        <div class="alert alert-warning border-0 py-2 px-3 mb-3 small d-flex
            align-items-center gap-2">
            <i class="fa fa-function text-warning"></i>
            <span>
                <strong>SDL Formula:</strong>
                5% &times; (Base Salary + Allowances)
                &mdash;
                Allowances = any earning component with
                <code>allowance</code> in its name
            </span>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <div class="row g-2 align-items-center">

                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="fa fa-search text-muted" style="font-size:12px;"></i>
                            </span>
                            <input type="text"
                                class="form-control form-control-sm border-start-0 ps-0"
                                placeholder="Search employee..."
                                wire:model.live.debounce.400ms="search">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select class="form-select form-select-sm"
                            wire:model.live="filterPeriodId">
                            <option value="">All Pay Periods</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->id }}">
                                    {{ $period->start_date->format('d M') }}
                                    –
                                    {{ $period->end_date->format('d M Y') }}
                                    ({{ str_replace('_', ' ', $period->status) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
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

                    <div class="col-md-3 text-end">
                        @if ($filterPeriodId || $filterEmployeeId || $search)
                            <button class="btn btn-sm btn-outline-secondary"
                                wire:click="clearFilters">
                                <i class="fa fa-times me-1"></i> Clear
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- SDL Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="font-size:11px;padding:11px 14px;">#</th>
                                <th style="font-size:11px;padding:11px 14px;">Employee</th>
                                <th style="font-size:11px;padding:11px 14px;">Department</th>
                                <th style="font-size:11px;padding:11px 14px;">Pay Period</th>

                                {{-- Sortable columns --}}
                                @foreach ([
                                    ['base_salary',      'Base Salary (TZS)'],
                                    ['total_allowances', 'Allowances (TZS)'],
                                    ['sdl_base',         'SDL Base (TZS)'],
                                    ['sdl_amount',       'SDL 5% (TZS)'],
                                ] as [$field, $label])
                                    <th style="font-size:11px;padding:11px 14px;"
                                        class="text-end">
                                        <button class="btn p-0 border-0 small fw-semibold
                                            text-muted d-flex align-items-center
                                            gap-1 ms-auto"
                                            wire:click="sortBy('{{ $field }}')">
                                            {{ $label }}
                                            <i class="fa fa-sort{{
                                                $sortField === $field
                                                    ? ($sortDir === 'asc' ? '-up' : '-down')
                                                    : ''
                                            }} opacity-{{ $sortField === $field ? '100' : '30' }}"
                                                style="font-size:9px;"></i>
                                        </button>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr wire:key="sdl-{{ $row->id }}">

                                    <td style="padding:11px 14px;"
                                        class="text-muted small">
                                        {{ ($rows->currentPage() - 1) * $rows->perPage() + $loop->iteration }}
                                    </td>

                                    <td style="padding:11px 14px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-warning-subtle
                                                d-flex align-items-center
                                                justify-content-center flex-shrink-0"
                                                style="width:30px;height:30px;font-size:10px;
                                                       font-weight:600;color:#7b5e00;">
                                                {{ strtoupper(substr($row->employee->user->first_name, 0, 1)) }}
                                                {{ strtoupper(substr($row->employee->user->last_name,  0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium small">
                                                    {{ $row->employee->user->first_name }}
                                                    {{ $row->employee->user->last_name }}
                                                </div>
                                                <div class="text-muted"
                                                    style="font-size:10px;">
                                                    {{ $row->employee->opf_number ?? '—' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td style="padding:11px 14px;">
                                        @if ($row->employee->department)
                                            <span class="badge bg-secondary-subtle
                                                text-secondary" style="font-size:10px;">
                                                {{ $row->employee->department->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>

                                    <td style="padding:11px 14px;" class="small text-muted">
                                        {{ $row->payPeriod->start_date->format('d M') }}
                                        –
                                        {{ $row->payPeriod->end_date->format('d M Y') }}
                                    </td>

                                    <td style="padding:11px 14px;"
                                        class="text-end small">
                                        {{ number_format($row->base_salary, 2) }}
                                    </td>

                                    <td style="padding:11px 14px;"
                                        class="text-end small text-warning fw-medium">
                                        {{ number_format($row->total_allowances, 2) }}
                                    </td>

                                    <td style="padding:11px 14px;"
                                        class="text-end small text-secondary fw-medium">
                                        {{ number_format($row->sdl_base, 2) }}
                                    </td>

                                    <td style="padding:11px 14px;"
                                        class="text-end fw-bold text-danger">
                                        {{ number_format($row->sdl_amount, 2) }}
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8"
                                        class="text-center text-muted py-5">
                                        <i class="fa fa-percent fa-2x d-block
                                            mb-2 opacity-25"></i>
                                        No SDL data found for the selected filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if ($rows->count())
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4"
                                        style="padding:10px 14px;font-size:11px;"
                                        class="text-end text-muted">
                                        Page totals
                                    </th>
                                    <th style="padding:10px 14px;font-size:11px;"
                                        class="text-end text-info">
                                        TZS {{ number_format($rows->sum('base_salary'), 0) }}
                                    </th>
                                    <th style="padding:10px 14px;font-size:11px;"
                                        class="text-end text-warning">
                                        TZS {{ number_format($rows->sum('total_allowances'), 0) }}
                                    </th>
                                    <th style="padding:10px 14px;font-size:11px;"
                                        class="text-end text-secondary">
                                        TZS {{ number_format($rows->sum('sdl_base'), 0) }}
                                    </th>
                                    <th style="padding:10px 14px;font-size:11px;"
                                        class="text-end text-danger">
                                        TZS {{ number_format($rows->sum('sdl_amount'), 0) }}
                                    </th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            @if ($rows instanceof \Illuminate\Pagination\LengthAwarePaginator && $rows->hasPages())
                <div class="card-footer d-flex justify-content-between
                    align-items-center py-2">
                    <small class="text-muted">
                        Showing {{ $rows->firstItem() }}–{{ $rows->lastItem() }}
                        of {{ $rows->total() }} records
                        &bull;
                        <strong class="text-danger">
                            Grand SDL: TZS {{ number_format($totals->total_sdl ?? 0, 0) }}
                        </strong>
                    </small>
                    {{ $rows->links() }}
                </div>
            @endif
        </div>

    {{-- ── Placeholder for other report types ──────────────────────────────── --}}
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fa fa-chart-bar fa-3x text-muted d-block mb-3 opacity-25"></i>
                <h5 class="text-muted">
                    {{ \Modules\PAYROLL\Enums\ReportType::label($reportType) }}
                </h5>
                <p class="small text-muted">
                    This report type is not yet implemented.
                </p>
            </div>
        </div>
    @endif

</div>