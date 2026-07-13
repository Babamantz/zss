<header class="main-nav">
    {{-- <div class="sidebar-user text-center">
        <a class="setting-primary" href="javascript:void(0)"><i data-feather="settings"></i></a><img
            class="img-90 rounded-circle" src="{{ asset('assets/images/dashboard/1.png') }}" alt="" />
        <div class="badge-bottom"><span class="badge badge-primary">New</span></div>
        <a href="user-profile">
            <h6 class="mt-3 f-14 f-w-600">Emay Walter</h6>
        </a>
        <p class="mb-0 font-roboto">Human Resources Department</p>
        <ul>
            <li>
                <span><span class="counter">19.8</span>k</span>
                <p>Follow</p>
            </li>
            <li>
                <span>2 year</span>
                <p>Experince</p>
            </li>
            <li>
                <span><span class="counter">95.2</span>k</span>
                <p>Follower</p>
            </li>
        </ul>
    </div> --}}

    @php
        $authUser = auth()->user();
        $employee = $authUser?->employee;
        $hiredDate = $employee?->hired_date
            ? \Carbon\Carbon::parse($employee->hired_date)
            : null;
        $yearsExp = $hiredDate ? $hiredDate->diffInYears(now()) : null;
        $monthsExp = $hiredDate ? $hiredDate->diffInMonths(now()) : null;
        $expLabel = match (true) {
            $yearsExp >= 1 => $yearsExp . ' ' . Str::plural('yr', $yearsExp),
            $monthsExp >= 1 => $monthsExp . ' ' . Str::plural('mo', $monthsExp),
            default => 'New hire',
        };
        $initials = strtoupper(substr($authUser?->first_name ?? '?', 0, 1))
            . strtoupper(substr($authUser?->last_name ?? '', 0, 1));

        $isNewHire = $hiredDate && $hiredDate->diffInDays(now()) <= 90;
        $designation = $employee?->designation?->designation_name ?? null;
        $department = $employee?->department?->name ?? null;
        $unit = $employee?->unit?->name ?? null;

        // Colleagues in same department
        // $colleagues = $employee?->department_id
        //     ? \Modules\HRM\Models\Employee::where('department_id', $employee->department_id)
        //         ->where('id', '!=', $employee?->id)
        //         ->where('is_active', 'active')
        //         ->count()
        //     : 0;

        // Total active employees in organisation
        $orgTotal = \Modules\HRM\Models\Employee::where('is_active', 'active')->count();
    @endphp

    <div class="sidebar-user text-center">

        {{-- Settings icon --}}
        <a class="setting-primary" href="javascript:void(0)">
            <i data-feather="settings"></i>
        </a>

        {{-- Avatar — photo if exists, initials fallback --}}
        @if ($employee?->photo_file && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->photo_file))
            <img class="img-90 rounded-circle" style="object-fit:cover;width:90px;height:90px;"
                src="{{ Storage::disk('public')->url($employee->photo_file) }}" alt="{{ $authUser?->first_name }}">
        @else
            <div class="img-90 rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width:90px;height:90px;background:#E8EAF6;
                                   font-size:28px;font-weight:700;color:#1a237e;">
                {{ $initials }}
            </div>
        @endif

        {{-- Badge --}}
        <div class="badge-bottom">
            @if ($isNewHire)
                <span class="badge badge-success">New</span>
            @elseif ($employee?->is_officer)
                <span class="badge badge-primary">Officer</span>
            @else
                <span class="badge badge-secondary">Staff</span>
            @endif
        </div>

        {{-- Name --}}
        <a href="{{ route('profile', $employee?->id ?? '#') }}">
            <h6 class="mt-3 f-14 f-w-600">
                {{ $authUser?->first_name }}
                {{ $authUser?->last_name }}
            </h6>
        </a>

        {{-- Designation --}}
        @if ($designation)
            <p class="mb-0 font-roboto text-primary" style="font-size:12px;font-weight:600;">
                {{ $designation }}
            </p>
        @endif

        {{-- Department / Unit --}}
        <p class="mb-0 font-roboto" style="font-size:12px;">
            @if ($department)
                {{ $department }}
                @if ($unit)
                    <span class="text-muted">· {{ $unit }}</span>
                @endif
            @else
                <span class="text-muted">No department assigned</span>
            @endif
        </p>

        {{-- OPF number --}}
        @if ($employee?->opf_number)
            <p class="mb-1 text-muted" style="font-size:11px;">
                OPF: {{ $employee->opf_number }}
            </p>
        @endif



    </div>
    <nav>
        @if (session('module') === 'general')
            <div class="main-navbar">
                <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
                <div id="mainnav">
                    <ul class="nav-menu custom-scrollbar">
                        <li class="back-btn">
                            <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                                    aria-hidden="true"></i></div>
                        </li>
                        <li class="sidebar-main-title">
                            <div>
                                <h6>General</h6>
                            </div>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/index') }}" href="{{ route('index') }}"><i
                                    data-feather="home"></i><span>Dashboard</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/index') }}" href="{{ route('profile') }}"><i
                                    data-feather="user"></i><span>Profile</span>
                            </a>
                        </li>


                        <li class="dropdown">
                            <a class="nav-link  {{ prefixActive('/index') }}" href="{{ route('documents.index') }}"><i
                                    data-feather="file"></i><span>Documents</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link  {{ prefixActive('/index') }}" href="{{ route('attendance.index') }}"><i
                                    data-feather="users"></i><span>Attendace</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link menu-title {{ prefixActive('/index') }}" href="javascript:void(0)"><i
                                    data-feather="users"></i><span>Management</span></a>
                            <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/index') }};">
                                <li><a href="{{ route('users.index') }}" class="{{ routeActive('users.index') }}">Users</a>
                                </li>
                                <li><a href="{{ route('password.reset') }}"
                                        class="{{ routeActive('password.reset') }}">reset</a>
                                </li>
                                <li><a href="{{ route('roles.index') }}" class="{{ routeActive('roles.index') }}">Roles</a>
                                </li>
                            </ul>
                        </li>


                        <li class="dropdown">
                            <a class="nav-link menu-title {{ prefixActive('/index') }}" href="javascript:void(0)"><i
                                    data-feather="file"></i><span>Reports</span></a>
                            <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/index') }};">
                                <li><a href="{{ route('user.report.index') }}"
                                        class="{{ routeActive('user.report.index') }}">Users</a>
                                </li>
                            </ul>
                        </li>
                        {{-- <li class="dropdown">
                            <a class="nav-link menu-title {{ prefixActive('/transaction/create') }}"
                                href="javascript:void(0)"><i data-feather="file"></i><span>Transaction</span></a>
                            <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/index') }};">
                                <li><a href="{{ route('chain.transaction.create') }}"
                                        class="{{ routeActive('chain.transaction.create') }}">Transaction</a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link menu-title {{ prefixActive('/queue') }}" href="javascript:void(0)"><i
                                    data-feather="file"></i><span>Queue</span></a>
                            <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/index') }};">
                                <li><a href="{{ route('chain.queue') }}" class="{{ routeActive('chain.queue') }}">Queues</a>
                                </li>
                            </ul>
                        </li> --}}
                        {{-- <li class="dropdown">
                            <a class="nav-link menu-title {{ prefixActive('transaction/{transactionId}/timeline') }}"
                                href="javascript:void(0)"><i data-feather="file"></i><span>Transaction Timeline</span></a>
                            <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/index') }};">
                                <li><a href="{{ route('chain.transaction.timeline') }}"
                                        class="{{ routeActive('chain.transaction.timeline') }}">Users</a>
                                </li>
                            </ul>
                        </li> --}}
                    </ul>
                </div>
                <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </div>
        @endif

        {{-- HRM Module Start --}}
        @if (session('module') === 'HRM')
            <div class="main-navbar">
                <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
                <div id="mainnav">
                    <ul class="nav-menu custom-scrollbar">
                        <li class="back-btn">
                            <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                                    aria-hidden="true"></i></div>
                        </li>
                        <li class="sidebar-main-title">
                            <div>
                                <h6>HRM</h6>
                            </div>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/hrm/dashboard') }}"
                                href="{{ route('hrm.employees.dashboard') }}"><i
                                    data-feather="home"></i><span>Dashboard</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link menu-title {{ prefixActive('/index') }}" href="javascript:void(0)"><i
                                    data-feather="users"></i><span>Employees</span></a>
                            <ul class="nav-submenu menu-content"
                                style="display: {{ prefixBlock('/module/hrm/employees/index') }};">
                                <li><a href="{{ route('hrm.employees.index') }}"
                                        class="{{ routeActive('hrm.employees.index') }}">Employees</a>
                                </li>
                                <li><a href="{{ route('hrm.employee.report') }}" class="">Report</a>
                                </li>

                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </div>
        @endif
        {{-- Payroll Module Start --}}

        @if (session('module') === 'PAYROLL')
            <div class="main-navbar">
                <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
                <div id="mainnav">
                    <ul class="nav-menu custom-scrollbar">
                        <li class="back-btn">
                            <div class="mobile-back text-end">
                                <span>Back</span>
                                <i class="fa fa-angle-right ps-2" aria-hidden="true"></i>
                            </div>
                        </li>

                        {{-- ── Dashboard ────────────────────────────────────────────── --}}
                        <li class="sidebar-main-title">
                            <div>
                                <h6>Dashboard</h6>
                            </div>
                        </li>

                        <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/payroll/dashboard') }}"
                                href="{{ route('payroll.dashboard') }}">
                                <i data-feather="home"></i>
                                <span>Home</span>
                            </a>
                        </li>

                        {{-- ── Setup & Configuration ────────────────────────────────── --}}
                        <li class="sidebar-main-title">
                            <div>
                                <h6>Setup & Configuration</h6>
                            </div>
                        </li>

                        {{-- Salary Components --}}
                        <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/payroll/components') }}"
                                href="{{ route('payroll.components') }}">
                                <i data-feather="sliders"></i>
                                <span>Salary Components</span>
                            </a>
                        </li>

                        {{-- Pay Periods --}}
                        <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/payroll/pay-periods') }}"
                                href="{{ route('payroll.pay-periods') }}">
                                <i data-feather="calendar"></i>
                                <span>Pay Periods</span>
                            </a>
                        </li>

                        {{-- Employee Components --}}
                        <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/payroll/employee-components') }}"
                                href="{{ route('payroll.employee-components') }}">
                                <i data-feather="user-check"></i>
                                <span>Employee Components</span>
                            </a>
                        </li>

                        {{-- Finance Profiles --}}
                        <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/payroll/finance-profiles') }}"
                                href="{{ route('payroll.finance-profiles') }}">
                                <i data-feather="credit-card"></i>
                                <span>Finance Profiles</span>
                            </a>
                        </li>

                        {{-- ── Payroll Processing ───────────────────────────────────── --}}
                        <li class="sidebar-main-title">
                            <div>
                                <h6>Payroll Processing</h6>
                            </div>
                        </li>

                        {{-- Run Payroll --}}
                        <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/payroll/run') }}" href="{{ route('payroll.run') }}">
                                <i data-feather="play-circle"></i>
                                <span>Run Payroll</span>
                            </a>
                        </li>

                        {{-- Payroll Entries --}}
                        <li class="dropdown">
                            <a class="nav-link menu-title {{ prefixActive('/module/payroll/entries') }}"
                                href="javascript:void(0)">
                                <i data-feather="list"></i>
                                <span>Payroll Entries</span>
                            </a>


                            <ul class="nav-submenu menu-content"
                                style="display: {{ prefixBlock('/module/payroll/entries') }};">

                                <li>
                                    <a href="{{ route('payroll.entries') }}" class="{{ request()->routeIs('payroll.entries') && !request()->status
            ? 'active' : '' }}">
                                        All Entries
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('payroll.entries', ['status' => 'Draft']) }}"
                                        class="{{ request()->status === 'Draft' ? 'active' : '' }}">
                                        Draft
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('payroll.entries', ['status' => 'Processing']) }}"
                                        class="{{ request()->status === 'Processing' ? 'active' : '' }}">
                                        Processing
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('payroll.entries', ['status' => 'Locked_Completed']) }}"
                                        class="{{ request()->status === 'Locked_Completed' ? 'active' : '' }}">
                                        Locked / Completed
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- ── Reports & Audit ──────────────────────────────────────── --}}
                        <li class="sidebar-main-title">
                            <div>
                                <h6>Reports & Audit</h6>
                            </div>
                        </li>

                        {{-- Payroll Summary --}}
                        {{-- <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/payroll/reports/summary') }}"
                                href="{{ route('payroll.reports.summary') }}">
                                <i data-feather="bar-chart-2"></i>
                                <span>Payroll Summary</span>
                            </a>
                        </li> --}}

                        {{-- Payslips --}}
                        {{-- <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/payroll/reports/payslips') }}"
                                href="{{ route('payroll.reports.payslips') }}">
                                <i data-feather="file-text"></i>
                                <span>Payslips</span>
                            </a>
                        </li> --}}

                        {{-- Deductions Report --}}
                        <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/payroll/reports/deductions') }}"
                                href="{{ route('payroll.reports.deductions') }}">
                                <i data-feather="trending-down"></i>
                                <span>Deductions Report</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/payroll/reports/deductions') }}"
                                href="{{ route('payroll.reports.others') }}">
                                <i data-feather="trending-up"></i>
                                <span>Other Report</span>
                            </a>
                        </li>

                        {{-- Audit Trail --}}
                        {{-- <li class="dropdown">
                            <a class="nav-link {{ prefixActive('/module/payroll/reports/audit') }}"
                                href="{{ route('payroll.reports.audit') }}">
                                <i data-feather="shield"></i>
                                <span>Audit Trail</span>
                            </a>
                        </li> --}}

                    </ul>
                </div>
                <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </div>
        @endif

    </nav>
</header>