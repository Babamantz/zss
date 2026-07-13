
<div class="container-fluid">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Payroll Dashboard</h4>
            <small class="text-muted">
                Overview of payroll operations and financial summaries/
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('payroll.pay-periods') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-calendar me-1"></i> Pay Periods
            </a>
            <a href="{{ route('payroll.run') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-play me-1"></i> Run Payroll
            </a>
        </div>
    </div>

    {{-- ── Current Period Banner ───────────────────────────────────────────── --}}
    @if ($latestPeriod)
        @php
            $bannerColor = match($latestPeriod->status) {
                'Draft'            => ['bg' => 'bg-secondary-subtle', 'text' => 'text-secondary', 'badge' => 'bg-secondary'],
                'Processing'       => ['bg' => 'bg-warning-subtle',   'text' => 'text-warning',   'badge' => 'bg-warning'],
                'Locked_Completed' => ['bg' => 'bg-success-subtle',   'text' => 'text-success',   'badge' => 'bg-success'],
                default            => ['bg' => 'bg-light',            'text' => 'text-muted',     'badge' => 'bg-secondary'],
            };
        @endphp
        <div class="card mb-4 border-0 {{ $bannerColor['bg'] }}">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="small text-muted mb-1">Current Pay Period</p>
                    <h5 class="mb-0 {{ $bannerColor['text'] }}">
                        {{ $latestPeriod->start_date->format('d M Y') }}
                        &ndash;
                        {{ $latestPeriod->end_date->format('d M Y') }}
                    </h5>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge {{ $bannerColor['badge'] }} fs-6 px-3 py-2">
                        {{ str_replace('_', ' ', $latestPeriod->status) }}
                    </span>
                    @unless ($latestPeriod->isLocked())
                        <a href="{{ route('payroll.run', $latestPeriod->id) }}"
                            class="btn btn-sm btn-primary">
                            <i class="fa fa-play me-1"></i>
                            {{ $latestPeriod->isDraft() ? 'Start Processing' : 'Continue Processing' }}
                        </a>
                    @endunless
                </div>
            </div>
        </div>
    @endif

    {{-- ── KPI Cards (Current Period) ─────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @php
            $kpis = [
                [
                    'label'   => 'Active Employees',
                    'value'   => number_format($counts['employees']),
                    'icon'    => 'fa-users',
                    'color'   => 'primary',
                    'sub'     => 'on payroll',
                ],
                [
                    'label'   => 'Period Gross',
                    'value'   => $periodStats ? 'TZS ' . number_format($periodStats->total_gross, 0) : '—',
                    'icon'    => 'fa-arrow-up-circle',
                    'color'   => 'info',
                    'sub'     => 'current period',
                ],
                [
                    'label'   => 'Period Deductions',
                    'value'   => $periodStats ? 'TZS ' . number_format($periodStats->total_deductions, 0) : '—',
                    'icon'    => 'fa-arrow-down-circle',
                    'color'   => 'danger',
                    'sub'     => 'current period',
                ],
                [
                    'label'   => 'Period Net Pay',
                    'value'   => $periodStats ? 'TZS ' . number_format($periodStats->total_net, 0) : '—',
                    'icon'    => 'fa-check-circle',
                    'color'   => 'success',
                    'sub'     => $periodStats ? $periodStats->employee_count . ' employees processed' : 'no entries yet',
                ],
            ];
        @endphp

        @foreach ($kpis as $kpi)
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-{{ $kpi['color'] }}-subtle p-3 flex-shrink-0">
                            <i class="fa {{ $kpi['icon'] }} text-{{ $kpi['color'] }} fa-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-muted small mb-0">{{ $kpi['label'] }}</p>
                            <h5 class="mb-0 text-truncate">{{ $kpi['value'] }}</h5>
                            <small class="text-muted">{{ $kpi['sub'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Quick Access Nav ────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @php
            $navItems = [
                ['label' => 'Salary Components', 'icon' => 'fa-sliders', 'color' => 'purple', 'route' => 'payroll.components',          'count' => $counts['salary_components']],
                ['label' => 'Pay Periods',        'icon' => 'fa-calendar','color' => 'teal',   'route' => 'payroll.pay-periods',         'count' => $counts['pay_periods']],
                ['label' => 'Emp. Components',    'icon' => 'fa-user-gear','color' => 'blue',  'route' => 'payroll.employee-components', 'count' => $counts['employee_components']],
                ['label' => 'Payroll Entries',    'icon' => 'fa-list',    'color' => 'green',  'route' => 'payroll.entries',             'count' => $counts['locked_periods'] . ' locked'],
            ];
            $colorMap = [
                'purple' => ['bg' => '#EEEDFE', 'text' => '#3C3489'],
                'teal'   => ['bg' => '#E1F5EE', 'text' => '#0F6E56'],
                'blue'   => ['bg' => '#E6F1FB', 'text' => '#0C447C'],
                'green'  => ['bg' => '#E4F3E9', 'text' => '#1B6B35'],
            ];
        @endphp

        @foreach ($navItems as $nav)
            @php $c = $colorMap[$nav['color']]; @endphp
            <div class="col-md-3 col-sm-6">
                <a href="{{ route($nav['route']) }}"
                    class="card border-0 shadow-sm h-100 text-decoration-none"
                    style="transition: transform .15s;"
                    onmouseover="this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.transform='translateY(0)'">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded p-3 flex-shrink-0"
                            style="background:{{ $c['bg'] }};">
                            <i class="fa {{ $nav['icon'] }} fa-lg"
                                style="color:{{ $c['text'] }};"></i>
                        </div>
                        <div>
                            <p class="mb-0 fw-medium text-dark">{{ $nav['label'] }}</p>
                            <small style="color:{{ $c['text'] }};">
                                {{ $nav['count'] }}
                            </small>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4">

        {{-- ── Trend Chart (last 6 months) ────────────────────────────────── --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Net Pay Trend — Last 6 Months</h6>
                    <small class="text-muted">TZS</small>
                </div>
                <div class="card-body">
                    @if ($trend->count())
                        <canvas id="payrollTrendChart" height="90"></canvas>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fa fa-chart-line fa-2x mb-2 d-block"></i>
                            No data for the last 6 months.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Period Status Breakdown ──────────────────────────────────────── --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">Pay Periods by Status</h6>
                </div>
                <div class="card-body">
                    @php
                        $statusMeta = [
                            'Draft'            => ['color' => 'secondary', 'icon' => 'fa-circle'],
                            'Processing'       => ['color' => 'warning',   'icon' => 'fa-spinner'],
                            'Locked_Completed' => ['color' => 'success',   'icon' => 'fa-lock'],
                        ];
                        $total = $periodsByStatus->sum();
                    @endphp

                    @foreach ($statusMeta as $status => $meta)
                        @php
                            $count = $periodsByStatus->get($status, 0);
                            $pct   = $total > 0 ? round(($count / $total) * 100) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small d-flex align-items-center gap-1">
                                    <i class="fa {{ $meta['icon'] }} text-{{ $meta['color'] }} fa-xs"></i>
                                    {{ str_replace('_', ' ', $status) }}
                                </span>
                                <span class="small fw-medium">{{ $count }}</span>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-{{ $meta['color'] }}"
                                    style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach

                    <hr class="my-3">

                    {{-- All-time totals --}}
                    <p class="small text-muted fw-medium mb-2">All-time Totals</p>
                    @foreach ([
                        ['Gross',      $allTime->gross,       'info'],
                        ['Deductions', $allTime->deductions,  'danger'],
                        ['Net Pay',    $allTime->net,         'success'],
                    ] as [$label, $val, $color])
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">{{ $label }}</small>
                            <small class="fw-medium text-{{ $color }}">
                                TZS {{ number_format($val ?? 0, 0) }}
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Recent Pay Periods ───────────────────────────────────────────── --}}
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between">
                    <h6 class="mb-0">Recent Pay Periods</h6>
                    <a href="{{ route('payroll.pay-periods') }}"
                        class="btn btn-sm btn-link p-0 text-decoration-none">View all</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Period</th>
                                    <th>Status</th>
                                    <th class="text-end">Employees</th>
                                    <th class="text-end">Net Pay</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentPeriods as $period)
                                    <tr>
                                        <td class="small">
                                            {{ $period->start_date->format('d M') }}
                                            –
                                            {{ $period->end_date->format('d M Y') }}
                                        </td>
                                        <td>
                                            @php
                                                $sc = match($period->status) {
                                                    'Draft'            => 'secondary',
                                                    'Processing'       => 'warning',
                                                    'Locked_Completed' => 'success',
                                                    default            => 'light',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $sc }}-subtle text-{{ $sc }} small">
                                                {{ str_replace('_', ' ', $period->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end small">
                                            {{ $period->entries_count }}
                                        </td>
                                        <td class="text-end small fw-medium text-success">
                                            {{ $period->total_net ? 'TZS ' . number_format($period->total_net, 0) : '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            No periods yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end row --}}
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('livewire:initialized', () => {
    const ctx = document.getElementById('payrollTrendChart');
    if (!ctx) return;

    const labels = @json($trend->map(fn($t) => \Carbon\Carbon::parse($t->start_date)->format('M Y')));
    const gross  = @json($trend->map(fn($t) => (float) $t->total_gross));
    const deductions = @json($trend->map(fn($t) => (float) $t->total_deductions));
    const net    = @json($trend->map(fn($t) => (float) $t->total_net));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Gross',
                    data: gross,
                    backgroundColor: 'rgba(13,110,253,0.15)',
                    borderColor: 'rgba(13,110,253,0.8)',
                    borderWidth: 1.5,
                    borderRadius: 4,
                },
                {
                    label: 'Deductions',
                    data: deductions,
                    backgroundColor: 'rgba(220,53,69,0.12)',
                    borderColor: 'rgba(220,53,69,0.7)',
                    borderWidth: 1.5,
                    borderRadius: 4,
                },
                {
                    label: 'Net Pay',
                    data: net,
                    type: 'line',
                    borderColor: 'rgba(25,135,84,0.9)',
                    backgroundColor: 'rgba(25,135,84,0.08)',
                    pointBackgroundColor: 'rgba(25,135,84,1)',
                    tension: 0.35,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 4,
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => 'TZS ' + Number(ctx.raw).toLocaleString()
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'TZS ' + Number(v).toLocaleString(),
                        font: { size: 11 },
                    },
                    grid: { color: 'rgba(0,0,0,.04)' },
                },
                x: { grid: { display: false } },
            },
        },
    });
});
</script>
@endpush