{{-- resources/views/hrm/livewire/payroll/employee-components/index.blade.php --}}

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Employee Components</h4>
            <small class="text-muted">Per-employee salary component overrides</small>
        </div>
        <button class="btn btn-primary btn-sm" wire:click="$set('showModal', true)">
            <i class="fa fa-plus me-1"></i> Assign Component
        </button>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" class="form-control form-control-sm" placeholder="Search by component name..."
                        wire:model.debounce.400ms="search">
                </div>
                <div class="col-md-4">
                    <select class="form-select form-select-sm" wire:model="employeeId">
                        <option value="">All Employees</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">
                                {{ $emp->user->first_name }} {{ $emp->user->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Component</th>
                            <th>Type</th>
                            <th>Amount (TZS)</th>
                            <th>Recurring</th>
                            <th>Ends At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>
                                    <div class="fw-medium">
                                        {{ $item->employee->user->first_name }}
                                        {{ $item->employee->user->last_name }}
                                    </div>
                                    <small class="text-muted">{{ $item->employee->opf_number }}</small>
                                </td>
                                <td>{{ $item->component->name }}</td>
                                <td>
                                    @if ($item->component->type === 'Earning')
                                        <span class="badge bg-success-subtle text-success">Earning</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Deduction</span>
                                    @endif
                                </td>
                                <td class="fw-medium">
                                    {{ number_format($item->custom_amount, 2) }}
                                </td>
                                <td>
                                    @if ($item->is_recurring)
                                        <span class="badge bg-info-subtle text-info">Yes</span>
                                    @else
                                        <span class="text-muted small">One-time</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $item->ends_at?->format('d M Y') ?? '—' }}
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" wire:click="openEdit({{ $item->id }})">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No employee components assigned yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($items->hasPages())
            <div class="card-footer">{{ $items->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.45);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editId ? 'Edit Component' : 'Assign Component' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="emp_id">
                                    <option value="">-- Select Employee --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->user->first_name }} {{ $emp->user->last_name }}
                                            ({{ $emp->opf_number }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('emp_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Component <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="component_id">
                                    <option value="">-- Select Component --</option>
                                    @foreach ($components as $comp)
                                        <option value="{{ $comp->id }}">
                                            {{ $comp->name }}
                                            ({{ $comp->type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('component_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" wire:model.defer="custom_amount"
                                    placeholder="0.00">
                                @error('custom_amount') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ends At</label>
                                <input type="date" class="form-control" wire:model.defer="ends_at">
                                <small class="text-muted">Leave blank if no end date</small>
                                @error('ends_at') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="is_recurring"
                                        id="is_recurring">
                                    <label class="form-check-label" for="is_recurring">
                                        Recurring every pay period
                                    </label>
                                </div>
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