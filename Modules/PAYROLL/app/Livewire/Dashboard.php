<?php

namespace Modules\PAYROLL\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\HRM\Models\Employee;
use Modules\PAYROLL\Models\EmployeeComponent;
use Modules\PAYROLL\Models\PayPeriod;
use Modules\PAYROLL\Models\PayrollEntry;
use Modules\PAYROLL\Models\SalaryComponent;

class Dashboard extends Component
{
    public function mount()
    {
        session(['module' => 'PAYROLL']);
    }

    public function render()
    {
        // ── Current / Latest pay period ───────────────────────────────────────
        $latestPeriod = PayPeriod::latest('start_date')->first();

        // ── Period stats ──────────────────────────────────────────────────────
        $periodStats = null;
        if ($latestPeriod) {
            $periodStats = PayrollEntry::where('pay_period_id', $latestPeriod->id)
                ->selectRaw('
                    COUNT(*)            AS employee_count,
                    SUM(total_gross)    AS total_gross,
                    SUM(total_deductions) AS total_deductions,
                    SUM(net_pay)        AS total_net
                ')->first();
        }

        // ── All-time summary ──────────────────────────────────────────────────
        $allTime = PayrollEntry::selectRaw('
            SUM(total_gross)        AS gross,
            SUM(total_deductions)   AS deductions,
            SUM(net_pay)            AS net
        ')->first();

        // ── Period status breakdown ───────────────────────────────────────────
        $periodsByStatus = PayPeriod::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // ── Recent 5 pay periods with their totals ────────────────────────────
        $recentPeriods = PayPeriod::withCount('entries')
            ->withSum('entries as total_net', 'net_pay')
            ->latest('start_date')
            ->take(5)
            ->get();

        // ── Component type breakdown (Earnings vs Deductions) ─────────────────
        $componentBreakdown = SalaryComponent::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        // ── Top 5 highest net pay in latest period ────────────────────────────
        $topEarners = PayrollEntry::with('employee.user')
            ->when($latestPeriod, fn($q) => $q->where('pay_period_id', $latestPeriod->id))
            ->orderByDesc('net_pay')
            ->take(5)
            ->get();

        // ── Monthly net pay trend (last 6 months) ─────────────────────────────
        $trend = PayPeriod::select(
            'pay_periods.id',
            'pay_periods.start_date',
            DB::raw('COALESCE(SUM(pe.net_pay), 0) as total_net'),
            DB::raw('COALESCE(SUM(pe.total_gross), 0) as total_gross'),
            DB::raw('COALESCE(SUM(pe.total_deductions), 0) as total_deductions')
        )
            ->leftJoin('payroll_entries as pe', 'pe.pay_period_id', '=', 'pay_periods.id')
            ->where('pay_periods.start_date', '>=', now()->subMonths(6))
            ->groupBy('pay_periods.id', 'pay_periods.start_date')
            ->orderBy('pay_periods.start_date')
            ->get();

        // ── Quick counts ──────────────────────────────────────────────────────
        $counts = [
            'employees'           => Employee::where('is_active', 'active')->count(),
            'salary_components'   => SalaryComponent::count(),
            'pay_periods'         => PayPeriod::count(),
            'locked_periods'      => PayPeriod::where('status', 'Locked_Completed')->count(),
            'draft_periods'       => PayPeriod::where('status', 'Draft')->count(),
            'processing_periods'  => PayPeriod::where('status', 'Processing')->count(),
            'employee_components' => EmployeeComponent::active()->count(),
        ];

        return view('payroll::livewire.dashboard', compact(
            'latestPeriod',
            'periodStats',
            'allTime',
            'periodsByStatus',
            'recentPeriods',
            'componentBreakdown',
            'topEarners',
            'trend',
            'counts',
        ));
    }
}
