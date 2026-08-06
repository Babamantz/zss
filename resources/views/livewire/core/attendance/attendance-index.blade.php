@push('css')
    <style>
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

        .form-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #E3F2FD;
            color: #0D47A1;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>
@endpush

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">My Attendance Forms</h4>
            <small class="text-muted">
                Manage attendance capture forms and their submitted rows
            </small>
        </div>
        <a class="btn btn-primary btn-sm" href="{{ route('attendance.create') }}" wire:navigate>
            <i class="fa fa-plus me-1"></i> Create New Form
        </a>
    </div>

    {{-- ── Flash ───────────────────────────────────────────────────────────── --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Stat Cards ───────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @foreach ([
                [
                    'label' => 'Total Forms',
                    'value' => $stats['total_forms'],
                    'icon' => 'fa-clipboard-list',
                    'color' => 'primary',
                    'sub' => 'attendance forms',
                ],
                [
                    'label' => 'Total Rows Captured',
                    'value' => $stats['total_rows'],
                    'icon' => 'fa-table-list',
                    'color' => 'warning',
                    'sub' => 'across all forms',
                ],
                [
                    'label' => 'Created This Month',
                    'value' => $stats['forms_this_month'],
                    'icon' => 'fa-calendar-check',
                    'color' => 'success',
                    'sub' => 'new forms',
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
                        <input type="text" class="form-control form-control-sm border-start-0 ps-0"
                            placeholder="Search forms by title..." wire:model.live.debounce.400ms="search">
                    </div>
                </div>

                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="perPage">
                        <option value="15">15 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>

                <div class="col-md-5 text-end">
                    @if ($search)
                        <button class="btn btn-sm btn-outline-secondary" wire:click="clearFilters">
                            <i class="fa fa-times me-1"></i> Clear
                        </button>
                    @endif
                    <small class="text-muted ms-2">
                        {{ $attendances->total() }} form(s) found
                    </small>
                </div>
            </div>

            {{-- Active filter chip --}}
            @if ($search)
                <div class="d-flex gap-1 mt-2">
                    <small class="text-muted me-1 align-self-center">Filters:</small>
                    <span class="badge bg-primary-subtle text-primary">
                        "{{ $search }}"
                        <a href="javascript:void(0)" wire:click="$set('search', '')"
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

                            {{-- Sortable: Title --}}
                            <th style="padding:11px 16px;">
                                <button class="sort-btn d-flex align-items-center gap-1" wire:click="sortBy('title')">
                                    Title
                                    <i class="fa fa-sort{{
    $sortField === 'title'
    ? ($sortDir === 'asc' ? '-up' : '-down')
    : ''
                                    }} opacity-{{ $sortField === 'title' ? '100' : '30' }}" style="font-size:9px;"></i>
                                </button>
                            </th>

                            {{-- Sortable: Rows --}}
                            <th style="padding:11px 16px;">
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('number_of_rows')">
                                    Rows
                                    <i class="fa fa-sort{{
    $sortField === 'number_of_rows'
    ? ($sortDir === 'asc' ? '-up' : '-down')
    : ''
                                    }} opacity-{{ $sortField === 'number_of_rows' ? '100' : '30' }}"
                                        style="font-size:9px;"></i>
                                </button>
                            </th>

                            {{-- Sortable: Created --}}
                            <th style="padding:11px 16px;">
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('created_at')">
                                    Created
                                    <i class="fa fa-sort{{
    $sortField === 'created_at'
    ? ($sortDir === 'asc' ? '-up' : '-down')
    : ''
                                    }} opacity-{{ $sortField === 'created_at' ? '100' : '30' }}"
                                        style="font-size:9px;"></i>
                                </button>
                            </th>

                            <th style="padding:11px 16px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $attendance)
                            <tr wire:key="attendance-{{ $attendance->id }}">

                                {{-- # --}}
                                <td class="text-muted small">
                                    {{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}
                                </td>

                                {{-- Title --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="form-avatar">
                                            {{ strtoupper(substr($attendance->title, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium small">
                                                {{ $attendance->title }}
                                            </div>
                                            <div class="text-muted" style="font-size:10px;">
                                                Form #{{ $attendance->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Rows --}}
                                <td>
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="fa fa-table-list fa-xs me-1"></i>
                                        {{ $attendance->number_of_rows }}
                                        {{ Str::plural('row', $attendance->number_of_rows) }}
                                    </span>
                                </td>

                                {{-- Created --}}
                                <td>
                                    <div class="small">{{ $attendance->created_at->format('M d, Y') }}</div>
                                    <div class="text-muted" style="font-size:10px;">
                                        {{ $attendance->created_at->diffForHumans() }}
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a class="btn btn-sm btn-outline-info"
                                            href="{{ route('attendance.edit', ['attendanceId' => $attendance->id, 'mode' => 'view']) }}"
                                            title="View" wire:navigate>
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-sm btn-outline-secondary"
                                            href="{{ route('attendance.edit', ['attendanceId' => $attendance->id]) }}"
                                            title="Edit" wire:navigate>
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a class="btn btn-sm btn-outline-success"
                                            href="{{ route('attendance.preview', $attendance->id) }}" title="Preview"
                                            target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete"
                                            wire:click="confirmDelete({{ $attendance->id }})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="fa fa-clipboard-list fa-2x d-block mb-2 opacity-25"></i>
                                    No attendance forms found.
                                    @if ($search)
                                        <a href="javascript:void(0)" wire:click="clearFilters"
                                            class="d-block mt-1 small text-primary">
                                            Clear filters
                                        </a>
                                    @else
                                        <a href="{{ route('attendance.create') }}" class="d-block mt-1 small text-primary"
                                            wire:navigate>
                                            Create your first form
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
        @if ($attendances->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2">
                <small class="text-muted">
                    Showing {{ $attendances->firstItem() }}–{{ $attendances->lastItem() }}
                    of {{ $attendances->total() }} forms
                </small>
                {{ $attendances->links() }}
            </div>
        @else
            <div class="card-footer py-2">
                <small class="text-muted">
                    {{ $attendances->total() }} form(s)
                </small>
            </div>
        @endif
    </div>

    {{-- ── Delete Confirmation Modal ───────────────────────────────────────── --}}
    @if ($confirmingDeletion)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close" wire:click="$set('confirmingDeletion', false)"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this attendance form? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('confirmingDeletion', false)">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteAttendance">
                            <i class="fa fa-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script src="{{ asset('./assets/js/tooltip-init.js') }}"></script>
@endpush