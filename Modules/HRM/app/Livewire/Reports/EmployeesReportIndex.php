<?php

namespace Modules\HRM\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\Unit;
use Modules\HRM\Exports\EmployeeReport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeesReportIndex extends Component
{
    use WithPagination;

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $filterGender = '';
    public string $filterDept   = '';
    public string $filterUnit   = '';
    public string $filterStatus = 'active';
    public string $search       = '';

    // ── Sorting ───────────────────────────────────────────────────────────────
    public string $sortField = 'hired_date';
    public string $sortDir   = 'desc';

    // ── Per page ──────────────────────────────────────────────────────────────
    public int $perPage = 15;

    // ── Reset pagination on filter change ─────────────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterGender(): void
    {
        $this->resetPage();
    }
    public function updatingFilterDept(): void
    {
        $this->resetPage();
    }
    public function updatingFilterUnit(): void
    {
        $this->resetPage();
    }
    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    // ── Sorting ───────────────────────────────────────────────────────────────
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir   = 'asc';
        }
        $this->resetPage();
    }

    // ── Clear all filters ─────────────────────────────────────────────────────
    public function clearFilters(): void
    {
        $this->reset([
            'filterGender',
            'filterDept',
            'filterUnit',
            'search',
        ]);
        $this->filterStatus = 'active';
        $this->sortField    = 'hired_date';
        $this->sortDir      = 'desc';
        $this->resetPage();
    }

    // ── Excel download (respects current filters) ─────────────────────────────
    public function downloadReport()
    {
        $filters = [
            'gender'        => $this->filterGender,
            'department_id' => $this->filterDept,
            'unit_id'       => $this->filterUnit,
            'is_active'     => $this->filterStatus,
            'search'        => $this->search,
        ];

        return Excel::download(
            new EmployeeReport($filters),
            'Employee_Report_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    // ── Render ────────────────────────────────────────────────────────────────
    public function render()
    {
        $employees = Employee::query()
            ->with(['user', 'department', 'unit'])
            ->where('is_active', $this->filterStatus ?: 'active')
            ->when(
                $this->search,
                fn($q) =>
                $q->whereHas(
                    'user',
                    fn($u) =>
                    $u->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name',  'like', "%{$this->search}%")
                )
            )
            ->when(
                $this->filterGender,
                fn($q) =>
                $q->where('gender', $this->filterGender)
            )
            ->when(
                $this->filterDept,
                fn($q) =>
                $q->where('department_id', $this->filterDept)
            )
            ->when(
                $this->filterUnit,
                fn($q) =>
                $q->where('unit_id', $this->filterUnit)
            )
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        // Summary counts (unfiltered)
        $counts = [
            'total'   => Employee::where('is_active', 'active')->count(),
            'male'    => Employee::where('is_active', 'active')->where('gender', 'male')->count(),
            'female'  => Employee::where('is_active', 'active')->where('gender', 'female')->count(),
            'depts'   => Employee::where('is_active', 'active')
                ->distinct('department_id')->count('department_id'),
        ];

        // Units filtered by selected department
        $units = Unit::when(
            $this->filterDept,
            fn($q) => $q->where('department_id', $this->filterDept)
        )->orderBy('name')->get();

        return view('hrm::livewire.reports.employees-report-index', [
            'employees'   => $employees,
            'departments' => Department::orderBy('name')->get(),
            'units'       => $units,
            'counts'      => $counts,
        ]);
    }
}
