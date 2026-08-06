@push('css')
    <style>
        thead th {
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: .05em;
            color: #6c757d; white-space: nowrap;
            padding: 11px 16px;
        }
        tbody td { padding: 11px 16px; vertical-align: middle; font-size: 13px; }
        .sort-btn {
            background: none; border: none; padding: 0;
            color: inherit; cursor: pointer; font-size: 11px;
            font-weight: 600; text-transform: uppercase;
            letter-spacing: .05em;
        }
        .sort-btn:hover { color: #0d6efd; }
        .role-avatar {
            width: 36px; height: 36px; border-radius: 8px;
            background: #EDE7F6; color: #4527A0;
            font-size: 13px; font-weight: 700;
            display: flex; align-items: center;
            justify-content: center; flex-shrink: 0;
        }
    </style>
@endpush

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Roles</h4>
            <small class="text-muted">
                Manage system roles and their permission assignments
            </small>
        </div>
        <a class="btn btn-primary btn-sm" href="{{ route('roles.create') }}">
            <i class="fa fa-plus me-1"></i> Create Role
        </a>
    </div>

    {{-- ── Flash ───────────────────────────────────────────────────────────── --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Toast (existing session logic preserved) ────────────────────────── --}}
    @if (session()->has('toastMagic'))
        <script>
            document.addEventListener('livewire:navigated', function () {
                Livewire.dispatch('toastMagic', @js(session('toastMagic')));
            });
        </script>
    @endif

    {{-- ── Stat Cards ───────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @foreach ([
            [
                'label' => 'Total Roles',
                'value' => $stats['total_roles'],
                'icon'  => 'fa-shield-halved',
                'color' => 'primary',
                'sub'   => 'configured roles',
            ],
            [
                'label' => 'Total Permissions',
                'value' => $stats['total_permissions'],
                'icon'  => 'fa-key',
                'color' => 'warning',
                'sub'   => 'system permissions',
            ],
            [
                'label' => 'Users with Roles',
                'value' => $stats['total_users_with_roles'],
                'icon'  => 'fa-users',
                'color' => 'success',
                'sub'   => 'role-assigned users',
            ],
        ] as $card)
            <div class="col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-{{ $card['color'] }}-subtle
                            p-3 flex-shrink-0">
                            <i class="fa {{ $card['icon'] }}
                                text-{{ $card['color'] }} fa-lg"></i>
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

                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fa fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text"
                            class="form-control form-control-sm border-start-0 ps-0"
                            placeholder="Search role name..."
                            wire:model.live.debounce.400ms="search">
                    </div>
                </div>

                <div class="col-md-2">
                    <select class="form-select form-select-sm"
                        wire:model.live="perPage">
                        <option value="15">15 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>

                <div class="col-md-5 text-end">
                    @if ($search)
                        <button class="btn btn-sm btn-outline-secondary"
                            wire:click="clearFilters">
                            <i class="fa fa-times me-1"></i> Clear
                        </button>
                    @endif
                    <small class="text-muted ms-2">
                        {{ $roles->total() }} role(s) found
                    </small>
                </div>
            </div>

            {{-- Active filter chip --}}
            @if ($search)
                <div class="d-flex gap-1 mt-2">
                    <small class="text-muted me-1 align-self-center">Filters:</small>
                    <span class="badge bg-primary-subtle text-primary">
                        "{{ $search }}"
                        <a href="javascript:void(0)"
                            wire:click="$set('search', '')"
                            class="ms-1 text-primary text-decoration-none">×</a>
                    </span>
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

                            {{-- Sortable: Role --}}
                            <th style="padding:11px 16px;">
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('name')">
                                    Role
                                    <i class="fa fa-sort{{
                                        $sortField === 'name'
                                            ? ($sortDir === 'asc' ? '-up' : '-down')
                                            : ''
                                    }} opacity-{{ $sortField === 'name' ? '100' : '30' }}"
                                        style="font-size:9px;"></i>
                                </button>
                            </th>

                            <th style="padding:11px 16px;">Guard</th>
                            <th style="padding:11px 16px;">Permissions</th>

                            {{-- Sortable: Users --}}
                            <th style="padding:11px 16px;">
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('users_count')">
                                    Users
                                    <i class="fa fa-sort{{
                                        $sortField === 'users_count'
                                            ? ($sortDir === 'asc' ? '-up' : '-down')
                                            : ''
                                    }} opacity-{{ $sortField === 'users_count' ? '100' : '30' }}"
                                        style="font-size:9px;"></i>
                                </button>
                            </th>

                            <th style="padding:11px 16px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr wire:key="role-{{ $role->id }}">

                                {{-- # --}}
                                <td class="text-muted small">
                                    {{ ($roles->currentPage() - 1) * $roles->perPage() + $loop->iteration }}
                                </td>

                                {{-- Role --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="role-avatar">
                                            {{ strtoupper(substr($role->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium small">
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </div>
                                            <div class="text-muted" style="font-size:10px;">
                                                {{ $role->permissions_count }}
                                                {{ Str::plural('permission', $role->permissions_count) }}
                                                assigned
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Guard --}}
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary"
                                        style="font-size:10px;">
                                        {{ $role->guard_name }}
                                    </span>
                                </td>

                                {{-- Permissions --}}
                                <td>
                                    @php
                                        $perms = $role->getAllPermissions()->pluck('name');
                                    @endphp
                                    @if ($perms->count())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($perms->take(3) as $perm)
                                                <span class="badge bg-warning-subtle text-warning"
                                                    style="font-size:9px;">
                                                    {{ $perm }}
                                                </span>
                                            @endforeach
                                            @if ($perms->count() > 3)
                                                <span class="badge bg-secondary-subtle text-secondary"
                                                    style="font-size:9px;"
                                                    title="{{ $perms->skip(3)->implode(', ') }}">
                                                    +{{ $perms->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">No permissions</span>
                                    @endif
                                </td>

                                {{-- Users count --}}
                                <td>
                                    @if ($role->users_count > 0)
                                        <span class="badge bg-primary-subtle text-primary">
                                            <i class="fa fa-users fa-xs me-1"></i>
                                            {{ $role->users_count }}
                                            {{ Str::plural('user', $role->users_count) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">No users</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a class="btn btn-sm btn-outline-secondary"
                                            href="{{ route('roles.edit', ['roleName' => $role->name]) }}"
                                            title="Edit Role">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="text-center text-muted py-5">
                                    <i class="fa fa-shield-halved fa-2x d-block
                                        mb-2 opacity-25"></i>
                                    No roles found.
                                    @if ($search)
                                        <a href="javascript:void(0)"
                                            wire:click="clearFilters"
                                            class="d-block mt-1 small text-primary">
                                            Clear filters
                                        </a>
                                    @else
                                        <a href="{{ route('roles.create') }}"
                                            class="d-block mt-1 small text-primary">
                                            Create the first role
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
        @if ($roles->hasPages())
            <div class="card-footer d-flex justify-content-between
                align-items-center py-2">
                <small class="text-muted">
                    Showing {{ $roles->firstItem() }}–{{ $roles->lastItem() }}
                    of {{ $roles->total() }} roles
                </small>
                {{ $roles->links() }}
            </div>
        @else
            <div class="card-footer py-2">
                <small class="text-muted">
                    {{ $roles->total() }} role(s)
                </small>
            </div>
        @endif
    </div>

</div>

@push('scripts')
    <script src="{{ asset('./assets/js/tooltip-init.js') }}"></script>
@endpush