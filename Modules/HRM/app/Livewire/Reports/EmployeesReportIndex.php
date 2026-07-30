<?php

namespace Modules\HRM\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\Division;
use Modules\HRM\Models\Unit;
use Modules\HRM\Exports\EmployeeReport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeesReportIndex extends Component
{
    use WithPagination;

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $filterGender   = '';
    public string $filterDept     = '';
    public string $filterDivision = '';
    public string $filterUnit     = '';
    public string $filterStatus   = 'active';
    public string $search         = '';

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
        // Divisions are scoped to a department, so drop a stale selection
        $this->reset('filterDivision');
        $this->resetPage();
    }
    public function updatingFilterDivision(): void
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
            'filterDivision',
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
            'division_id'   => $this->filterDivision,
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
        $query = Employee::query()
            ->with(['user', 'division.department', 'unit'])
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
                $this->filterDivision,
                // A specific division is more precise than a department filter
                fn($q) =>
                $q->where('division_id', $this->filterDivision)
            )
            ->when(
                $this->filterDept && !$this->filterDivision,
                fn($q) =>
                $q->whereHas(
                    'division',
                    fn($d) =>
                    $d->where('department_id', $this->filterDept)
                )
            )
            ->when(
                $this->filterUnit,
                fn($q) =>
                $q->where('unit_id', $this->filterUnit)
            );

        // Sorting: department_id / division_id no longer live on employees directly
        if ($this->sortField === 'department_id') {
            $query->join('divisions', 'employees.division_id', '=', 'divisions.id')
                ->join('departments', 'divisions.department_id', '=', 'departments.id')
                ->orderBy('departments.name', $this->sortDir)
                ->select('employees.*');
        } elseif ($this->sortField === 'division_id') {
            $query->join('divisions', 'employees.division_id', '=', 'divisions.id')
                ->orderBy('divisions.name', $this->sortDir)
                ->select('employees.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDir);
        }

        $employees = $query->paginate($this->perPage);

        // Summary counts (unfiltered)
        $counts = [
            'total'  => Employee::where('is_active', 'active')->count(),
            'male'   => Employee::where('is_active', 'active')->where('gender', 'male')->count(),
            'female' => Employee::where('is_active', 'active')->where('gender', 'female')->count(),
            'depts'  => Employee::where('employees.is_active', 'active')
                ->join('divisions', 'employees.division_id', '=', 'divisions.id')
                ->distinct('divisions.department_id')
                ->count('divisions.department_id'),
        ];

        // Divisions scoped to the selected department (or all, if none selected)
        $divisions = Division::when(
            $this->filterDept,
            fn($q) => $q->where('department_id', $this->filterDept)
        )->orderBy('name')->get();

        // Units — kept as in the original (filtered only by exact selected id)
        $units = Unit::when($this->filterUnit, function ($q) {
            $q->where('id', $this->filterUnit);
        })->orderBy('name')->get();

        return view('hrm::livewire.reports.employees-report-index', [
            'employees'   => $employees,
            'departments' => Department::orderBy('name')->get(),
            'divisions'   => $divisions,
            'units'       => $units,
            'counts'      => $counts,
        ]);
    }
}
