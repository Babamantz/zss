
<div class="container-fluid">

    <div class="mb-4">
        <h4 class="mb-1">Run Payroll</h4>
        <small class="text-muted">Process employee salaries for a selected pay period</small>
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

    <div class="row g-4">

        {{-- Left — Controls --}}
        {{-- Left — Controls --}}
<div class="col-md-4">
    <div class="card h-100">
        <div class="card-header">
            <h6 class="mb-0">Payroll Settings</h6>
        </div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">
                    Pay Period <span class="text-danger">*</span>
                </label>
                {{-- .live so $pay_period_id is set on the server immediately --}}
                <select class="form-select" wire:model.live="pay_period_id">
                    <option value="">-- Select Period --</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}">
                            {{ $period->start_date->format('d M') }}
                            –
                            {{ $period->end_date->format('d M Y') }}
                            ({{ str_replace('_', ' ', $period->status) }})
                        </option>
                    @endforeach
                </select>
                @error('pay_period_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Confirmation checkbox --}}
            <div class="form-check mb-4">
                {{-- .live so $confirmed syncs immediately --}}
                <input class="form-check-input" type="checkbox"
                    wire:model.live="confirmed" id="confirm_run">
                <label class="form-check-label small" for="confirm_run">
                    I confirm that all employee components are correctly configured
                    and this run will overwrite any previous draft entries.
                </label>
            </div>

            <div class="d-grid gap-2">
                {{-- Alpine reads $wire properties reactively — no server round-trip needed --}}
                <button class="btn btn-primary"
                    wire:click="process"
                    wire:loading.attr="disabled"
                    x-bind:disabled="!$wire.confirmed || !$wire.pay_period_id"
                    x-bind:class="(!$wire.confirmed || !$wire.pay_period_id)
                        ? 'btn btn-primary opacity-50'
                        : 'btn btn-primary'">
                    <span wire:loading.remove wire:target="process">
                        <i class="fa fa-play me-1"></i> Process Payroll
                    </span>
                    <span wire:loading wire:target="process">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Processing...
                    </span>
                </button>

                @if ($processed)
                    <button class="btn btn-success"
                        wire:click="lock"
                        wire:confirm="Lock this pay period? This cannot be undone."
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="lock">
                            <i class="fa fa-lock me-1"></i> Lock & Complete
                        </span>
                        <span wire:loading wire:target="lock">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Locking...
                        </span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Processing flow guide --}}
        <div class="card-footer bg-transparent">
            <p class="small text-muted mb-2 fw-medium">Processing flow</p>
            <div class="d-flex flex-column gap-1">
                @foreach ([
                    ['Draft',      'secondary', 'circle'],
                    ['Processing', 'warning',   'play-circle'],
                    ['Locked',     'success',   'lock'],
                ] as [$label, $color, $icon])
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-{{ $icon }} text-{{ $color }}"></i>
                        <small class="text-muted">{{ $label }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
       

        {{-- Right — Results --}}
        <div class="col-md-8">
            @if ($processed && count($results))
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            Processing Results
                            <span class="badge bg-primary ms-1">{{ count($results) }} employees</span>
                        </h6>
                        <small class="text-muted">Review before locking</small>
                    </div>

                    {{-- Summary Totals --}}
                    @php
                        $totalNet = collect($results)->sum('net_pay');
                    @endphp
                    <div class="card-body border-bottom pb-3 mb-0">
                        <div class="row text-center g-2">
                            <div class="col-4">
                                <p class="text-muted small mb-0">Employees</p>
                                <h5 class="mb-0">{{ count($results) }}</h5>
                            </div>
                            <div class="col-4">
                                <p class="text-muted small mb-0">Total Net Pay</p>
                                <h5 class="mb-0 text-success">
                                    TZS {{ number_format($totalNet, 0) }}
                                </h5>
                            </div>
                            <div class="col-4">
                                <p class="text-muted small mb-0">Status</p>
                                <span class="badge bg-warning-subtle text-warning">Processing</span>
                            </div>
                        </div>
                    </div>

                    {{-- Per-employee table --}}
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">

                            <thead class="table-light">
    <tr>
        <th>#</th>
        <th>Employee</th>
        <th>OPF No.</th>
        <th class="text-end">Gross (TZS)</th>
        <th class="text-end">Deductions (TZS)</th>
        <th class="text-end">Net Pay (TZS)</th>
    </tr>
</thead>
<tbody>
    @foreach ($results as $i => $row)
        <tr>
            <td class="text-muted small">{{ $i + 1 }}</td>
            <td class="fw-medium small">{{ $row['employee_name'] }}</td>
            <td class="text-muted small">{{ $row['opf_number'] }}</td>
            <td class="text-end small">{{ number_format($row['gross'], 2) }}</td>
            <td class="text-end small text-danger">{{ number_format($row['deductions'], 2) }}</td>
            <td class="text-end fw-bold text-success">
                {{ number_format($row['net_pay'], 2) }}
            </td>
        </tr>
    @endforeach
</tbody>
<tfoot class="table-light">
    <tr>
        <th colspan="5" class="text-end">Total Net</th>
        <th class="text-end text-success">
            TZS {{ number_format(collect($results)->sum('net_pay'), 0) }}
        </th>
    </tr>
</tfoot>
                            
                           
                           
                        </table>
                    </div>
                </div>
            @elseif (!$processed)
                <div class="card h-100 d-flex align-items-center justify-content-center text-center">
                    <div class="card-body py-5">
                        <i class="fa fa-coins fa-3x text-muted mb-3 d-block"></i>
                        <h6 class="text-muted">No payroll run yet</h6>
                        <p class="small text-muted">
                            Select a pay period and click <strong>Process Payroll</strong> to begin.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>