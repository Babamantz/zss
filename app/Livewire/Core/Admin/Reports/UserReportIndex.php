<?php

namespace App\Livewire\Core\Admin\Reports;

use App\Models\User;
use App\Models\Tenant;
use Livewire\Component;
use App\Exports\UserExport;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;

class UserReportIndex extends Component
{
    public $filterRole = '';
    public $filterTenant = '';
    public $filterStatus = '1';

    public function downloadReport()
    {
        $filters = [
            'role' => $this->filterRole,
            'tenant_id' => $this->filterTenant,
            'is_active' => $this->filterStatus,
        ];
        return Excel::download(new UserExport($filters), 'User_Report_' . now()->format('Y-m-d') . '.xlsx');
    }
    public function render()
    {
        $users = User::with(['roles', 'tenant'])
            ->when($this->filterRole, function ($query) {
                $query->role($this->filterRole); // Spatie role scope
            })
            ->when($this->filterTenant, fn($q) => $q->where('tenant_id', $this->filterTenant))
            ->where('is_active', (bool)$this->filterStatus)
            ->get();

        return view('livewire.core.admin.reports.user-report-index', [
            'users' => $users,
            'roles' => Role::all(),
            'tenants' => Tenant::all(),
        ]);
    }
}
