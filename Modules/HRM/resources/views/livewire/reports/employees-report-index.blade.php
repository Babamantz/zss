@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
    <style>
        .sort-btn {
            background: none; border: none; padding: 0;
            color: inherit; cursor: pointer; font-size: 12px;
            font-weight: 600; text-transform: uppercase;
            letter-spacing: .05em;
        }
        .sort-btn:hover { color: #0d6efd; }
        thead th { white-space: nowrap; padding: 12px 16px; }
        tbody td { padding: 11px 16px; vertical-align: middle; font-size: 13px; }
        .avatar-circle {
            width: 34px; height: 34px; border-radius: 50%;
            background: #E8EAF6; color: #1a237e;
            font-size: 11px; font-weight: 600;
            display: flex; align-items: center;
            justify-content: center; flex-shrink: 0;
        }
    </style>
@endpush

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Employee Report</h4>
            <small class="text-muted">
                Filter, review and export employee data
            </small>
        </div>
        <button wire:click="downloadReport"
            wire:loading.attr="disabled"
            class="btn btn-success btn-sm">
            <span wire:loading.remove wire:target="downloadReport">
                <i class="fa fa-download me-1"></i> Export Excel
            </span>
            <span wire:loading wire:target="downloadReport">
                <span class="spinner-border spinner-border-sm me-1"></span>
                Generating...
            </span>
        </button>
    </div>

    {{-- ── Stat Cards ───────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @foreach ([
            ['Total Active',  $counts['total'],  'fa-users',       'primary'],
            ['Male',          $counts['male'],   'fa-person',      'info'],
            ['Female',        $counts['female'], 'fa-person-dress','warning'],
            ['Departments',   $counts['depts'],  'fa-building',    'success'],
        ] as [$label, $value, $icon, $color])
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-circle bg-{{ $color }}-subtle p-3 flex-shrink-0">
                            <i class="fa {{ $icon }} text-{{ $color }} fa-lg"></i>
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

    {{-- ── Filters ──────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">

                {{-- Search --}}
                <div class="col-md-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fa fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text"
                            class="form-control form-control-sm border-start-0 ps-0"
                            placeholder="Search by name..."
                            wire:model.live.debounce.400ms="search">
                    </div>
                </div>

                {{-- Gender --}}
                <div class="col-md-2">
                    <select wire:model.live="filterGender"
                        class="form-select form-select-sm">
                        <option value="">All Genders</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                {{-- Department --}}
                <div class="col-md-2">
                    <select wire:model.live="filterDept"
                        class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Division (scoped to selected department) --}}
                <div class="col-md-2">
                    <select wire:model.live="filterDivision"
                        class="form-select form-select-sm">
                        <option value="">All Divisions</option>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Unit --}}
                <div class="col-md-2">
                    <select wire:model.live="filterUnit"
                        class="form-select form-select-sm">
                        <option value="">
                             All Units
                        </option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status (defaults to All) --}}
                <div class="col-md-1">
                    <select wire:model.live="filterStatus"
                        class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="true">Active</option>
                        <option value="false">Inactive</option>
                    </select>
                </div>

                {{-- Per page --}}
                <div class="col-md-1">
                    <select wire:model.live="perPage"
                        class="form-select form-select-sm">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

            </div>

            <div class="row g-2 align-items-center mt-1">
                {{-- Clear --}}
                <div class="col-md-2 offset-md-10 text-end">
                    @if ($search || $filterGender || $filterDept || $filterDivision || $filterUnit || $filterStatus)
                        <button class="btn btn-sm btn-outline-secondary w-100"
                            wire:click="clearFilters"
                            title="Clear filters">
                            <i class="fa fa-times me-1"></i> Clear filters
                        </button>
                    @endif
                </div>
            </div>

            {{-- Active filter chips --}}
            @if ($search || $filterGender || $filterDept || $filterDivision || $filterUnit || $filterStatus)
                <div class="d-flex flex-wrap gap-1 mt-2">
                    <small class="text-muted me-1 align-self-center">Active:</small>

                    @if ($search)
                        <span class="badge bg-primary-subtle text-primary">
                            "{{ $search }}"
                            <a href="javascript:void(0)"
                                wire:click="$set('search', '')"
                                class="ms-1 text-primary text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterGender)
                        <span class="badge bg-info-subtle text-info">
                            {{ ucfirst($filterGender) }}
                            <a href="javascript:void(0)"
                                wire:click="$set('filterGender', '')"
                                class="ms-1 text-info text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterDept)
                        <span class="badge bg-warning-subtle text-warning">
                            {{ $departments->find($filterDept)?->name }}
                            <a href="javascript:void(0)"
                                wire:click="$set('filterDept', '')"
                                class="ms-1 text-warning text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterDivision)
                        <span class="badge bg-dark-subtle text-dark">
                            {{ $divisions->find($filterDivision)?->name }}
                            <a href="javascript:void(0)"
                                wire:click="$set('filterDivision', '')"
                                class="ms-1 text-dark text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterUnit)
                        <span class="badge bg-secondary-subtle text-secondary">
                            {{ $units->find($filterUnit)?->name }}
                            <a href="javascript:void(0)"
                                wire:click="$set('filterUnit', '')"
                                class="ms-1 text-secondary text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterStatus !== '')
                        <span class="badge bg-success-subtle text-success">
                            {{ $filterStatus === 'true' ? 'Active' : 'Inactive' }}
                            <a href="javascript:void(0)"
                                wire:click="$set('filterStatus', '')"
                                class="ms-1 text-success text-decoration-none">×</a>
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ── Table ────────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>

                            {{-- Name --}}
                            <th>
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('id')">
                                    Name
                                    <span class="text-muted" style="font-size:10px;">
                                        @if ($sortField === 'id')
                                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fa fa-sort opacity-30"></i>
                                        @endif
                                    </span>
                                </button>
                            </th>

                            {{-- Gender --}}
                            <th>
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('gender')">
                                    Gender
                                    <span class="text-muted" style="font-size:10px;">
                                        @if ($sortField === 'gender')
                                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fa fa-sort opacity-30"></i>
                                        @endif
                                    </span>
                                </button>
                            </th>

                            <th>Designation</th>

                            {{-- Department --}}
                            <th>
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('department_id')">
                                    Department
                                    <span class="text-muted" style="font-size:10px;">
                                        @if ($sortField === 'department_id')
                                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fa fa-sort opacity-30"></i>
                                        @endif
                                    </span>
                                </button>
                            </th>

                            {{-- Division --}}
                            <th>
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('division_id')">
                                    Division
                                    <span class="text-muted" style="font-size:10px;">
                                        @if ($sortField === 'division_id')
                                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fa fa-sort opacity-30"></i>
                                        @endif
                                    </span>
                                </button>
                            </th>

                            <th>Unit</th>

                            {{-- Hired Date --}}
                            <th>
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('hired_date')">
                                    Hired Date
                                    <span class="text-muted" style="font-size:10px;">
                                        @if ($sortField === 'hired_date')
                                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fa fa-sort opacity-30"></i>
                                        @endif
                                    </span>
                                </button>
                            </th>

                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $emp)
                            <tr wire:key="emp-{{ $emp->id }}">

                                {{-- # --}}
                                <td class="text-muted small">
                                    {{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}
                                </td>

                                {{-- Name with avatar --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle">
                                            {{ strtoupper(substr($emp->user?->first_name ?? '?', 0, 1)) }}{{ strtoupper(substr($emp->user?->last_name ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium" style="font-size:13px;">
                                                {{ $emp->user->first_name }}
                                                {{ $emp->user->middle_name }}
                                                {{ $emp->user->last_name }}
                                            </div>
                                            <div class="text-muted" style="font-size:11px;">
                                                OPF: {{ $emp->opf_number ?? '—' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Gender --}}
                                <td>
                                    <span class="badge {{ $emp->gender === 'male'
                                        ? 'bg-info-subtle text-info'
                                        : 'bg-warning-subtle text-warning' }}">
                                        <i class="fa {{ $emp->gender === 'male'
                                            ? 'fa-person'
                                            : 'fa-person-dress' }} fa-xs me-1"></i>
                                        {{ ucfirst($emp->gender) }}
                                    </span>
                                </td>

                                {{-- Designation --}}
                                <td class="small text-muted">
                                    {{ $emp->designation?->designation_name ?? '—' }}
                                </td>

                                {{-- Department --}}
                                <td>
                                    @if ($emp->division?->department)
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $emp->division->department->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>

                                {{-- Division --}}
                                <td>
                                    @if ($emp->division)
                                        <span class="badge bg-info-subtle text-info">
                                            {{ $emp->division->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>

                                {{-- Unit --}}
                                <td>
                                    @if ($emp->unit)
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ $emp->unit->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>

                                {{-- Hired Date --}}
                                <td class="small">
                                    @if ($emp->hired_date)
                                        <span>
                                            {{ \Carbon\Carbon::parse($emp->hired_date)->format('d M Y') }}
                                        </span>
                                        <div class="text-muted" style="font-size:11px;">
                                            {{ \Carbon\Carbon::parse($emp->hired_date)->diffForHumans() }}
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>
                                    <span class="badge {{ $emp->is_active === 'active'
                                        ? 'bg-success-subtle text-success'
                                        : 'bg-secondary-subtle text-secondary' }}">
                                        {{ $emp->is_active === 'active' ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fa fa-users fa-2x d-block mb-2 opacity-25"></i>
                                    No employees found
                                    @if ($search || $filterGender || $filterDept || $filterDivision || $filterUnit || $filterStatus)
                                        for the current filters.
                                        <a href="javascript:void(0)"
                                            wire:click="clearFilters"
                                            class="d-block mt-1 small">
                                            Clear filters
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Pagination ───────────────────────────────────────────────────── --}}
        @if ($employees->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2">
                <small class="text-muted">
                    Showing {{ $employees->firstItem() }}–{{ $employees->lastItem() }}
                    of {{ $employees->total() }} employees
                </small>
                {{ $employees->links() }}
            </div>
        @else
            <div class="card-footer py-2">
                <small class="text-muted">
                    {{ $employees->total() }} employee(s) found
                    @if ($filterStatus === 'true') (active only) @elseif ($filterStatus === 'false') (inactive only) @endif
                </small>
            </div>
        @endif
    </div>

</div>

@push('scripts')
    <script src="{{ asset('./assets/js/tooltip-init.js') }}"></script>
@endpush