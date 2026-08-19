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
        $query = Employee::query()->with(['user', 'designation', 'identifications.identification', 'division.department', 'unit']);

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
            $query->whereHas('user', function ($u) {
                $u->where('is_active', $this->filters['is_active']);
            });
        }
        if (!empty($this->filters['search'])) {
            $query->whereHas('user', function ($u) {
                $u->where('first_name', 'like', "%{$this->filters['search']}%")
                    ->orWhere('last_name', 'like', "%{$this->filters['search']}%");
            });
        }

        return $query;
    }

    // public function headings(): array
    // {
    //     return [
    //         'Full Name',
    //         'Gender',
    //         'Designation',
    //         'Department',
    //         'Division',
    //         'Unit',
    //         'NIDA No',
    //         'File No',
    //         'OPF No',
    //         'Hired Date',
    //         'ZSSF No',

    //         'Status',
    //     ];
    // }

    // public function map($employee): array
    // {
    //     return [
    //         trim($employee->user?->first_name . ' ' . $employee->user?->last_name),
    //         ucfirst($employee->gender),
    //         $employee->designation?->designation_name ?? 'N/A',
    //         $employee->division?->department?->name ?? 'N/A',
    //         $employee->division?->name ?? 'N/A',
    //         $employee->unit?->name ?? 'N/A',
    //         '-',
    //         // $employee->identifications->identificationnida_no,
    //         $employee->file_number,
    //         $employee->opf_number,
    //         $employee->hired_date,
    //         $employee->user?->is_active === true ? 'Active' : 'Inactive',
    //     ];
    // }

    public function headings(): array
    {
        return [
            'Full Name',
            'Gender',
            'Marital Status',
            'Date of Birth',
            'Designation',
            'Education',
            'Course Name',
            'Department',
            'Division',
            'Unit',
            'NIDA No',
            'Zan Id',
            'File No',
            'OPF No',
            'ZSSF No',
            'ZHSF No',
            'Contacts',
            'Hired Date',
            'Confirmed at Work Date',
            'Has Disability',
            'Disability Type',
            'Location',
            'Status',
        ];
    }

    public function map($employee): array
    {
        $nidaNo = $employee->identifications
            ->first(fn($item) => $item->identification?->slug === 'nida')
            ?->identification_no ?? '-';
        $zssfNo = $employee->identifications
            ->first(fn($item) => $item->identification?->slug === 'zssf-id')
            ?->identification_no ?? '-';
        $zhsfNo = $employee->identifications
            ->first(fn($item) => $item->identification?->slug === 'zhsf-id')
            ?->identification_no ?? '-';
        $zanIdNo = $employee->identifications
            ->first(fn($item) => $item->identification?->slug === 'zan-id')
            ?->identification_no ?? '-';

        $education_level = $employee->employed_education_level->name;

        $courseName = $employee->education_levels->first(function ($item) use ($education_level) {
            $itemVal = $item->employed_education_level?->course_name;
            return str_contains(strtolower(trim($itemVal)), strtolower(trim($education_level)));
        })?->course_name ?? '-';

        // dd($courseName);


        return [
            trim($employee->user?->first_name . ' ' . $employee->user?->last_name),
            ucfirst($employee->gender),
            ucfirst($employee->marital_status),
            $employee->dob,
            $employee->designation?->designation_name ?? 'N/A',
            $employee->employed_education_level->name,
            $courseName,
            $courseName ?? 'N/A',
            $employee->division?->name ?? 'N/A',
            $employee->unit?->name ?? 'N/A',
            $nidaNo,
            $zanIdNo,
            $employee->file_number,
            $employee->opf_number,
            $zssfNo,
            $zhsfNo,
            $employee->contacts,
            $employee->hired_date,
            $employee->confirmed_at_work_date,
            $employee->is_disable ? 'Yes' : 'No',
            $employee->disability_types ?? 'N/A',
            $employee->user->tenant?->name ?? 'N/A',
            $employee->user?->is_active === true ? 'Active' : 'Inactive',
        ];
    }
}
