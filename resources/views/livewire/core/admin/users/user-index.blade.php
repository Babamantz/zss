@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
    <style>
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
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
            white-space: nowrap;
            padding: 11px 16px;
        }

        tbody td {
            padding: 11px 16px;
            vertical-align: middle;
            font-size: 13px;
        }

        .sort-btn {
            background: none;
            border: none;
            padding: 0;
            color: inherit;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .sort-btn:hover {
            color: #0d6efd;
        }
    </style>
@endpush

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Users</h4>
            <small class="text-muted">Manage system users, roles and permissions</small>
        </div>
        <a class="btn btn-primary btn-sm" href="{{ route('users.create') }}">
            <i class="fa fa-plus me-1"></i> Create User
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
        @php
            $statCards = [
                [
                    'label' => 'Total Users',
                    'value' => $users->total(),
                    'icon' => 'fa-users',
                    'color' => 'primary',
                    'sub' => 'registered users',
                ],
                [
                    'label' => 'Active Roles',
                    'value' => \Spatie\Permission\Models\Role::count(),
                    'icon' => 'fa-shield-halved',
                    'color' => 'info',
                    'sub' => 'configured roles',
                ],
                [
                    'label' => 'Permissions',
                    'value' => \Spatie\Permission\Models\Permission::count(),
                    'icon' => 'fa-key',
                    'color' => 'warning',
                    'sub' => 'system permissions',
                ],
                [
                    'label' => 'Locations',
                    'value' => \App\Models\Tenant::count(),
                    'icon' => 'fa-location-dot',
                    'color' => 'success',
                    'sub' => 'tenants',
                ],
            ];
        @endphp

        @foreach ($statCards as $card)
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-{{ $card['color'] }}-subtle
                                    p-3 flex-shrink-0">
                            <i class="fa {{ $card['icon'] }} text-{{ $card['color'] }} fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">{{ $card['label'] }}</p>
                            <h5 class="mb-0">{{ $card['value'] }}</h5>
                            <small class="text-muted">{{ $card['sub'] }}</small>
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
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fa fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" class="form-control form-control-sm border-start-0 ps-0"
                            placeholder="Search name or email..." wire:model.live.debounce.400ms="search">
                    </div>
                </div>

                {{-- Role filter --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filterRole">
                        <option value="">All Roles</option>
                        @foreach (\Spatie\Permission\Models\Role::orderBy('name')->get() as $role)
                            <option value="{{ $role->name }}">
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Location filter --}}
                <div class="col-md-3">
                    <select class="form-select form-select-sm" wire:model.live="filterTenant">
                        <option value="">All Locations</option>
                        @foreach (\App\Models\Tenant::orderBy('name')->get() as $tenant)
                            <option value="{{ $tenant->id }}">
                                {{ $tenant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filterStatus">
                        <option value="">All Status</option>
                        <option value="1">
                            active
                        </option>
                        <option value="0">
                            in-active
                        </option>
                    </select>
                </div>

                {{-- Per page --}}
                <div class="col-md-1">
                    <select class="form-select form-select-sm" wire:model.live="perPage">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">150</option>
                    </select>
                </div>

                {{-- Clear --}}
                <div class="col-md-1 text-end">
                    @if ($search || $filterRole || $filterTenant)
                        <button class="btn btn-sm btn-outline-secondary" wire:click="clearFilters" title="Clear filters">
                            <i class="fa fa-times"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Active filter chips --}}
            @if ($search || $filterRole || $filterTenant)
                <div class="d-flex flex-wrap gap-1 mt-2">
                    <small class="text-muted me-1 align-self-center">Filters:</small>
                    @if ($search)
                        <span class="badge bg-primary-subtle text-primary">
                            "{{ $search }}"
                            <a href="javascript:void(0)" wire:click="$set('search', '')"
                                class="ms-1 text-primary text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterRole)
                        <span class="badge bg-info-subtle text-info">
                            {{ ucfirst($filterRole) }}
                            <a href="javascript:void(0)" wire:click="$set('filterRole', '')"
                                class="ms-1 text-info text-decoration-none">×</a>
                        </span>
                    @endif
                    @if ($filterTenant)
                        <span class="badge bg-success-subtle text-success">
                            {{ \App\Models\Tenant::find($filterTenant)?->name }}
                            <a href="javascript:void(0)" wire:click="$set('filterTenant', '')"
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
                            <th style="padding:11px 16px;">#</th>

                            {{-- Sortable: Name --}}
                            <th style="padding:11px 16px;">
                                <button class="sort-btn d-flex align-items-center gap-1"
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
                            <th style="padding:11px 16px;">
                                <button class="sort-btn d-flex align-items-center gap-1" wire:click="sortBy('email')">
                                    Email
                                    <i class="fa fa-sort{{
    $sortField === 'email'
    ? ($sortDir === 'asc' ? '-up' : '-down')
    : ''
                                    }} opacity-{{ $sortField === 'email' ? '100' : '30' }}" style="font-size:9px;"></i>
                                </button>
                            </th>

                            <th style="padding:11px 16px;">Location</th>
                            <th style="padding:11px 16px;">Role</th>
                            <th style="padding:11px 16px;">Permissions</th>
                            <th style="padding:11px 16px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr wire:key="user-{{ $user->id }}">

                                {{-- # --}}
                                <td class="text-muted small">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                </td>

                                {{-- User (Avatar + Name) --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle">
                                            {{ strtoupper(substr($user->first_name ?? '?', 0, 1)) }}
                                            {{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium small">
                                                {{ $user->first_name }}
                                                {{ $user->middle_name }}
                                                {{ $user->last_name }}
                                            </div>
                                            @if ($user->employee)
                                                <div class="text-muted" style="font-size:10px;">
                                                    OPF: {{ $user->employee->opf_number ?? '—' }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td>
                                    <a href="mailto:{{ $user->email }}" class="text-muted text-decoration-none small">
                                        <i class="fa fa-envelope fa-xs me-1"></i>
                                        {{ $user->email }}
                                    </a>
                                </td>

                                {{-- Location --}}
                                <td>
                                    @if ($user->tenant)
                                        <span class="badge bg-success-subtle text-success" style="font-size:10px;">
                                            <i class="fa fa-location-dot fa-xs me-1"></i>
                                            {{ $user->tenant->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>

                                {{-- Role --}}
                                <td>
                                    @forelse ($user->getRoleNames() as $role)
                                        <span class="badge bg-info-subtle text-info mb-1" style="font-size:10px;">
                                            <i class="fa fa-shield-halved fa-xs me-1"></i>
                                            {{ ucfirst($role) }}
                                        </span>
                                    @empty
                                        <span class="text-muted small">No role</span>
                                    @endforelse
                                </td>

                                {{-- Permissions --}}
                                <td>
                                    @php
                                        $perms = $user->getAllPermissions()->pluck('name');
                                    @endphp
                                    @if ($perms->count())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($perms->take(2) as $perm)
                                                <span class="badge bg-warning-subtle text-warning" style="font-size:9px;">
                                                    {{ $perm }}
                                                </span>
                                            @endforeach
                                            @if ($perms->count() > 2)
                                                <span class="badge bg-secondary-subtle
                                                                            text-secondary" style="font-size:9px;"
                                                    title="{{ $perms->skip(2)->implode(', ') }}">
                                                    +{{ $perms->count() - 2 }} more
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a class="btn btn-sm btn-outline-info"
                                            href="{{ route('users.edit', ['id' => $user->id, 'mode' => 'view']) }}"
                                            wire:navigate title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-sm btn-outline-secondary"
                                            href="{{ route('users.edit', ['id' => $user->id, 'mode' => 'edit']) }}"
                                            wire:navigate title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fa fa-users fa-2x d-block mb-2 opacity-25"></i>
                                    No users found.
                                    @if ($search || $filterRole || $filterTenant)
                                        <a href="javascript:void(0)" wire:click="clearFilters"
                                            class="d-block mt-1 small text-primary">
                                            Clear filters
                                        </a>
                                    @else
                                        <a href="{{ route('users.create') }}" class="d-block mt-1 small text-primary">
                                            Create the first user
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