<?php

namespace Modules\HRM\Livewire\Reports;

use Livewire\Component;
use Modules\HRM\Models\Employee;
use Maatwebsite\Excel\Facades\Excel;
use Modules\HRM\Exports\EmployeeReport;
use Modules\HRM\Models\Department;

class EmployeesReportIndex extends Component
{

    public $filterGender = '';
    public $filterDept = '';
    public $filterUnit = '';
    public $filterStatus = 'active';

    public function downloadReport()
    {
        $filters = [
            'gender' => $this->filterGender,
            'department_id' => $this->filterDept,
            'is_active' => $this->filterStatus,
        ];

        return Excel::download(new EmployeeReport($filters), 'Employee_Report_' . now()->format('Y-m-d') . '.xlsx');
    }
    public function render()
    {
        $employees = Employee::query()
            ->when($this->filterGender, fn($q) => $q->where('gender', $this->filterGender))
            ->when($this->filterDept, fn($q) => $q->where('department_id', $this->filterDept))
            ->when($this->filterUnit, fn($q) => $q->where('unit_id', $this->filterUnit)) // Relates to your unit_id column
            ->where('is_active', $this->filterStatus)
            ->with(['user', 'department', 'unit']) // Add this so names and titles show up
            ->get();


        return view('hrm::livewire.reports.employees-report-index', [
            'employees' => $employees,
            'departments' => Department::all()
        ]);
    }
}
