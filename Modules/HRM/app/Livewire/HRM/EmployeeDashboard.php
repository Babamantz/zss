<?php

namespace Modules\HRM\Livewire\HRM;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\Unit;

class EmployeeDashboard extends Component
{
    public function mount(): void
    {
        session(['module' => 'HRM']);
    }

    public function render()
    {
        // ── Core counts ───────────────────────────────────────────────────────
        $totalActive   = Employee::where('is_active', 'active')->count();
        $totalInactive = Employee::where('is_active', 'in-active')->count();
        $totalAll      = Employee::count();

        // ── Gender breakdown ──────────────────────────────────────────────────
        $genderRatio = Employee::where('is_active', 'active')
            ->select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->pluck('total', 'gender');

        // ── Retirements ───────────────────────────────────────────────────────
        $retiringWithin6Months = Employee::where('is_active', 'active')
            ->whereBetween('retiring_date', [now(), now()->addMonths(6)])
            ->count();

        $retiringWithin1Year = Employee::where('is_active', 'active')
            ->whereBetween('retiring_date', [now(), now()->addYear()])
            ->count();

        $upcomingRetirements = Employee::with(['user', 'department'])
            ->where('is_active', 'active')
            ->whereBetween('retiring_date', [now(), now()->addYear()])
            ->orderBy('retiring_date')
            ->take(5)
            ->get();

        // ── HR Registration ───────────────────────────────────────────────────
        $hrRegistered   = Employee::where('is_hr_registered', true)->count();
        $hrUnregistered = $totalAll - $hrRegistered;

        // ── Department breakdown ──────────────────────────────────────────────
        $byDepartment = Department::withCount([
            'employees as active_count' => fn($q) =>
            $q->where('is_active', 'active'),
        ])
            ->having('active_count', '>', 0)
            ->orderByDesc('active_count')
            ->take(6)
            ->get();

        // ── Education breakdown ───────────────────────────────────────────────
        $byEducation = Employee::where('is_active', 'active')
            ->select('education', DB::raw('count(*) as total'))
            ->groupBy('education')
            ->orderByDesc('total')
            ->get();

        // ── Disability ────────────────────────────────────────────────────────
        $withDisability    = Employee::where('is_active', 'active')
            ->where('is_disable', true)->count();
        $withoutDisability = $totalActive - $withDisability;

        // ── Recent hires (last 30 days) ───────────────────────────────────────
        $recentHires = Employee::with(['user', 'department'])
            ->where('is_active', 'active')
            ->where('hired_date', '>=', now()->subDays(30))
            ->orderByDesc('hired_date')
            ->take(5)
            ->get();

        $recentHiresCount = Employee::where('is_active', 'active')
            ->where('hired_date', '>=', now()->subDays(30))
            ->count();

        // ── Monthly hires trend (last 6 months) ───────────────────────────────
        $hireTrend = Employee::select(
            DB::raw("DATE_FORMAT(hired_date, '%b %Y') as month"),
            DB::raw("DATE_FORMAT(hired_date, '%Y-%m') as month_sort"),
            DB::raw('count(*) as total')
        )
            ->where('hired_date', '>=', now()->subMonths(6))
            ->groupBy('month', 'month_sort')
            ->orderBy('month_sort')
            ->get();

        return view('hrm::livewire.h-r-m.employee-dashboard', compact(
            'totalActive',
            'totalInactive',
            'totalAll',
            'genderRatio',
            'retiringWithin6Months',
            'retiringWithin1Year',
            'upcomingRetirements',
            'hrRegistered',
            'hrUnregistered',
            'byDepartment',
            'byEducation',
            'withDisability',
            'withoutDisability',
            'recentHires',
            'recentHiresCount',
            'hireTrend',
        ));
    }
}
