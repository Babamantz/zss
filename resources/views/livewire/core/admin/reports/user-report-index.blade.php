{{-- resources/views/livewire/core/admin/reports/user-report-index.blade.php --}}

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">User Report</h4>
            <small class="text-muted">
                Filter, review and export user data
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
            ['Total Users',    $counts['total'],      'fa-users',         'primary'],
            ['Active',         $counts['active'],     'fa-circle-check',  'success'],
            ['Inactive',       $counts['inactive'],   'fa-circle-xmark',  'secondary'],
            ['With Roles',     $counts['with_roles'], 'fa-shield-halved', 'info'],
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
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fa fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text"
                            class="form-control form-control-sm border-start-0 ps-0"
                            placeholder="Search name or email..."
                            wire:model.live.debounce.400ms="search">
                    </div>
                </div>

                {{-- Role --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm"
                        wire:model.live="filterRole">
                        <option value="">All Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tenant --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm"
                        wire:model.live="filterTenant">
                        <option value="">All Locations</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm"
                        wire:model.live="filterStatus">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                {{-- Per page --}}
                <div class="col-md-1">
                    <select class="form-select form-select-sm"
                        wire:model.live="perPage">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                {{-- Clear --}}
                <div class="col-md-2 text-end">
                    @if ($search || $filterRole || $filterTenant)
                        <button class="btn btn-sm btn-outline-secondary"
                            wire:click="clearFilters">
                            <i class="fa fa-times me-1"></i> Clear
                        </button>
                    @endif
                </div>
            </div>

            {{-- Active filter chips --}}
            @if ($search || $filterRole || $filterTenant)
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
                    @if ($filterRole)
                        <span class="badge bg-info-subtle text-info">
                            {{ ucfirst(str_replace('_', ' ', $filterRole)) }}
                            <a href="javascript:void(0)"
                                wire:click="$set('filterRole', '')"
                                class="ms-1 text-info text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterTenant)
                        <span class="badge bg-success-subtle text-success">
                            {{ $tenants->find($filterTenant)?->name }}
                            <a href="javascript:void(0)"
                                wire:click="$set('filterTenant', '')"
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
                            <th style="font-size:11px;padding:11px 16px;">#</th>

                            {{-- Sortable: Name --}}
                            <th style="font-size:11px;padding:11px 16px;">
                                <button class="btn p-0 border-0 fw-semibold text-muted
                                    d-flex align-items-center gap-1"
                                    style="font-size:11px;text-transform:uppercase;
                                           letter-spacing:.05em;"
                                    wire:click="sortBy('first_name')">
                                    User
                                    <i class="fa fa-sort{{
                                        $sortField === 'first_name'
                                            ? ($sortDir === 'asc' ? '-up' : '-down')
                                            : ''
                                    }} opacity-{{ $sortField === 'first_name' ? '100' : '30' }}"
                                        style="font-size:9px;"></i>
                                </button>
                            </th>

                            {{-- Sortable: Email --}}
                            <th style="font-size:11px;padding:11px 16px;">
                                <button class="btn p-0 border-0 fw-semibold text-muted
                                    d-flex align-items-center gap-1"
                                    style="font-size:11px;text-transform:uppercase;
                                           letter-spacing:.05em;"
                                    wire:click="sortBy('email')">
                                    Email
                                    <i class="fa fa-sort{{
                                        $sortField === 'email'
                                            ? ($sortDir === 'asc' ? '-up' : '-down')
                                            : ''
                                    }} opacity-{{ $sortField === 'email' ? '100' : '30' }}"
                                        style="font-size:9px;"></i>
                                </button>
                            </th>

                            <th style="font-size:11px;padding:11px 16px;
                                text-transform:uppercase;letter-spacing:.05em;
                                color:#6c757d;">
                                Location
                            </th>
                            <th style="font-size:11px;padding:11px 16px;
                                text-transform:uppercase;letter-spacing:.05em;
                                color:#6c757d;">
                                Role
                            </th>
                            <th style="font-size:11px;padding:11px 16px;
                                text-transform:uppercase;letter-spacing:.05em;
                                color:#6c757d;">
                                Permissions
                            </th>
                            <th style="font-size:11px;padding:11px 16px;
                                text-transform:uppercase;letter-spacing:.05em;
                                color:#6c757d;">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr wire:key="ur-{{ $user->id }}">

                                {{-- # --}}
                                <td style="padding:11px 16px;"
                                    class="text-muted small">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                </td>

                                {{-- User --}}
                                <td style="padding:11px 16px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary-subtle
                                            d-flex align-items-center justify-content-center
                                            flex-shrink-0"
                                            style="width:34px;height:34px;font-size:11px;
                                                   font-weight:600;color:#1a237e;">
                                            {{ strtoupper(substr($user->first_name ?? '?', 0, 1)) }}
                                            {{ strtoupper(substr($user->last_name  ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium small">
                                                {{ $user->first_name }}
                                                {{ $user->middle_name }}
                                                {{ $user->last_name }}
                                            </div>
                                            @if ($user->employee)
                                                <div class="text-muted"
                                                    style="font-size:10px;">
                                                    OPF:
                                                    {{ $user->employee->opf_number ?? '—' }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td style="padding:11px 16px;">
                                    <a href="mailto:{{ $user->email }}"
                                        class="text-muted text-decoration-none small">
                                        <i class="fa fa-envelope fa-xs me-1"></i>
                                        {{ $user->email }}
                                    </a>
                                </td>

                                {{-- Location --}}
                                <td style="padding:11px 16px;">
                                    @if ($user->tenant)
                                        <span class="badge bg-success-subtle text-success"
                                            style="font-size:10px;">
                                            <i class="fa fa-location-dot fa-xs me-1"></i>
                                            {{ $user->tenant->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>

                                {{-- Role --}}
                                <td style="padding:11px 16px;">
                                    @forelse ($user->getRoleNames() as $role)
                                        <span class="badge bg-info-subtle text-info mb-1"
                                            style="font-size:10px;">
                                            <i class="fa fa-shield-halved fa-xs me-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                                        </span>
                                    @empty
                                        <span class="text-muted small">No role</span>
                                    @endforelse
                                </td>

                                {{-- Permissions --}}
                                <td style="padding:11px 16px;">
                                    @php
                                        $perms = $user->getAllPermissions()->pluck('name');
                                    @endphp
                                    @if ($perms->count())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($perms->take(2) as $perm)
                                                <span class="badge bg-warning-subtle
                                                    text-warning"
                                                    style="font-size:9px;">
                                                    {{ $perm }}
                                                </span>
                                            @endforeach
                                            @if ($perms->count() > 2)
                                                <span class="badge bg-secondary-subtle
                                                    text-secondary"
                                                    style="font-size:9px;"
                                                    title="{{ $perms->skip(2)->implode(', ') }}">
                                                    +{{ $perms->count() - 2 }} more
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td style="padding:11px 16px;">
                                    <span class="badge {{
                                        $user->is_active
                                            ? 'bg-success-subtle text-success'
                                            : 'bg-secondary-subtle text-secondary'
                                    }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    class="text-center text-muted py-5">
                                    <i class="fa fa-users fa-2x d-block mb-2 opacity-25"></i>
                                    No users found.
                                    @if ($search || $filterRole || $filterTenant)
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
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="card-footer d-flex justify-content-between
                align-items-center py-2">
                <small class="text-muted">
                    Showing {{ $users->firstItem() }}–{{ $users->lastItem() }}
                    of {{ $users->total() }} users
                    &bull;
                    Status:
                    <strong class="{{ $filterStatus === '1' ? 'text-success' : 'text-secondary' }}">
                        {{ $filterStatus === '1' ? 'Active' : 'Inactive' }}
                    </strong>
                </small>
                {{ $users->links() }}
            </div>
        @else
            <div class="card-footer py-2">
                <small class="text-muted">
                    {{ $users->total() }} user(s) found
                </small>
            </div>
        @endif
    </div>

</div>

@push('scripts')
    <script src="{{ asset('./assets/js/tooltip-init.js') }}"></script>
@endpush