{{-- resources/views/hrm/livewire/payroll/payroll-entries/show.blade.php --}}

<div class="container-fluid">

    {{-- ── Toolbar ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('payroll.entries') }}"
                class="btn btn-sm btn-outline-secondary me-2">
                <i class="fa fa-arrow-left me-1"></i> Back
            </a>
            <span class="text-muted small">Payslip Detail</span>
        </div>
        <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
            <i class="fa fa-print me-1"></i> Print Payslip
        </button>
    </div>

    {{-- ── Payslip Card ─────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm" id="payslip">
        {{-- Header --}}
        <div class="card-header border-bottom py-4"
            style="background: linear-gradient(135deg, #1a237e 0%, #283593 100%);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h4 class="text-white mb-1">PAYSLIP</h4>
                    <p class="text-white-50 mb-0 small">
                        Pay Period:
                        <strong class="text-white">
                            {{ \Carbon\Carbon::parse($entry->payPeriod->start_date)->format('d M Y') }}
                            &ndash;
                            {{ \Carbon\Carbon::parse($entry->payPeriod->end_date)->format('d M Y') }}
                        </strong>
                    </p>
                </div>
                <div class="text-end">
                    @php
                        $statusColor = match($entry->payPeriod->status) {
                            'Locked_Completed' => '#4caf50',
                            'Processing'       => '#ff9800',
                            default            => '#9e9e9e',
                        };
                    @endphp
                    <span class="badge px-3 py-2"
                        style="background:{{ $statusColor }}; font-size:12px;">
                        {{ str_replace('_', ' ', $entry->payPeriod->status) }}
                    </span>
                    <p class="text-white-50 small mt-1 mb-0">
                        Processed:
                        {{ \Carbon\Carbon::parse($entry->processed_at)?->format('d M Y H:i') ?? 'Pending' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="card-body p-4">

            {{-- ── Employee Info ───────────────────────────────────────────── --}}
            <div class="row g-4 mb-4 pb-4 border-bottom">
                <div class="col-md-6">
                    <p class="small text-muted fw-medium mb-2 text-uppercase">Employee Details</p>
                    <div class="d-flex gap-3 align-items-start">
                        {{-- Avatar --}}
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:56px;height:56px;background:#E8EAF6;font-size:20px;font-weight:600;color:#3949AB;">
                            {{ strtoupper(substr($entry->employee->user->first_name, 0, 1)) }}
                            {{ strtoupper(substr($entry->employee->user->last_name,  0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-1">
                                {{ $entry->employee->user->first_name }}
                                {{ $entry->employee->user->middle_name }}
                                {{ $entry->employee->user->last_name }}
                            </h5>
                            <p class="text-muted small mb-0">
                                {{ $entry->employee->user->email }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <p class="small text-muted fw-medium mb-2 text-uppercase">Employment Info</p>
                    <div class="row g-1">
                        @foreach ([
                            ['OPF Number',  $entry->employee->opf_number  ?? '—'],
                            ['Department',  $entry->employee->department?->name ?? '—'],
                            ['Unit',        $entry->employee->unit?->name ?? '—'],
                            ['Education',   ucfirst($entry->employee->education ?? '—')],
                            ['Processed By',$entry->processedBy?->name ?? '—'],
                        ] as [$label, $value])
                            <div class="col-6">
                                <small class="text-muted d-block">{{ $label }}</small>
                                <span class="small fw-medium">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── Earnings & Deductions side by side ─────────────────────── --}}
            <div class="row g-4 mb-4">

                {{-- Earnings --}}
                <div class="col-md-6">
                    <div class="card border-0 bg-success-subtle h-100">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h6 class="text-success mb-0">
                                <i class="fa fa-arrow-up-circle me-1"></i> Earnings
                            </h6>
                        </div>
                        <div class="card-body pt-2">
                            <table class="table table-sm table-borderless mb-0">
                                <thead>
                                    <tr class="small text-muted">
                                        <th>Component</th>
                                        <th class="text-end">Amount (TZS)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($earnings as $item)
                                        <tr>
                                            <td class="small">
                                                {{ $item->component_name_snapshot }}
                                            </td>
                                            <td class="text-end small fw-medium">
                                                {{ number_format($item->finalized_amount, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-muted small text-center py-2">
                                                No earnings recorded.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="border-top">
                                        <th class="small">Total Earnings</th>
                                        <th class="text-end text-success">
                                            TZS {{ number_format($entry->total_gross, 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Deductions --}}
                <div class="col-md-6">
                    <div class="card border-0 bg-danger-subtle h-100">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h6 class="text-danger mb-0">
                                <i class="fa fa-arrow-down-circle me-1"></i> Deductions
                            </h6>
                        </div>
                        <div class="card-body pt-2">
                            <table class="table table-sm table-borderless mb-0">
                                <thead>
                                    <tr class="small text-muted">
                                        <th>Component</th>
                                        <th class="text-end">Amount (TZS)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($deductions as $item)
                                        <tr>
                                            <td class="small">
                                                {{ $item->component_name_snapshot }}
                                            </td>
                                            <td class="text-end small fw-medium text-danger">
                                                {{ number_format($item->finalized_amount, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-muted small text-center py-2">
                                                No deductions recorded.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="border-top">
                                        <th class="small">Total Deductions</th>
                                        <th class="text-end text-danger">
                                            TZS {{ number_format($entry->total_deductions, 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Net Pay Banner ──────────────────────────────────────────── --}}
            <div class="rounded-3 p-4 text-center mb-4"
                style="background:linear-gradient(135deg,#1B5E20,#2E7D32);">
                <p class="text-white-50 small mb-1">NET PAY</p>
                <h2 class="text-white mb-0">
                    TZS {{ number_format($entry->net_pay, 2) }}
                </h2>
                <p class="text-white-50 small mt-1 mb-0">
                    After all deductions for the period ending
                    {{ \Carbon\Carbon::parse($entry->payPeriod->end_date)->format('d M Y') }}
                </p>
            </div>

            {{-- ── Summary Row ─────────────────────────────────────────────── --}}
            <div class="row g-3 text-center">
                @foreach ([
                    ['Total Gross',      $entry->total_gross,       'primary'],
                    ['Total Deductions', $entry->total_deductions,  'danger'],
                    ['Net Pay',          $entry->net_pay,           'success'],
                ] as [$label, $value, $color])
                    <div class="col-4">
                        <div class="card border-0 bg-{{ $color }}-subtle py-3">
                            <p class="small text-muted mb-1">{{ $label }}</p>
                            <h5 class="text-{{ $color }} mb-0">
                                TZS {{ number_format($value, 2) }}
                            </h5>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>{{-- card-body --}}

        {{-- Footer --}}
        <div class="card-footer bg-transparent text-center">
            <small class="text-muted">
                This payslip was generated on {{ now()->format('d M Y H:i') }}
                and is a computer-generated document.
            </small>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #payslip, #payslip * { visibility: visible; }
        #payslip { position: absolute; left: 0; top: 0; width: 100%; }
        .btn, nav, header, aside { display: none !important; }
    }
</style>
@endpush