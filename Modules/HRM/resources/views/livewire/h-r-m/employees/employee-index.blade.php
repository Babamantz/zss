@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
    <style>
        .sort-btn {
            background: none;
            border: none;
            padding: 0;
            color: inherit;
            cursor: pointer;
        }

        .sort-btn:hover {
            color: #0d6efd;
        }

        .th-sortable {
            white-space: nowrap;
        }

        tbody tr {
            transition: background .1s;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #E8EAF6;
            color: #1a237e;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        thead th {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
            white-space: nowrap;
            padding: 12px 16px;
        }

        tbody td {
            padding: 11px 16px;
            vertical-align: middle;
            font-size: 13px;
        }
    </style>
@endpush

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Employees</h4>
            <small class="text-muted">Manage all registered employees and their records</small>
        </div>
        <a class="btn btn-primary btn-sm" href="{{ route('hrm.employees.create') }}">
            <i class="fa fa-plus me-1"></i> Add Employee
        </a>
    </div>

    {{-- ── Flash ───────────────────────────────────────────────────────────── --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Stat Cards ───────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @foreach ([
                ['Total', $counts['total'], 'fa-users', 'primary'],
                ['Active', $counts['active'], 'fa-circle-check', 'success'],
                ['Inactive', $counts['inactive'], 'fa-circle-xmark', 'secondary'],
                ['Male', $counts['male'], 'fa-person', 'info'],
                ['Female', $counts['female'], 'fa-person-dress', 'pink'],
            ] as [$label, $value, $icon, $color])
            <div class="col">
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

    {{-- ── Filters ──────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">

                {{-- Search --}}
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fa fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" class="form-control form-control-sm border-start-0 ps-0"
                            placeholder="Search name or email..." wire:model.live.debounce.400ms="search">
                    </div>
                </div>

                {{-- Department --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filterDepartment">
                        <option value="">All Departments</option>
                        @foreach ($this->departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Unit (filtered by department) --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filterUnit">
                        <option value="">All Units</option>
                        @foreach ($this->units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filterStatus">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="in-active">Inactive</option>
                    </select>
                </div>

                {{-- Gender --}}
                <div class="col-md-1">
                    <select class="form-select form-select-sm" wire:model.live="filterGender">
                        <option value="">Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                {{-- Per page --}}
                <div class="col-md-1">
                    <select class="form-select form-select-sm" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>

                {{-- Clear filters --}}
                <div class="col-md-1 text-end">
                    @if ($search || $filterDepartment || $filterUnit || $filterStatus || $filterGender)
                        <button class="btn btn-sm btn-outline-secondary w-100" wire:click="clearFilters"
                            title="Clear all filters">
                            <i class="fa fa-times"></i>
                        </button>
                    @endif
                </div>

            </div>

            {{-- Active filter chips --}}
            @if ($search || $filterDepartment || $filterUnit || $filterStatus || $filterGender)
                <div class="d-flex flex-wrap gap-1 mt-2">
                    <small class="text-muted me-1 align-self-center">Filters:</small>
                    @if ($search)
                        <span class="badge bg-primary-subtle text-primary">
                            "{{ $search }}"
                            <a href="javascript:void(0)" wire:click="$set('search','')"
                                class="ms-1 text-primary text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterDepartment)
                        <span class="badge bg-info-subtle text-info">
                            Dept: {{ $this->departments->find($filterDepartment)?->name }}
                            <a href="javascript:void(0)" wire:click="$set('filterDepartment','')"
                                class="ms-1 text-info text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterUnit)
                        <span class="badge bg-secondary-subtle text-secondary">
                            Unit: {{ $this->units->find($filterUnit)?->name }}
                            <a href="javascript:void(0)" wire:click="$set('filterUnit','')"
                                class="ms-1 text-secondary text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterStatus)
                        <span class="badge bg-success-subtle text-success">
                            {{ ucfirst($filterStatus) }}
                            <a href="javascript:void(0)" wire:click="$set('filterStatus','')"
                                class="ms-1 text-success text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterGender)
                        <span class="badge bg-warning-subtle text-warning">
                            {{ ucfirst($filterGender) }}
                            <a href="javascript:void(0)" wire:click="$set('filterGender','')"
                                class="ms-1 text-warning text-decoration-none">×</a>
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
                <table class="table table-hover mb-0" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>

                            {{-- Sortable: Employee Name --}}
                            <th class="th-sortable">
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('first_name')">
                                    Employee
                                    <span class="text-muted" style="font-size:10px;">
                                        @if ($sortField === 'first_name')
                                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fa fa-sort opacity-30"></i>
                                        @endif
                                    </span>
                                </button>
                            </th>

                            <th>Email</th>
                            <th>Phone</th>
                            <th>Location</th>

                            {{-- Sortable: Department --}}
                            <th class="th-sortable">
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

                            <th>Unit</th>

                            {{-- Sortable: Status --}}
                            <th class="th-sortable">
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('is_active')">
                                    Status
                                    <span class="text-muted" style="font-size:10px;">
                                        @if ($sortField === 'is_active')
                                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fa fa-sort opacity-30"></i>
                                        @endif
                                    </span>
                                </button>
                            </th>
                            <th class="th">
                                Registered by Hr

                            </th>

                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                                                <tr wire:key="employee-{{ $employee->id }}">

                                                    {{-- # --}}
                                                    <td class="text-muted small">
                                                        {{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}
                                                    </td>

                                                    {{-- Employee --}}
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">

                                                            <div>
                                                                <div class="fw-medium" style="font-size:13px;">
                                                                    {{ $employee->user->first_name }}
                                                                    {{ $employee->user?->middle_name }}
                                                                    {{ $employee->user?->last_name }}
                                                                </div>
                                                                <div class="text-muted" style="font-size:11px;">
                                                                    OPF: {{ $employee->opf_number ?? '—' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    {{-- Email --}}
                                                    <td>
                                                        <a href="mailto:{{ $employee->user?->email }}"
                                                            class="text-muted text-decoration-none small">
                                                            <i class="fa fa-envelope fa-xs me-1"></i>
                                                            {{ $employee->user?->email ?? '—' }}
                                                        </a>
                                                    </td>

                                                    {{-- Phone / contacts --}}
                                                    <td>
                                                        @if ($employee->contacts)
                                                            @php
                                                                $contacts = is_array($employee->contacts)
                                                                    ? $employee->contacts
                                                                    : json_decode($employee->contacts, true);
                                                                $personal = collect($contacts)->firstWhere('type', 'personal');
                                                                $nextOfKin = collect($contacts)->firstWhere('type', 'next_of_kin');
                                                            @endphp
                                                            @if (!empty($personal['phone_number']))
                                                                <div class="small">
                                                                    <i class="fa fa-phone fa-xs text-muted me-1"></i>
                                                                    {{ $personal['phone_number'] }}
                                                                </div>
                                                            @endif
                                                            @if (!empty($nextOfKin['phone_number']))
                                                                <div class="text-muted" style="font-size:11px;">
                                                                    <i class="fa fa-user fa-xs me-1"></i>
                                                                    Kin: {{ $nextOfKin['phone_number'] }}
                                                                </div>
                                                            @endif
                                                            @if (empty($personal['phone_number']) && empty($nextOfKin['phone_number']))
                                                                <span class="text-muted small">—</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted small">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Location --}}
                                                    <td>
                                                        <span class="small text-muted">
                                                            <i class="fa fa-location-dot fa-xs me-1"></i>
                                                            {{ $employee->user?->tenant?->name ?? 'N/A' }}
                                                        </span>
                                                    </td>

                                                    {{-- Department --}}
                                                    <td>
                                                        @if ($employee->department)
                                                            <span class="badge bg-info-subtle text-info">
                                                                {{ $employee->department->name }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted small">N/A</span>
                                                        @endif
                                                    </td>

                                                    {{-- Unit --}}
                                                    <td>
                                                        @if ($employee->unit)
                                                            <span class="badge bg-secondary-subtle text-secondary">
                                                                {{ $employee->unit->name }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted small">N/A</span>
                                                        @endif
                                                    </td>

                                                    {{-- Status --}}
                                                    <td>
                                                        <span class="badge {{ $employee->is_active === 'active'
                                ? 'bg-success-subtle text-success'
                                : 'bg-secondary-subtle text-secondary' }}">
                                                            {{ $employee->is_active === 'active' ? 'Active' : 'Inactive' }}
                                                        </span>
                                                        @if ($employee->is_hr_registered)
                                                            <span class="badge bg-primary-subtle text-primary ms-1">
                                                                HR ✓
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td class="">
                                                        @if(auth()->user()->hasRole('director-hr'))
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    wire:click="toggleApproval({{ $employee->id }})"
                                                                    @checked($employee->is_hr_registered) @disabled($employee->is_hr_registered)>
                                                            </div>
                                                        @else
                                                            <span class="badge {{ $employee->is_hr_registered ? 'bg-success' : 'bg-warning' }}">
                                                                {{ $employee->is_hr_registered ? 'Approved' : 'Pending' }}
                                                            </span>
                                                        @endif
                                                    </td>

                                                    {{-- Actions --}}
                                                    <td class="text-end">
                                                        <div class="d-flex gap-1 justify-content-end">
                                                            <a class="btn btn-sm btn-outline-info" href="{{ route('hrm.employee.edit', [
                                'employeeId' => $employee->id,
                                'mode' => 'view',
                            ]) }}" wire:navigate title="View">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('hrm.employee.edit', [
                                'employeeId' => $employee->id,
                                'mode' => 'edit'
                            ]) }}" wire:navigate title="Edit">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                            <button class="btn btn-sm btn-outline-danger"
                                                                wire:click="deleteEmployee({{ $employee->id }})"
                                                                wire:confirm="Remove this employee?" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>

                                                </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fa fa-users fa-2x d-block mb-2 opacity-25"></i>
                                    No employees found
                                    @if ($search || $filterDepartment || $filterUnit || $filterStatus || $filterGender)
                                        for the current filters.
                                        <a href="javascript:void(0)" wire:click="clearFilters" class="d-block mt-1 small">
                                            Clear filters
                                        </a>
                                    @else
                                        .
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
                </small>
            </div>
        @endif
    </div>

</div>

@push('scripts')
    <script src="{{ asset('./assets/js/tooltip-init.js') }}"></script>
    <script src="{{ asset('./assets/js/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('./assets/js/select2/select2-custom.js') }}"></script>
@endpush