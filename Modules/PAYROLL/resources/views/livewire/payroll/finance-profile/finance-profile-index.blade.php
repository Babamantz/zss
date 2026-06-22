{{-- resources/views/hrm/livewire/payroll/finance-profiles/index.blade.php --}}

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Finance Profiles</h4>
            <small class="text-muted">
                Manage employee base salaries, bank accounts and tax identifiers
            </small>
        </div>
        <button class="btn btn-primary btn-sm" wire:click="openCreate">
            <i class="fa fa-plus me-1"></i> New Profile
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

    {{-- ── Stat Cards ──────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @php
            $statCards = [
                [
                    'label' => 'Profiled Employees',
                    'value' => number_format($stats['total']),
                    'icon'  => 'fa-users',
                    'color' => 'primary',
                    'sub'   => $stats['no_profile'] . ' still missing profile',
                ],
                [
                    'label' => 'Average Base Salary',
                    'value' => 'TZS ' . number_format($stats['avg_salary'], 0),
                    'icon'  => 'fa-calculator',
                    'color' => 'info',
                    'sub'   => 'across all profiled employees',
                ],
                [
                    'label' => 'Highest Base Salary',
                    'value' => 'TZS ' . number_format($stats['max_salary'], 0),
                    'icon'  => 'fa-arrow-up-circle',
                    'color' => 'success',
                    'sub'   => 'top earner base',
                ],
                [
                    'label' => 'Lowest Base Salary',
                    'value' => 'TZS ' . number_format($stats['min_salary'], 0),
                    'icon'  => 'fa-arrow-down-circle',
                    'color' => 'warning',
                    'sub'   => 'minimum base',
                ],
            ];
        @endphp

        @foreach ($statCards as $card)
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-{{ $card['color'] }}-subtle p-3 flex-shrink-0">
                            <i class="fa {{ $card['icon'] }} text-{{ $card['color'] }} fa-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-muted small mb-0">{{ $card['label'] }}</p>
                            <h5 class="mb-0 text-truncate">{{ $card['value'] }}</h5>
                            <small class="text-muted">{{ $card['sub'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Search ───────────────────────────────────────────────────────────── --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" class="form-control form-control-sm"
                        placeholder="Search by name, bank account, or tax ID..."
                        wire:model.debounce.400ms="search">
                </div>
                <div class="col-md-7 text-end">
                    <small class="text-muted">
                        {{ $profiles->total() }} profile(s) found
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Table ────────────────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>
                                <a href="javascript:void(0)"
                                    wire:click="sortBy('employee_id')"
                                    class="text-dark text-decoration-none">
                                    Employee
                                    @if ($sortField === 'employee_id')
                                        <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fa fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Department</th>
                            <th>
                                <a href="javascript:void(0)"
                                    wire:click="sortBy('base_salary')"
                                    class="text-dark text-decoration-none">
                                    Base Salary (TZS)
                                    @if ($sortField === 'base_salary')
                                        <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fa fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Bank Account</th>
                            <th>Tax ID</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($profiles as $profile)
                            <tr>
                                <td class="text-muted small">
                                    {{ $loop->iteration + ($profiles->currentPage() - 1) * $profiles->perPage() }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        {{-- Avatar --}}
                                        <div class="rounded-circle bg-primary-subtle d-flex align-items-center
                                            justify-content-center flex-shrink-0"
                                            style="width:32px;height:32px;font-size:11px;font-weight:600;color:#1a237e;">
                                            {{ strtoupper(substr($profile->employee->user->first_name, 0, 1)) }}
                                            {{ strtoupper(substr($profile->employee->user->last_name,  0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium small">
                                                {{ $profile->employee->user->first_name }}
                                                {{ $profile->employee->user->last_name }}
                                            </div>
                                            <div class="text-muted" style="font-size:11px;">
                                                {{ $profile->employee->opf_number ?? '—' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="small text-muted">
                                    {{ $profile->employee->department?->name ?? '—' }}
                                </td>
                                <td class="fw-medium">
                                    {{ number_format($profile->base_salary, 2) }}
                                </td>
                                <td class="small font-monospace">
                                    {{ $profile->bank_account_number }}
                                </td>
                                <td class="small text-muted">
                                    {{ $profile->tax_id ?? '—' }}
                                </td>
                                <td class="small text-muted">
                                    {{ $profile->created_at->format('d M Y') }}
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <button class="btn btn-sm btn-outline-info"
                                            wire:click="openView({{ $profile->id }})"
                                            title="View">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary"
                                            wire:click="openEdit({{ $profile->id }})"
                                            title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            wire:click="delete({{ $profile->id }})"
                                            wire:confirm="Remove this finance profile?"
                                            title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fa fa-credit-card fa-2x d-block mb-2"></i>
                                    No finance profiles found.
                                    <a href="javascript:void(0)"
                                        wire:click="openCreate"
                                        class="d-block mt-1 text-primary small">
                                        Create the first one
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($profiles->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Showing {{ $profiles->firstItem() }}–{{ $profiles->lastItem() }}
                    of {{ $profiles->total() }}
                </small>
                {{ $profiles->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         CREATE / EDIT MODAL
    ══════════════════════════════════════════════════════════════════════ --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1"
            style="background:rgba(0,0,0,.45);">
            <div class="modal-dialog modal-md">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-credit-card me-2 text-primary"></i>
                            {{ $editId ? 'Edit Finance Profile' : 'New Finance Profile' }}
                        </h5>
                        <button type="button" class="btn-close"
                            wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Employee --}}
                            <div class="col-12">
                                <label class="form-label">
                                    Employee <span class="text-danger">*</span>
                                </label>
                                <select class="form-select"
                                    wire:model.live="employee_id"
                                    {{ $editId ? 'disabled' : '' }}>
                                    <option value="">-- Select Employee --</option>
                                    @foreach ($availableEmployees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->user->first_name }}
                                            {{ $emp->user->last_name }}
                                            ({{ $emp->opf_number ?? 'No OPF' }})
                                        </option>
                                    @endforeach
                                </select>
                                @if ($editId)
                                    <small class="text-muted">
                                        Employee cannot be changed after creation.
                                    </small>
                                @endif
                                @error('employee_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Base Salary --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Base Salary (TZS) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text small text-muted">TZS</span>
                                    <input type="number" step="0.01" min="0"
                                        class="form-control"
                                        wire:model.defer="base_salary"
                                        placeholder="0.00">
                                </div>
                                @error('base_salary')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Tax ID --}}
                            <div class="col-md-6">
                                <label class="form-label">Tax ID (TIN)</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="tax_id"
                                    placeholder="e.g. TIN-001-2024">
                                @error('tax_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Bank Account --}}
                            <div class="col-12">
                                <label class="form-label">
                                    Bank Account Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control font-monospace"
                                    wire:model.live="bank_account_number"
                                    placeholder="e.g. 1000-2024-0001" readonly>
                                @error('bank_account_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm"
                            wire:click="closeModal">
                            Cancel
                        </button>
                        <button class="btn btn-primary btn-sm"
                            wire:click="save"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">
                                <i class="fa fa-save me-1"></i>
                                {{ $editId ? 'Update Profile' : 'Save Profile' }}
                            </span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Saving...
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         VIEW MODAL — Full profile detail
    ══════════════════════════════════════════════════════════════════════ --}}
    @if ($showView && $viewProfile)
        <div class="modal fade show d-block" tabindex="-1"
            style="background:rgba(0,0,0,.45);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">Finance Profile</h5>
                        <button type="button" class="btn-close"
                            wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body pt-2">

                        {{-- Employee header --}}
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-primary-subtle mb-4">
                            <div class="rounded-circle bg-primary d-flex align-items-center
                                justify-content-center flex-shrink-0"
                                style="width:52px;height:52px;font-size:18px;font-weight:600;color:#fff;">
                                {{ strtoupper(substr($viewProfile->employee->user->first_name, 0, 1)) }}
                                {{ strtoupper(substr($viewProfile->employee->user->last_name,  0, 1)) }}
                            </div>
                            <div>
                                <h5 class="mb-0 text-primary">
                                    {{ $viewProfile->employee->user->first_name }}
                                    {{ $viewProfile->employee->user->middle_name }}
                                    {{ $viewProfile->employee->user->last_name }}
                                </h5>
                                <small class="text-muted">
                                    {{ $viewProfile->employee->user->email }}
                                    &bull;
                                    OPF: {{ $viewProfile->employee->opf_number ?? '—' }}
                                </small>
                            </div>
                        </div>

                        <div class="row g-4">

                            {{-- Left — Profile Details --}}
                            <div class="col-md-6">
                                <p class="small text-muted fw-medium mb-2 text-uppercase">
                                    Finance Details
                                </p>
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        @foreach ([
                                            ['Base Salary',    'TZS ' . number_format($viewProfile->base_salary, 2), 'success'],
                                            ['Bank Account',   $viewProfile->bank_account_number,                    'dark'],
                                            ['Tax ID (TIN)',   $viewProfile->tax_id ?? '—',                          'dark'],
                                            ['Profile Since',  $viewProfile->created_at->format('d M Y'),            'muted'],
                                            ['Last Updated',   $viewProfile->updated_at->format('d M Y H:i'),        'muted'],
                                        ] as [$label, $value, $color])
                                            <div class="d-flex justify-content-between
                                                align-items-center py-2 border-bottom">
                                                <small class="text-muted">{{ $label }}</small>
                                                <span class="small fw-medium text-{{ $color }}
                                                    {{ $label === 'Bank Account' ? 'font-monospace' : '' }}">
                                                    {{ $value }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Employment info --}}
                                <p class="small text-muted fw-medium mb-2 text-uppercase mt-3">
                                    Employment
                                </p>
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        @foreach ([
                                            ['Department', $viewProfile->employee->department?->name ?? '—'],
                                            ['Unit',       $viewProfile->employee->unit?->name ?? '—'],
                                            ['Education',  ucfirst($viewProfile->employee->education ?? '—')],
                                            ['Status',     ucfirst($viewProfile->employee->is_active ?? '—')],
                                        ] as [$label, $value])
                                            <div class="d-flex justify-content-between
                                                align-items-center py-2 border-bottom">
                                                <small class="text-muted">{{ $label }}</small>
                                                <span class="small fw-medium">{{ $value }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Right — Salary Components --}}
                            <div class="col-md-6">
                                <p class="small text-muted fw-medium mb-2 text-uppercase">
                                    Assigned Salary Components
                                </p>

                                @php
                                    $comps      = $viewProfile->employee->components ?? collect();
                                    $earnings   = $comps->filter(fn($c) => $c->component->type === 'Earning');
                                    $deductions = $comps->filter(fn($c) => $c->component->type === 'Deduction');
                                    $totalEarnings   = $earnings->sum('custom_amount');
                                    $totalDeductions = $deductions->sum('custom_amount');
                                @endphp

                                @if ($comps->isEmpty())
                                    <div class="text-center text-muted py-4 border rounded-3">
                                        <i class="fa fa-exclamation-circle d-block mb-1"></i>
                                        <small>No components assigned yet.</small>
                                        <a href="{{ route('payroll.employee-components') }}"
                                            class="d-block mt-1 small">
                                            Assign components
                                        </a>
                                    </div>
                                @else
                                    {{-- Earnings --}}
                                    @if ($earnings->isNotEmpty())
                                        <div class="mb-3">
                                            <p class="small text-success fw-medium mb-1">
                                                <i class="fa fa-arrow-up-circle me-1"></i> Earnings
                                            </p>
                                            @foreach ($earnings as $ec)
                                                <div class="d-flex justify-content-between
                                                    py-1 border-bottom">
                                                    <small class="text-muted">
                                                        {{ $ec->component->name }}
                                                        @if ($ec->is_recurring)
                                                            <span class="badge bg-info-subtle
                                                                text-info ms-1" style="font-size:9px;">
                                                                recurring
                                                            </span>
                                                        @endif
                                                    </small>
                                                    <small class="fw-medium text-success">
                                                        {{ number_format($ec->custom_amount, 2) }}
                                                    </small>
                                                </div>
                                            @endforeach
                                            <div class="d-flex justify-content-between pt-1">
                                                <small class="fw-medium">Total Earnings</small>
                                                <small class="fw-bold text-success">
                                                    TZS {{ number_format($totalEarnings, 2) }}
                                                </small>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Deductions --}}
                                    @if ($deductions->isNotEmpty())
                                        <div class="mb-3">
                                            <p class="small text-danger fw-medium mb-1">
                                                <i class="fa fa-arrow-down-circle me-1"></i> Deductions
                                            </p>
                                            @foreach ($deductions as $ec)
                                                <div class="d-flex justify-content-between
                                                    py-1 border-bottom">
                                                    <small class="text-muted">
                                                        {{ $ec->component->name }}
                                                        @if ($ec->ends_at)
                                                            <span class="text-warning ms-1"
                                                                style="font-size:10px;">
                                                                ends {{ $ec->ends_at->format('M Y') }}
                                                            </span>
                                                        @endif
                                                    </small>
                                                    <small class="fw-medium text-danger">
                                                        {{ number_format($ec->custom_amount, 2) }}
                                                    </small>
                                                </div>
                                            @endforeach
                                            <div class="d-flex justify-content-between pt-1">
                                                <small class="fw-medium">Total Deductions</small>
                                                <small class="fw-bold text-danger">
                                                    TZS {{ number_format($totalDeductions, 2) }}
                                                </small>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Estimated Net --}}
                                    <div class="rounded-3 p-3 text-center mt-2"
                                        style="background:#E8F5E9;">
                                        <small class="text-muted d-block">
                                            Estimated Net Pay
                                        </small>
                                        <h5 class="text-success mb-0">
                                            TZS {{ number_format($totalEarnings - $totalDeductions, 2) }}
                                        </h5>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button class="btn btn-outline-secondary btn-sm"
                            wire:click="closeModal">
                            Close
                        </button>
                        <button class="btn btn-outline-secondary btn-sm"
                            wire:click="openEdit({{ $viewProfile->id }})"
                            wire:click="closeModal">
                            <i class="fa fa-pencil me-1"></i> Edit Profile
                        </button>
                        <a href="{{ route('payroll.employee-components') }}?employee={{ $viewProfile->employee_id }}"
                            class="btn btn-primary btn-sm">
                            <i class="fa fa-sliders me-1"></i> Manage Components
                        </a>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>


    @script
    <script>
        document.addEventListener('livewire:initialized', () => {

        });
    <script>
    @endscript