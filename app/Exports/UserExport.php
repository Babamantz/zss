<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromQuery, WithMapping, WithHeadings
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Build the query for the export with Spatie roles and Tenant filters.
     */
    public function query()
    {
        // Start with eager loading for performance (roles and tenant)
        $query = User::with(['roles', 'tenant']);

        // Apply Spatie Role Filter
        if (!empty($this->filters['role'])) {
            $query->role($this->filters['role']);
        }

        // Apply Tenant Filter
        if (!empty($this->filters['tenant_id'])) {
            $query->where('tenant_id', $this->filters['tenant_id']);
        }

        $query->where('is_active', (bool)$this->filters['is_active']);
        return $query;
    }

    /**
     * Map each user row to the Excel columns.
     */
    public function map($user): array
    {
        return [
            $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
            $user->email,
            $user->getRoleNames()->implode(', '), // Join Spatie roles with a comma
            $user->tenant->name ?? 'System Admin',
            $user->is_active ? 'Active' : 'Inactive',
            $user->created_at->format('d M, Y'),
        ];
    }

    /**
     * Define the table headers.
     */
    public function headings(): array
    {
        return [
            'Full Name',
            'Email Address',
            'Assigned Roles',
            'Tenant / Organization',
            'Status',
            'Registration Date',
        ];
    }
}
