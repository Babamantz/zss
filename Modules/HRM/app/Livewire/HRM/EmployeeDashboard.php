<?php

namespace Modules\HRM\Livewire\HRM;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\Division;
use Modules\HRM\Models\Employee;
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
        $totalActive   = Employee::whereHas('user', fn($q) => $q->where('is_active', true))->count();
        $totalInactive = Employee::whereHas('user', fn($q) => $q->where('is_active', false))->count();
        $totalAll      = Employee::count();

        // ── Gender breakdown ──────────────────────────────────────────────────
        $genderRatio = Employee::whereHas('user', fn($q) => $q->where('is_active', true))
            ->select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->pluck('total', 'gender');

        // ── Retirements ───────────────────────────────────────────────────────
        $retiringWithin6Months = Employee::whereHas('user', fn($q) => $q->where('is_active', true))
            ->whereBetween('retiring_date', [now(), now()->addMonths(6)])
            ->count();

        $retiringWithin1Year = Employee::whereHas('user', fn($q) => $q->where('is_active', true))
            ->whereBetween('retiring_date', [now(), now()->addYear()])
            ->count();

        $upcomingRetirements = Employee::with(['user', 'division.department'])
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->whereBetween('retiring_date', [now(), now()->addYear()])
            ->orderBy('retiring_date')
            ->take(5)
            ->get();

        // ── HR Registration ───────────────────────────────────────────────────
        $hrRegistered   = Employee::where('is_hr_registered', true)->count();
        $hrUnregistered = $totalAll - $hrRegistered;

        // ── Department breakdown (via division) ────────────────────────────────
        $byDepartment = Employee::query()->with('education_levels')
            ->join('divisions', 'employees.division_id', '=', 'divisions.id')
            ->join('departments', 'divisions.department_id', '=', 'departments.id')
            // ->whereHas('user', fn ($q) => $q->where('is_active', 'active'))
            ->select('departments.id', 'departments.name', DB::raw('count(*) as active_count'))
            ->groupBy('departments.id', 'departments.name')
            ->having('active_count', '>', 0)
            ->orderByDesc('active_count')
            ->take(6)
            ->get();

        // ── Education breakdown ───────────────────────────────────────────────
        $byEducation = Employee::query()
            ->whereHas('user', fn($q) => $q->where('is_active', 'active'))
            ->with('employed_education_level:id,name')
            ->select('education_level_id', DB::raw('count(id) as total'))
            ->groupBy('education_level_id')
            ->orderByDesc('total')
            ->get();

        // ── Disability ────────────────────────────────────────────────────────
        $withDisability    = Employee::whereHas('user', fn($q) => $q->where('is_active', 'active'))
            ->where('is_disable', true)->count();
        $withoutDisability = $totalActive - $withDisability;

        // ── Recent hires (last 30 days) ─────────────────────────────────────��──
        $recentHires = Employee::with(['user', 'division.department'])
            ->whereHas('user', fn($q) => $q->where('is_active', 'active'))
            ->where('hired_date', '>=', now()->subDays(30))
            ->orderByDesc('hired_date')
            ->take(5)
            ->get();

        $recentHiresCount = Employee::whereHas('user', fn($q) => $q->where('is_active', 'active'))
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
