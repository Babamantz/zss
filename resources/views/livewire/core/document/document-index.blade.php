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

        .doc-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #FCE4EC;
            color: #AD1457;
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
            <h4 class="mb-1">Documents</h4>
            <small class="text-muted">
                Manage uploaded documents and files
            </small>
        </div>
        <a class="btn btn-primary btn-sm" href="{{ route('documents.create') }}" wire:navigate>
            <i class="fa fa-plus me-1"></i> Add Document
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
                    'label' => 'Total Documents',
                    'value' => $stats['total_documents'],
                    'icon' => 'fa-file-text',
                    'color' => 'primary',
                    'sub' => 'documents on file',
                ],
                [
                    'label' => 'Added This Month',
                    'value' => $stats['documents_this_month'],
                    'icon' => 'fa-calendar-plus-o',
                    'color' => 'success',
                    'sub' => 'new uploads',
                ],
                [
                    'label' => 'Added Today',
                    'value' => $stats['documents_today'],
                    'icon' => 'fa-clock-o',
                    'color' => 'warning',
                    'sub' => 'uploaded today',
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
                            placeholder="Search documents by name..." wire:model.live.debounce.400ms="search">
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
                        {{ $documents->total() }} document(s) found
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

                            {{-- Sortable: Name --}}
                            <th style="padding:11px 16px;">
                                <button class="sort-btn d-flex align-items-center gap-1" wire:click="sortBy('name')">
                                    Name
                                    <i class="fa fa-sort{{
    $sortField === 'name'
    ? ($sortDir === 'asc' ? '-up' : '-down')
    : ''
                                    }} opacity-{{ $sortField === 'name' ? '100' : '30' }}" style="font-size:9px;"></i>
                                </button>
                            </th>

                            {{-- Sortable: Uploaded Date --}}
                            <th style="padding:11px 16px;">
                                <button class="sort-btn d-flex align-items-center gap-1"
                                    wire:click="sortBy('created_at')">
                                    Uploaded Date
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
                        @forelse ($documents as $document)
                            <tr wire:key="document-{{ $document->id }}">

                                {{-- # --}}
                                <td class="text-muted small">
                                    {{ ($documents->currentPage() - 1) * $documents->perPage() + $loop->iteration }}
                                </td>

                                {{-- Name --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="doc-avatar">
                                            <i class="fa fa-file-text"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium small">
                                                {{ $document->name }}
                                            </div>
                                            <div class="text-muted" style="font-size:10px;">
                                                Document #{{ $document->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Uploaded Date --}}
                                <td>
                                    <div class="small">{{ $document->created_at->format('M d, Y') }}</div>
                                    <div class="text-muted" style="font-size:10px;">
                                        {{ $document->created_at->diffForHumans() }}
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a class="btn btn-sm btn-outline-info"
                                            href="{{ route('documents.edit', ['documentId' => $document->id, 'mode' => 'view']) }}"
                                            title="View" wire:navigate>
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-sm btn-outline-secondary"
                                            href="{{ route('documents.edit', ['documentId' => $document->id]) }}"
                                            title="Edit" wire:navigate>
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a class="btn btn-sm btn-outline-success"
                                            href="{{ Storage::url($document->document_path) }}" title="Download"
                                            target="_blank">
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete"
                                            wire:click="confirmDelete({{ $document->id }})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="fa fa-file-text fa-2x d-block mb-2 opacity-25"></i>
                                    No documents found.
                                    @if ($search)
                                        <a href="javascript:void(0)" wire:click="clearFilters"
                                            class="d-block mt-1 small text-primary">
                                            Clear filters
                                        </a>
                                    @else
                                        <a href="{{ route('documents.create') }}" class="d-block mt-1 small text-primary"
                                            wire:navigate>
                                            Add your first document
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
        @if ($documents->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2">
                <small class="text-muted">
                    Showing {{ $documents->firstItem() }}–{{ $documents->lastItem() }}
                    of {{ $documents->total() }} documents
                </small>
                {{ $documents->links() }}
            </div>
        @else
            <div class="card-footer py-2">
                <small class="text-muted">
                    {{ $documents->total() }} document(s)
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
                        Are you sure you want to delete this document? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('confirmingDeletion', false)">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteDocument">
                            <i class="fa fa-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>