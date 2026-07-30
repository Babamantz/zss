<?php

namespace Modules\HRM\Exports;

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
        $query = Employee::query()->with(['user', 'designation', 'division.department', 'unit']);

        if (!empty($this->filters['division_id'])) {
            $query->where('division_id', $this->filters['division_id']);
        } elseif (!empty($this->filters['department_id'])) {
            $query->whereHas('division', function ($q) {
                $q->where('department_id', $this->filters['department_id']);
            });
        }
        if (!empty($this->filters['unit_id'])) {
            $query->where('unit_id', $this->filters['unit_id']);
        }
        if (!empty($this->filters['gender'])) {
            $query->where('gender', $this->filters['gender']);
        }
        if (!empty($this->filters['is_active'])) {
            $query->where('is_active', $this->filters['is_active']);
        }
        if (!empty($this->filters['search'])) {
            $query->whereHas('user', function ($u) {
                $u->where('first_name', 'like', "%{$this->filters['search']}%")
                    ->orWhere('last_name', 'like', "%{$this->filters['search']}%");
            });
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
            'Division',
            'Unit',
            'NIDA No',
            'File No',
            'OPF No',
            'Hired Date',
            'Status',
        ];
    }

    public function map($employee): array
    {
        return [
            trim($employee->user?->first_name . ' ' . $employee->user?->last_name),
            ucfirst($employee->gender),
            $employee->designation?->designation_name ?? 'N/A',
            $employee->division?->department?->name ?? 'N/A',
            $employee->division?->name ?? 'N/A',
            $employee->unit?->name ?? 'N/A',
            $employee->identifications->identificationnida_no,
            $employee->file_number,
            $employee->opf_number,
            $employee->hired_date,
            $employee->is_active === 'active' ? 'Active' : 'Inactive',
        ];
    }
}
