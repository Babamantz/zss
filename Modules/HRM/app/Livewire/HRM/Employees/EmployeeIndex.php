<?php

namespace Modules\HRM\Livewire\HRM\Employees;

use App\Models\User;
use App\Models\Tenant;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Modules\HRM\Models\Employee;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmployeeIndex extends Component
{
    public $search = '';
    public bool $open = false;

    #[Computed]
    public function roleNames()
    {
        return Role::select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function permissionNames()
    {
        return Permission::select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function locations()
    {
        return Tenant::select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    public function deleteUser(?int $id) {}


    public function render()
    {
        $employees = Employee::query()
            ->with(['user', 'user.roles', 'user.permissions', 'user.tenant'])
            ->where('hr_registered', true)
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->get();
        return view('hrm::livewire.h-r-m.employees.employee-index', [
            'employees' => $employees
        ]);
    }
}
