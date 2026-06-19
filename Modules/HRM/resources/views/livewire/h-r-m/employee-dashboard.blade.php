<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">HR Dashboard</h4>
            <small class="text-muted">
                Employee overview as of {{ now()->format('d M Y') }}
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('hrm.employees.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-users me-1"></i> All Employees
            </a>
            <a href="{{ route('hrm.employees.create') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-plus me-1"></i> Add Employee
            </a>
        </div>
    </div>

    {{-- ── KPI Cards ────────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Total Active --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary-subtle p-3 flex-shrink-0">
                        <i class="fa fa-users text-primary fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Total Active Staff</p>
                        <h3 class="mb-0 fw-bold">{{ number_format($totalActive) }}</h3>
                        <small class="text-muted">
                            {{ $totalInactive }} inactive
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent pt-0 pb-2 border-0">
                    <div class="progress" style="height:4px;">
                        <div class="progress-bar bg-primary"
                            style="width:{{ $totalAll > 0 ? round(($totalActive / $totalAll) * 100) : 0 }}%">
                        </div>
                    </div>
                    <small class="text-muted">
                        {{ $totalAll > 0 ? round(($totalActive / $totalAll) * 100) : 0 }}% of total workforce
                    </small>
                </div>
            </div>
        </div>

        {{-- Gender Ratio --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info-subtle p-3 flex-shrink-0">
                        <i class="fa fa-venus-mars text-info fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Gender Ratio (M : F)</p>
                        <h3 class="mb-0 fw-bold">
                            {{ $genderRatio['male'] ?? 0 }}
                            <span class="text-muted fs-5">:</span>
                            {{ $genderRatio['female'] ?? 0 }}
                        </h3>
                        <small class="text-muted">
                            @php
                                $maleTotal = $genderRatio['male'] ?? 0;
                                $femaleTotal = $genderRatio['female'] ?? 0;
                                $genderTotal = $maleTotal + $femaleTotal;
                            @endphp
                            {{ $genderTotal > 0 ? round(($femaleTotal / $genderTotal) * 100) : 0 }}% female
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent pt-0 pb-2 border-0">
                    <div class="progress" style="height:4px;">
                        @php $malePct = $genderTotal > 0 ? round(($maleTotal / $genderTotal) * 100) : 50; @endphp
                        <div class="progress-bar bg-info" style="width:{{ $malePct }}%"></div>
                        <div class="progress-bar bg-warning" style="width:{{ 100 - $malePct }}%"></div>
                    </div>
                    <small class="text-muted">
                        <span class="text-info">■</span> Male &nbsp;
                        <span class="text-warning">■</span> Female
                    </small>
                </div>
            </div>
        </div>

        {{-- Retiring Soon --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle {{ $retiringWithin6Months > 0
    ? 'bg-danger-subtle' : 'bg-warning-subtle' }} p-3 flex-shrink-0">
                        <i class="fa fa-clock {{ $retiringWithin6Months > 0
    ? 'text-danger' : 'text-warning' }} fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Retiring Soon</p>
                        <h3 class="mb-0 fw-bold {{ $retiringWithin6Months > 0 ? 'text-danger' : '' }}">
                            {{ $retiringWithin6Months }}
                        </h3>
                        <small class="text-muted">
                            within 6 months &bull; {{ $retiringWithin1Year }} within 1 year
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent pt-0 pb-2 border-0">
                    @if ($retiringWithin6Months > 0)
                        <small class="text-danger">
                            <i class="fa fa-triangle-exclamation me-1"></i>
                            Immediate action required
                        </small>
                    @else
                        <small class="text-success">
                            <i class="fa fa-check-circle me-1"></i>
                            No urgent retirements
                        </small>
                    @endif
                </div>
            </div>
        </div>

        {{-- HR Registration --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success-subtle p-3 flex-shrink-0">
                        <i class="fa fa-id-card text-success fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">HR Registered</p>
                        <h3 class="mb-0 fw-bold">{{ number_format($hrRegistered) }}</h3>
                        <small class="{{ $hrUnregistered > 0 ? 'text-danger' : 'text-muted' }}">
                            {{ $hrUnregistered }} pending registration
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent pt-0 pb-2 border-0">
                    <div class="progress" style="height:4px;">
                        <div class="progress-bar bg-success"
                            style="width:{{ $totalAll > 0 ? round(($hrRegistered / $totalAll) * 100) : 0 }}%">
                        </div>
                    </div>
                    <small class="text-muted">
                        {{ $totalAll > 0 ? round(($hrRegistered / $totalAll) * 100) : 0 }}% fully onboarded
                    </small>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Row 2 ────────────────────────────────────────────────────────────── --}}
    <div class="row g-4 mb-4">

        {{-- Department Breakdown --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between">
                    <h6 class="mb-0">Staff by Department</h6>
                    <a href="{{ route('hrm.employees.index') }}"
                        class="btn btn-sm btn-link p-0 text-decoration-none small">
                        View all
                    </a>
                </div>
                <div class="card-body">
                    @php $maxDept = $byDepartment->max('active_count') ?: 1; @endphp
                    @forelse ($byDepartment as $dept)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-medium">{{ $dept->name }}</span>
                                <span class="small text-muted">
                                    {{ $dept->active_count }}
                                    <span class="text-muted opacity-50">
                                        ({{ round(($dept->active_count / $totalActive) * 100) }}%)
                                    </span>
                                </span>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-primary" style="width:{{ round(($dept->active_count / $maxDept) * 100) }}%;
                                               border-radius:4px;">
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small text-center py-3">No department data.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Education Breakdown --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">Education Levels</h6>
                </div>
                <div class="card-body">
                    @php
                        $eduColors = [
                            'phd' => 'purple',
                            'master' => 'primary',
                            'bachelor' => 'info',
                            'advance_diploma' => 'teal',
                            'diploma' => 'success',
                            'form-iv' => 'warning',
                            'certificate' => 'secondary',
                        ];
                    @endphp
                    @forelse ($byEducation as $edu)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle d-inline-block"
                                    style="width:8px;height:8px;background:var(--bs-{{ $eduColors[$edu->education] ?? 'secondary' }});">
                                </span>
                                <span class="small">
                                    {{ ucfirst(str_replace('_', ' ', $edu->education)) }}
                                </span>
                            </div>
                            <div class="text-end">
                                <span class="small fw-medium">{{ $edu->total }}</span>
                                <span class="text-muted small ms-1">
                                    ({{ $totalActive > 0 ? round(($edu->total / $totalActive) * 100) : 0 }}%)
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small text-center py-3">No data.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Disability & Quick Stats --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">Disability Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">With disability</span>
                        <span class="small fw-medium text-danger">{{ $withDisability }}</span>
                    </div>
                    <div class="progress mb-3" style="height:8px;">
                        <div class="progress-bar bg-danger"
                            style="width:{{ $totalActive > 0 ? round(($withDisability / $totalActive) * 100) : 0 }}%">
                        </div>
                        <div class="progress-bar bg-success-subtle"
                            style="width:{{ $totalActive > 0 ? round(($withoutDisability / $totalActive) * 100) : 0 }}%">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">
                            <span class="text-danger">■</span>
                            Disability: {{ $withDisability }}
                        </small>
                        <small class="text-muted">
                            <span class="text-success">■</span>
                            None: {{ $withoutDisability }}
                        </small>
                    </div>
                </div>
            </div>

            {{-- New Hires card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success-subtle p-3 flex-shrink-0">
                        <i class="fa fa-user-plus text-success fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">New Hires (30 days)</p>
                        <h3 class="mb-0 fw-bold text-success">
                            {{ $recentHiresCount }}
                        </h3>
                        <small class="text-muted">employees onboarded</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Row 3: Hire Trend + Tables ──────────────────────────────────────── --}}
    <div class="row g-4">

        {{-- Hire Trend Chart --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between">
                    <h6 class="mb-0">Monthly Hiring Trend</h6>
                    <small class="text-muted">Last 6 months</small>
                </div>
                <div class="card-body">
                    @if ($hireTrend->count())
                        <canvas id="hireTrendChart" height="120"></canvas>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fa fa-chart-bar fa-2x d-block mb-2 opacity-25"></i>
                            No hiring data available.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Upcoming Retirements --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between">
                    <h6 class="mb-0">Upcoming Retirements</h6>
                    <span class="badge bg-warning-subtle text-warning">
                        Within 1 year
                    </span>
                </div>
                <div class="card-body p-0">
                    @forelse ($upcomingRetirements as $emp)
                        @php
                            $daysLeft = now()->diffInDays($emp->retiring_date, false);
                            $urgency = $daysLeft <= 90
                                ? ['danger', 'Retiring in ' . $daysLeft . ' days']
                                : ['warning', 'Retiring in ' . round($daysLeft / 30) . ' months'];
                        @endphp
                        <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                            <div class="rounded-circle bg-{{ $urgency[0] }}-subtle d-flex
                                    align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;font-size:11px;font-weight:600;
                                           color:var(--bs-{{ $urgency[0] }});">
                                {{ strtoupper(substr($emp->user?->first_name ?? '?', 0, 1)) }}{{ strtoupper(substr($emp->user?->last_name ?? '', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-medium small text-truncate">
                                    {{ $emp->user?->first_name }}
                                    {{ $emp->user?->last_name }}
                                </div>
                                <small class="text-muted">
                                    {{ $emp->department?->name ?? '—' }}
                                </small>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="badge bg-{{ $urgency[0] }}-subtle text-{{ $urgency[0] }}">
                                    {{ $urgency[1] }}
                                </span>
                                <div class="text-muted" style="font-size:10px;">
                                    {{ \Carbon\Carbon::parse($emp->retiring_date)->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fa fa-check-circle fa-2x d-block mb-2 text-success opacity-50"></i>
                            No upcoming retirements within a year.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Hires --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between">
                    <h6 class="mb-0">
                        Recent Hires
                        <span class="badge bg-success-subtle text-success ms-1">
                            Last 30 days
                        </span>
                    </h6>
                    <a href="{{ route('hrm.employees.index') }}"
                        class="btn btn-sm btn-link p-0 text-decoration-none small">
                        View all
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size:11px;padding:10px 16px;">Employee</th>
                                    <th style="font-size:11px;padding:10px 16px;">Department</th>
                                    <th style="font-size:11px;padding:10px 16px;">Gender</th>
                                    <th style="font-size:11px;padding:10px 16px;">Hired</th>
                                    <th style="font-size:11px;padding:10px 16px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentHires as $hire)
                                                            <tr>
                                                                <td style="padding:10px 16px;">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <div class="rounded-circle bg-success-subtle d-flex
                                                                                align-items-center justify-content-center" style="width:30px;height:30px;font-size:11px;
                                                                                       font-weight:600;color:#1B5E20;">
                                                                            {{ strtoupper(substr($hire->user?->first_name ?? '?', 0, 1)) }}{{ strtoupper(substr($hire->user?->last_name ?? '', 0, 1)) }}
                                                                        </div>
                                                                        <div>
                                                                            <div class="fw-medium" style="font-size:13px;">
                                                                                {{ $hire->user?->first_name }}
                                                                                {{ $hire->user?->last_name }}
                                                                            </div>
                                                                            <div class="text-muted" style="font-size:11px;">
                                                                                {{ $hire->user?->email }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td style="padding:10px 16px;">
                                                                    @if ($hire->department)
                                                                        <span class="badge bg-info-subtle text-info">
                                                                            {{ $hire->department->name }}
                                                                        </span>
                                                                    @else
                                                                        <span class="text-muted small">—</span>
                                                                    @endif
                                                                </td>
                                                                <td style="padding:10px 16px;">
                                                                    <span class="badge {{ $hire->gender === 'male'
                                    ? 'bg-info-subtle text-info'
                                    : 'bg-warning-subtle text-warning' }}">
                                                                        {{ ucfirst($hire->gender) }}
                                                                    </span>
                                                                </td>
                                                                <td style="padding:10px 16px;" class="small">
                                                                    {{ \Carbon\Carbon::parse($hire->hired_date)->format('d M Y') }}
                                                                    <div class="text-muted" style="font-size:11px;">
                                                                        {{ \Carbon\Carbon::parse($hire->hired_date)->diffForHumans() }}
                                                                    </div>
                                                                </td>
                                                                <td style="padding:10px 16px;">
                                                                    <span class="badge bg-success-subtle text-success">
                                                                        Active
                                                                    </span>
                                                                </td>
                                                            </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No new hires in the last 30 days.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const ctx = document.getElementById('hireTrendChart');
            if (!ctx) return;

            const labels = @json($hireTrend->pluck('month'));
            const data = @json($hireTrend->pluck('total'));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'New Hires',
                        data,
                        backgroundColor: 'rgba(25,135,84,0.15)',
                        borderColor: 'rgba(25,135,84,0.9)',
                        borderWidth: 1.5,
                        borderRadius: 4,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.raw + ' hire(s)'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
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