{{-- Modules/PAYROLL/resources/views/livewire/payroll/setups/salary-component-index.blade.php --}}

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Salary Components</h4>
            <small class="text-muted">Manage earnings and deductions applied to payroll</small>
        </div>
        <button class="btn btn-primary btn-sm" wire:click="openCreate">
            <i class="fa fa-plus me-1"></i> New Component
        </button>
    </div>

    {{-- Flash Notifications --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters Bar --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <!-- Note: For Livewire v3 use wire:model.live.debounce.400ms -->
                    <input type="text" class="form-control form-control-sm" placeholder="Search components..."
                        wire:model.live.debounce.400ms="search">
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" wire:model.live="filterType">
                        <option value="">All Types</option>
                        <option value="Earning">Earnings</option>
                        <option value="Deduction">Deductions</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" wire:model.live="filterGlobal" id="globalOnly">
                        <label class="form-check-label small" for="globalOnly">
                            Global only
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Global</th>
                            <th>Created by</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($components as $comp)
                            <tr>
                                <td class="text-muted small">{{ $loop->iteration }}</td>
                                <td>{{ $comp->name }}</td>
                                <td>
                                    @if ($comp->type === 'Earning')
                                        <span class="badge bg-success-subtle text-success">Earning</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Deduction</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($comp->is_global)
                                        <span class="badge bg-primary-subtle text-primary">Global</span>
                                    @else
                                        <span class="text-muted small">Per employee</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $comp->creator?->name ?? '—' }}
                                </td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-outline-secondary me-1"
                                        wire:click="openEdit({{ $comp->id }})">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $comp->id }})"
                                        wire:confirm="Remove this component?">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No salary components found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($components->hasPages())
            <div class="card-footer d-flex justify-content-end">
                {{ $components->links() }}
            </div>
        @endif
    </div>

    {{-- Create / Edit Modal Wrapper --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editId ? 'Edit Component' : 'New Salary Component' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="resetModal" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <!-- Name Field -->
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="name"
                                    placeholder="e.g. Housing Allowance">
                                @error('name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <!-- Classification Radio Options -->
                            <div class="mb-3">
                                <label class="form-label d-block font-weight-bold">Type <span
                                        class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" wire:model="type" value="Earning"
                                        id="type_earning">
                                    <label class="form-check-label" for="type_earning">Earning</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" wire:model="type" value="Deduction"
                                        id="type_deduction">
                                    <label class="form-check-label" for="type_deduction">Deduction</label>
                                </div>
                                @error('type') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <!-- Scope Flag Checkbox -->
                            <div class="mb-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="is_global" id="is_global">
                                    <label class="form-check-label font-weight-bold" for="is_global">
                                        Apply globally to all employees
                                    </label>
                                </div>
                                <small class="text-muted d-block ms-4">
                                    Global components auto-apply to every employee during payroll run.
                                </small>
                                @error('is_global') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Actions Row -->
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary btn-sm" wire:click="resetModal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa fa-save me-1"></i> Save Component
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>