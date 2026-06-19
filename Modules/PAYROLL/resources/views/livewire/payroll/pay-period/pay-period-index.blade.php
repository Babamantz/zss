{{-- resources/views/hrm/livewire/payroll/pay-periods/index.blade.php --}}

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Pay Periods</h4>
            <small class="text-muted">Define and manage monthly payroll cycles</small>
        </div>
        <button class="btn btn-primary btn-sm" wire:click="openCreate">
            <i class="fa fa-plus me-1"></i> New Period
        </button>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Status Filter --}}
    <div class="mb-3 d-flex gap-2">
        @foreach (['' => 'All', 'Draft' => 'Draft', 'Processing' => 'Processing', 'Locked_Completed' => 'Locked'] as $val => $label)
            <button wire:click="$set('filterStatus', '{{ $val }}')"
                class="btn btn-sm {{ $filterStatus === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Cards Grid --}}
    <div class="row g-3">
        @forelse ($periods as $period)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">
                                {{ $period->start_date->format('M d') }}
                                &ndash;
                                {{ $period->end_date->format('M d, Y') }}
                            </h6>
                            @php
                                $badgeClass = match ($period->status) {
                                    'Draft' => 'bg-secondary-subtle text-secondary',
                                    'Processing' => 'bg-warning-subtle text-warning',
                                    'Locked_Completed' => 'bg-success-subtle text-success',
                                    default => 'bg-light text-muted',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ str_replace('_', ' ', $period->status) }}
                            </span>
                        </div>

                        <p class="text-muted small mb-3">
                            <i class="fa fa-users me-1"></i>
                            {{ $period->entries_count ?? $period->entries()->count() }} employees processed
                        </p>

                        <div class="d-flex gap-2">
                            @unless ($period->isLocked())
                                <button class="btn btn-sm btn-outline-secondary" wire:click="openEdit({{ $period->id }})">
                                    <i class="fa fa-pencil me-1"></i> Edit
                                </button>
                            @endunless
                            <a href="{{ route('payroll.run', $period->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-play me-1"></i>
                                {{ $period->isLocked() ? 'View' : 'Run Payroll' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center text-muted py-5">
                    <i class="fa fa-calendar fa-2x mb-2 d-block"></i>
                    No pay periods found. Create one to get started.
                </div>
            </div>
        @endforelse
    </div>

    @if ($periods->hasPages())
        <div class="mt-4">{{ $periods->links() }}</div>
    @endif

    {{-- Modal --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.45);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editId ? 'Edit Pay Period' : 'New Pay Period' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model.defer="start_date">
                                @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model.defer="end_date">
                                @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Status</label>
                                <select class="form-select" wire:model="status">
                                    <option value="Draft">Draft</option>
                                    <option value="Processing">Processing</option>
                                    <option value="Locked_Completed">Locked / Completed</option>
                                </select>
                                @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" wire:click="$set('showModal', false)">Cancel</button>
                        <button class="btn btn-primary btn-sm" wire:click="save" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">Save</span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm"></span> Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>