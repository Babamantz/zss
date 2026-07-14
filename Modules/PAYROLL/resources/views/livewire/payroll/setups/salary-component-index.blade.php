{{-- resources/views/payroll/livewire/payroll/salary-components/index.blade.php --}}

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Salary Components</h4>
            <small class="text-muted">
                Manage earnings and deductions applied during payroll processing
            </small>
        </div>
        <button class="btn btn-primary btn-sm" wire:click="openCreate">
            <i class="fa fa-plus me-1"></i> New Component
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
        <div class="row g-3 mb-4">
            @foreach($statCards as $card)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }} me-3">
                                <i class="fa {{ $card['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold">{{ $card['value'] }}</div>
                                <div class="text-muted small">{{ $card['label'] }}</div>
                                <div class="text-muted small">{{ $card['sub'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @foreach ($statCards as $card)
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
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fa fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" class="form-control form-control-sm border-start-0 ps-0"
                            placeholder="Search components..." wire:model.live.debounce.400ms="search">
                    </div>
                </div>

                {{-- Type filter --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filterType">
                        <option value="">All Types</option>
                        <option value="{{ \Modules\PAYROLL\Enums\ComponentType::EARNING }}">
                            Earnings
                        </option>
                        <option value="{{ \Modules\PAYROLL\Enums\ComponentType::DEDUCTION }}">
                            Deductions
                        </option>
                    </select>
                </div>

                {{-- Calculation type filter --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filterCalculationType">
                        <option value="">All Calculations</option>
                        <option value="{{ \Modules\PAYROLL\Enums\CalculationType::FIXED }}">
                            Fixed Amount
                        </option>
                        <option value="{{ \Modules\PAYROLL\Enums\CalculationType::PERCENTAGE }}">
                            Percentage
                        </option>
                    </select>
                </div>

                {{-- Global filter --}}
                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filterGlobal">
                        <option value="">All Scopes</option>
                        <option value="global">Global Components</option>
                        <option value="normal">Normal Components</option>
                    </select>
                </div>

                {{-- Clear --}}
                <div class="col-md-2 text-end">
                    @if ($search || $filterType || $filterCalculationType || $filterGlobal)
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
                            <th style="font-size:11px;padding:11px 16px;">Name</th>
                            <th style="font-size:11px;padding:11px 16px;">Type</th>
                            <th style="font-size:11px;padding:11px 16px;">Calculation</th>
                            <th style="font-size:11px;padding:11px 16px;">Value</th>
                            <th style="font-size:11px;padding:11px 16px;">Scope</th>
                            <th style="font-size:11px;padding:11px 16px;">Applies To</th>
                            <th style="font-size:11px;padding:11px 16px;">Global Flag</th>
                            <th style="font-size:11px;padding:11px 16px;" class="text-end">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salary_components as $comp)
                            <tr wire:key="comp-{{ $comp->id }}">

                                {{-- # --}}
                                <td style="padding:11px 16px;" class="text-muted small">
                                    {{ ($salary_components->currentPage() - 1) * $salary_components->perPage() + $loop->iteration }}
                                </td>

                                {{-- Name --}}
                                <td style="padding:11px 16px;">
                                    <div class="fw-medium small">{{ $comp->component?->name ?? '' }}</div>
                                    @if ($comp->is_global_component)
                                        <small class="text-info" style="font-size:10px;">
                                            <i class="fa fa-globe me-1"></i>
                                            Calculated from Total Gross
                                        </small>
                                    @endif
                                </td>

                                {{-- Type --}}
                                <td style="padding:11px 16px;">
                                    @if ($comp->isEarning())
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
                                    @if ($comp->isFixed())
                                        <span class="badge bg-primary-subtle text-primary">
                                            <i class="fa fa-lock fa-xs me-1"></i>
                                            Fixed
                                        </span>
                                    @elseif($comp->isPercentage())
                                        <span class="badge bg-warning-subtle text-warning">
                                            <i class="fa fa-percent fa-xs me-1"></i>
                                            Percentage
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">
                                            <i class="fa fa-gear fa-xs me-1"></i>
                                            Auto
                                        </span>
                                    @endif
                                </td>

                                {{-- Value --}}
                                <td style="padding:11px 16px;" class="small fw-medium">
                                    @if ($comp->isFixed())
                                        TZS {{ number_format($comp->amount ?? 0, 2) }}
                                        <small class="text-muted d-block" style="font-size:10px;">
                                            Fixed — overrides %
                                        </small>
                                    @elseif($comp->isPercentage())
                                        {{ number_format($comp->percentage_value, 2) }}%
                                        <small class="text-muted d-block" style="font-size:10px;">
                                            of gross salary
                                        </small>
                                    @else
                                        {{ number_format($comp->percentage_value, 2) }}
                                        <small class="text-muted d-block" style="font-size:10px;">
                                            auto
                                        </small>
                                    @endif
                                </td>

                                {{-- Scope (global component vs normal) --}}
                                <td style="padding:11px 16px;">
                                    @if ($comp->is_global_component)
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="fa fa-globe fa-xs me-1"></i>
                                            Global
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="fa fa-user fa-xs me-1"></i>
                                            Per Employee
                                        </span>
                                    @endif
                                </td>

                                {{-- Applies to employment type --}}
                                <td style="padding:11px 16px;">
                                    @php
                                        $atColor = match ($comp->applies_to) {
                                            \Modules\PAYROLL\Enums\AppliesTo::ALL => ['secondary', 'All Staff'],
                                            \Modules\PAYROLL\Enums\AppliesTo::PERMANENT => ['primary', 'Permanent'],
                                            \Modules\PAYROLL\Enums\AppliesTo::NON_PERMANENT => ['warning', 'Non-Permanent'],
                                            default => ['secondary', ucfirst($comp->applies_to)],
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $atColor[0] }}-subtle
                                                                text-{{ $atColor[0] }}" style="font-size:10px;">
                                        {{ $atColor[1] }}
                                    </span>
                                </td>

                                {{-- is_global (old flag — auto-applied) --}}
                                <td style="padding:11px 16px;">
                                    @if ($comp->is_global)
                                        <span class="badge bg-success-subtle text-success" style="font-size:10px;">
                                            <i class="fa fa-check me-1"></i> Auto-apply
                                        </span>
                                    @else
                                        <span class="text-muted small">Manual</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td style="padding:11px 16px;" class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <button class="btn btn-sm btn-outline-secondary"
                                            wire:click="openEdit({{ $comp->id }})" title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $comp->id }})"
                                            wire:confirm="Remove this component? It will be soft-deleted." title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fa fa-sliders fa-2x d-block mb-2 opacity-25"></i>
                                    No salary components found.
                                    <a href="javascript:void(0)" wire:click="openCreate"
                                        class="d-block mt-1 small text-primary">
                                        Create the first component
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($salary_components->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2">
                <small class="text-muted">
                    Showing {{ $salary_components->firstItem() }}–{{ $salary_components->lastItem() }}
                    of {{ $salary_components->total() }} components
                </small>
                {{ $salary_components->links() }}
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
                            <i class="fa fa-sliders me-2 text-primary"></i>
                            {{ $editId ? 'Edit Component' : 'New Salary Component' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="resetModal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Name --}}
                            <div class="col-md-12">
                                <label class="form-label">
                                    Name <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" wire:model="componentNameId">
                                    <option value="">-- Select --</option>
                                    @foreach ($components as $id => $comp)
                                        <option value="{{ $id }}">
                                            {{ ucfirst($comp) }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('componentNameId')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                         

                            {{-- Calculation type --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Calculation Method <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" wire:model.live="calculation_type">
                                    <option value="">-- Select --</option>
                                    @foreach (\Modules\PAYROLL\Enums\CalculationType::ALL as $ct)
                                        <option value="{{ $ct }}">
                                            {{ ucfirst($ct) }}
                                            @if ($ct === \Modules\PAYROLL\Enums\CalculationType::FIXED)
                                                (Superior — overrides %)
                                            @elseif ($ct === \Modules\PAYROLL\Enums\CalculationType::PERCENTAGE)
                                                (% of gross salary)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('calculation_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Value input — changes based on calculation type --}}
                            <div class="col-md-6">
                                @if ($calculation_type === \Modules\PAYROLL\Enums\CalculationType::FIXED)
                                    <label class="form-label">
                                        Fixed Amount (TZS)
                                        <span class="text-danger">*</span>
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
                                @elseif ($calculation_type === \Modules\PAYROLL\Enums\CalculationType::PERCENTAGE)
                                    <label class="form-label">
                                        Percentage Value <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.0001" min="0" max="100" class="form-control"
                                            wire:model.defer="percentage_value" placeholder="e.g. 7.5">
                                        <span class="input-group-text small">%</span>
                                    </div>
                                    <small class="text-muted">
                                        of Total Gross Salary
                               
                                    </small>
                                    @error('percentage_value')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @else
                                    <label class="form-label">Value</label>
                                    <input type="text" class="form-control" disabled
                                        placeholder="Select calculation method first">
                                @endif
                            </div>

                            {{-- Applies To --}}
                            <div class="col-md-6">
                                <label class="form-label">Applies To</label>
                                <select class="form-select" wire:model.live="applies_to">
                                    <option value="">
                                        All
                                    </option>

                                    @forelse ($appliesTo as $id => $name)
                                        <option value="{{ $id }}">
                                            {{ ucfirst($name) }}
                                        </option>
                                    @empty
                                        <p>No entries found </p>

                                    @endforelse
                                </select>
                                @error('applies_to')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Flags --}}
                            <div class="col-md-6">
                                <label class="form-label d-block">Component Flags</label>
                                <div class="d-flex flex-column gap-2">
                                  
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="is_global"
                                            id="is_global">
                                        <label class="form-check-label small" for="is_global">
                                            <strong>Auto-apply</strong>
                                            — automatically assigned to employees
                                            on payroll run
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" wire:click="resetModal">Cancel</button>
                        <button class="btn btn-primary btn-sm" wire:click="save" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">
                                <i class="fa fa-save me-1"></i>
                                {{ $editId ? 'Update' : 'Save Component' }}
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