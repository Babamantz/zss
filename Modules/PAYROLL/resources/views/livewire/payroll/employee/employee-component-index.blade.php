{{-- resources/views/payroll/livewire/payroll/employee-components/index.blade.php --}}

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Employee Components</h4>
            <small class="text-muted">
                Per-employee salary component overrides with fixed or percentage calculation
            </small>
        </div>
        <button class="btn btn-primary btn-sm" wire:click="$set('showModal', true)">
            <i class="fa fa-plus me-1"></i> Assign Component
        </button>
    </div>

    {{-- ── Flash ───────────────────────────────────────────────────────────── --}}
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

    {{-- ── Stat Cards ───────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @php
            use Modules\PAYROLL\Enums\CalculationType;
            $ecStats = [
                [
                    'label' => 'Total Assignments',
                    'value' => \Modules\PAYROLL\Models\EmployeeComponent::active()->count(),
                    'icon' => 'fa-link',
                    'color' => 'primary',
                    'sub' => 'active assignments',
                ],
                [
                    'label' => 'Fixed Amount',
                    'value' => \Modules\PAYROLL\Models\EmployeeComponent::active()
                        ->where('calculation_type', CalculationType::FIXED)->count(),
                    'icon' => 'fa-lock',
                    'color' => 'success',
                    'sub' => 'fixed overrides',
                ],
                [
                    'label' => 'Percentage Based',
                    'value' => \Modules\PAYROLL\Models\EmployeeComponent::active()
                        ->where('calculation_type', CalculationType::PERCENTAGE)->count(),
                    'icon' => 'fa-percent',
                    'color' => 'warning',
                    'sub' => 'percentage based',
                ],
                [
                    'label' => 'Recurring',
                    'value' => \Modules\PAYROLL\Models\EmployeeComponent::active()
                        ->where('is_recurring', true)->count(),
                    'icon' => 'fa-rotate',
                    'color' => 'info',
                    'sub' => 'every pay period',
                ],
            ];
        @endphp

        @foreach ($ecStats as $card)
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-{{ $card['color'] }}-subtle p-3 flex-shrink-0">
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
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fa fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" class="form-control form-control-sm border-start-0 ps-0"
                            placeholder="Search by component name..." wire:model.live.debounce.400ms="search">
                    </div>
                </div>

                {{-- Employee filter --}}
                <div class="col-md-3">
                    <select class="form-select form-select-sm" wire:model.live="employeeId">
                        <option value="">All Employees</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">
                                {{ $emp->user->first_name }}
                                {{ $emp->user->last_name }}
                                ({{ $emp->opf_number ?? 'No OPF' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Calculation type filter --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filterCalculationType">
                        <option value="">All Calculations</option>
                        <option value="{{ \Modules\PAYROLL\Enums\CalculationType::FIXED }}">
                            Fixed
                        </option>
                        <option value="{{ \Modules\PAYROLL\Enums\CalculationType::PERCENTAGE }}">
                            Percentage
                        </option>
                    </select>
                </div>

                {{-- Recurring filter --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filterRecurring">
                        <option value="">All</option>
                        <option value="1">Recurring</option>
                        <option value="0">One-time</option>
                    </select>
                </div>

                {{-- Clear --}}
                
                <div class="col-md-2 text-end">
                    @if ($search || $employeeId || $filterCalculationType || $filterRecurring)
                        <button class="btn btn-sm btn-outline-secondary" wire:click="clearFilters">
                            <i class="fa fa-times me-1"></i> Clear
                        </button>
                    @endif
                </div>

            </div>
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
                            <th style="font-size:11px;padding:11px 16px;">Employee</th>
                            <th style="font-size:11px;padding:11px 16px;">Component</th>
                            <th style="font-size:11px;padding:11px 16px;">Type</th>
                            <th style="font-size:11px;padding:11px 16px;">Calculation</th>
                            <th style="font-size:11px;padding:11px 16px;">Amount / %</th>
                            <th style="font-size:11px;padding:11px 16px;">Recurring</th>
                            <th style="font-size:11px;padding:11px 16px;">Ends At</th>
                            <th style="font-size:11px;padding:11px 16px;" class="text-end">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                                            <tr wire:key="ec-{{ $item->id }}">

                                                {{-- # --}}
                                                <td style="padding:11px 16px;" class="text-muted small">
                                                    {{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                                                </td>

                                                {{-- Employee --}}
                                                <td style="padding:11px 16px;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="rounded-circle bg-primary-subtle
                                                                d-flex align-items-center justify-content-center
                                                                flex-shrink-0" style="width:30px;height:30px;font-size:10px;
                                                                       font-weight:600;color:#1a237e;">
                                                            {{ strtoupper(substr($item->employee->user->first_name, 0, 1)) }}
                                                            {{ strtoupper(substr($item->employee->user->last_name, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium small">
                                                                {{ $item->employee->user->first_name }}
                                                                {{ $item->employee->user->last_name }}
                                                            </div>
                                                            <div class="text-muted" style="font-size:11px;">
                                                                {{ $item->employee->opf_number ?? '—' }}
                                                                &bull;
                                                                <span class="badge
                                                                        {{ $item->employee->isPermanent()
                            ? 'bg-primary-subtle text-primary'
                            : 'bg-warning-subtle text-warning' }}" style="font-size:9px;">
                                                                    {{ $item->employee->employmentType->name }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Component name --}}
                                                <td style="padding:11px 16px;">
                                                    <div class="small fw-medium">
                                                        {{ $item->component->name }}
                                                    </div>
                                                    @if ($item->component->is_global_component)
                                                        <small class="text-info" style="font-size:10px;">
                                                            <i class="fa fa-globe me-1"></i> Global
                                                        </small>
                                                    @endif
                                                </td>

                                                {{-- Component type --}}
                                                <td style="padding:11px 16px;">
                                                    @if ($item->component->isEarning())
                                                        <span class="badge bg-success-subtle text-success">
                                                            <i class="fa fa-arrow-up fa-xs me-1"></i>
                                                            Earning
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">
                                                            <i class="fa fa-arrow-down fa-xs me-1"></i>
                                                            Deduction
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- Calculation type --}}
                                                <td style="padding:11px 16px;">
                                                    @if ($item->isFixed())
                                                        <span class="badge bg-primary-subtle text-primary">
                                                            <i class="fa fa-lock fa-xs me-1"></i>
                                                            Fixed
                                                        </span>
                                                        <div class="text-muted" style="font-size:10px;">
                                                            Superior
                                                        </div>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">
                                                            <i class="fa fa-percent fa-xs me-1"></i>
                                                            Percentage
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- Amount / percentage --}}
                                                <td style="padding:11px 16px;" class="fw-medium small">
                                                    @if ($item->isFixed())
                                                        TZS {{ number_format($item->custom_amount, 2) }}
                                                    @else
                                                        {{ number_format($item->percentage_value, 4) }}%
                                                        <div class="text-muted" style="font-size:11px;">
                                                            of gross
                                                        </div>
                                                    @endif
                                                </td>

                                                {{-- Recurring --}}
                                                <td style="padding:11px 16px;">
                                                    @if ($item->is_recurring)
                                                        <span class="badge bg-info-subtle text-info">
                                                            <i class="fa fa-rotate fa-xs me-1"></i>
                                                            Recurring
                                                        </span>
                                                    @else
                                                        <span class="text-muted small">One-time</span>
                                                    @endif
                                                </td>

                                                {{-- Ends at --}}
                                                <td style="padding:11px 16px;" class="small">
                                                    @if ($item->ends_at)
                                                        @php
                                                            $daysLeft = now()->diffInDays($item->ends_at, false);
                                                            $isExpiringSoon = $daysLeft <= 30 && $daysLeft >= 0;
                                                        @endphp
                                                        <span class="{{ $isExpiringSoon ? 'text-warning fw-medium' : 'text-muted' }}">
                                                            {{ $item->ends_at->format('d M Y') }}
                                                        </span>
                                                        @if ($isExpiringSoon)
                                                            <div style="font-size:10px;" class="text-warning">
                                                                Expires in {{ $daysLeft }} days
                                                            </div>
                                                        @elseif ($daysLeft < 0)
                                                            <div style="font-size:10px;" class="text-danger">
                                                                Expired
                                                            </div>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">No end date</span>
                                                    @endif
                                                </td>

                                                {{-- Actions --}}
                                                <td style="padding:11px 16px;" class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <button class="btn btn-sm btn-outline-secondary"
                                                            wire:click="openEdit({{ $item->id }})" title="Edit">
                                                            <i class="fa fa-pencil"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td style="padding:11px 16px;" class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <button class="btn btn-sm btn-outline-danger"
                                                        wire:confirm = "Are you sure you want to delete this?"
                                                            wire:click="openEdit({{ $item->id }})" title="Edit">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fa fa-link fa-2x d-block mb-2 opacity-25"></i>
                                    No employee components assigned yet.
                                    <a href="javascript:void(0)" wire:click="$set('showModal', true)"
                                        class="d-block mt-1 small text-primary">
                                        Assign the first component
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($items->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2">
                <small class="text-muted">
                    Showing {{ $items->firstItem() }}–{{ $items->lastItem() }}
                    of {{ $items->total() }} assignments
                </small>
                {{ $items->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
    CREATE / EDIT MODAL
    ══════════════════════════════════════════════════════════════════════ --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.45);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-link me-2 text-primary"></i>
                            {{ $editId ? 'Edit Component Assignment' : 'Assign Component' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Employee --}}
                            <div class="col-12">
                                <label class="form-label">
                                    Employee <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" wire:model.live="emp_id" {{ $editId ? 'disabled' : '' }}>
                                    <option value="">-- Select Employee --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->user->first_name }}
                                            {{ $emp->user->last_name }}
                                            ({{ $emp->opf_number ?? 'No OPF' }})
                                            — {{ $emp->employmentType?->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('emp_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Component --}}
                            <div class="col-12">
                                <label class="form-label">
                                    Salary Component <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" wire:model.live="component_id">
                                    <option value="">-- Select Component --</option>
                                    @foreach ($components as $comp)
                                        <option value="{{ $comp->id }}">
                                            {{ $comp->name }}
                                            ({{ $comp->type }})
                                            @if ($comp->is_global_component)
                                                — Global
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('component_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                                {{-- Component info banner --}}
                                @if ($component_id)
                                    @php
                                        $selectedComp = $components->find($component_id);
                                    @endphp
                                    @if ($selectedComp)
                                        <div class="mt-2 p-2 bg-light rounded-3 small">
                                            <div class="d-flex gap-3">
                                                <span>
                                                    Type:
                                                    <strong>{{ $selectedComp->type }}</strong>
                                                </span>
                                                <span>
                                                    Default calc:
                                                    <strong>{{ ucfirst($selectedComp->calculation_type) }}</strong>
                                                </span>
                                                @if ($selectedComp->is_global_component)
                                                    <span class="text-info">
                                                        <i class="fa fa-globe me-1"></i>
                                                        Global — uses gross salary as base
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            {{-- Calculation type override --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Calculation Method <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" wire:model.live="calculation_type">
                                    @foreach (\Modules\PAYROLL\Enums\CalculationType::ALL as $ct)
                                        <option value="{{ $ct }}">
                                            {{ ucfirst($ct) }}
                                            @if ($ct === \Modules\PAYROLL\Enums\CalculationType::FIXED)
                                                (Fixed — Superior)
                                            @else
                                                (% of gross)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    Fixed amount always overrides percentage when set.
                                </small>
                                @error('calculation_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Value --}}
                            <div class="col-md-6">
                                @if ($calculation_type === \Modules\PAYROLL\Enums\CalculationType::FIXED)
                                    <label class="form-label">
                                        Fixed Amount (TZS) <span class="text-danger">*</span>
                                        <span class="badge bg-primary-subtle text-primary ms-1" style="font-size:10px;">
                                            Superior
                                        </span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text small">TZS</span>
                                        <input type="number" step="0.01" min="0" class="form-control"
                                            wire:model.defer="custom_amount" placeholder="0.00">
                                    </div>
                                    @error('custom_amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @else
                                    <label class="form-label">
                                        Percentage <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.0001" min="0" max="100" class="form-control"
                                            wire:model.defer="percentage_value" placeholder="e.g. 7.5">
                                        <span class="input-group-text small">%</span>
                                    </div>
                                    <small class="text-muted">
                                        of employee gross salary
                                    </small>
                                    @error('percentage_value')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            {{-- Ends at --}}
                            <div class="col-md-6">
                                <label class="form-label">Ends At</label>
                                <input type="date" class="form-control" wire:model.defer="ends_at">
                                <small class="text-muted">
                                    Leave blank for no end date
                                </small>
                                @error('ends_at')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Recurring --}}
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" wire:model="is_recurring"
                                        id="is_recurring">
                                    <label class="form-check-label" for="is_recurring">
                                        <strong>Recurring</strong>
                                        — apply every pay period automatically
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" wire:click="$set('showModal', false)">
                            Cancel
                        </button>
                        <button class="btn btn-primary btn-sm" wire:click="save" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">
                                <i class="fa fa-save me-1"></i>
                                {{ $editId ? 'Update Assignment' : 'Assign Component' }}
                            </span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm"></span>
                                Saving...
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>