<?php 
namespace Modules\HRM\Exports ;

use Modules\HRM\Models\Employee;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeReport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Employee::query()->with(['department', 'unit']);

        // Apply Filters based on your migration fields
        if ($this->filters['department_id']) {
            $query->where('department_id', $this->filters['department_id']);
        }
        if ($this->filters['gender']) {
            $query->where('gender', $this->filters['gender']);
        }
        if ($this->filters['is_active']) {
            $query->where('is_active', $this->filters['is_active']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Gender',
            'Designation',
            'Department',
            'Unit',
            'NIDA No',
            'Hired Date',
            'Status'
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->first_name . ' ' . $employee->last_name, // Assuming these exist
            ucfirst($employee->gender),
            $employee->designation,
            $employee->department->name ?? 'N/A',
            $employee->unit->name ?? 'N/A',
            $employee->nida_no,
            $employee->hired_date,
            $employee->is_active,
        ];
    }
}
