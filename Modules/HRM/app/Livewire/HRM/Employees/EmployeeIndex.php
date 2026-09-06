<?php

namespace Modules\HRM\Livewire\HRM\Employees;

use App\Enums\ApprovalDecision;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Approval\ApprovalEngine;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Unit;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeIndex extends Component
{
    use WithPagination;

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $search           = '';
    public string $filterDepartment = '';
    public string $filterUnit       = '';
    public string $filterStatus     = ''; // '' = all, 'true' = active, 'false' = in-active
    public string $filterGender     = '';  // 'male' | 'female' | ''

    // ── Sorting ───────────────────────────────────────────────────────────────
    public string $sortField = 'created_at';
    public string $sortDir   = 'desc';

    public ?int $actioningEmployeeId = null;
    public string $actionRemarks = '';
    public string $actionType = ''; // 'approve' | 'reject'

    // ── Per page ──────────────────────────────────────────────────────────────
    public int $perPage = 15;

    // ── Reset pagination when any filter changes ──────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterDepartment(): void
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
    public function updatingFilterGender(): void
    {
        $this->resetPage();
    }
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    // ── Sorting toggle ────────────────────────────────────────────────────────
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
            'search',
            'filterDepartment',
            'filterUnit',
            'filterStatus',
            'filterGender',
            'sortField',
            'sortDir',
        ]);
        $this->sortField = 'created_at';
        $this->sortDir   = 'desc';
        $this->resetPage();
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function deleteEmployee(int $id): void
    {
        Employee::findOrFail($id)->delete();
        session()->flash('success', 'Employee removed.');
    }



    // ── Computed: filter options ───────────────────────────────────────────────
    #[Computed]
    public function departments()
    {
        return Department::orderBy('name')->get();
    }

    #[Computed]
    public function units()
    {
        return Unit::when(
            $this->filterDepartment,
            fn($q) => $q->where('department_id', $this->filterDepartment)
        )->orderBy('name')->get();
    }

    #[Computed]
    public function roleNames()
    {
        return Role::select(['id', 'name'])->orderBy('name')->get();
    }

    #[Computed]
    public function permissionNames()
    {
        return Permission::select(['id', 'name'])->orderBy('name')->get();
    }

    #[Computed]
    public function locations()
    {
        return Tenant::select(['id', 'name'])->orderBy('name')->get();
    }

    public function openActionModal(int $employeeId, string $type)
    {
        $this->actioningEmployeeId = $employeeId;
        $this->actionType = $type;
        $this->actionRemarks = '';
        $this->resetErrorBag();
    }

    public function closeActionModal()
    {
        $this->reset(['actioningEmployeeId', 'actionRemarks', 'actionType']);
    }

    public function confirmAction(ApprovalEngine $engine)
    {
        $this->validate([
            'actionRemarks' => 'required|string|min:3',
        ], [], ['actionRemarks' => 'remarks']);

        $employee = Employee::with('approvalTransaction.currentStep')->findOrFail($this->actioningEmployeeId);
        $transaction = $employee->approvalTransaction;

        abort_unless($transaction && $transaction->canBeActionedBy(auth()->user()), 403);

        $decision = $this->actionType === 'approve'
            ? ApprovalDecision::Approved
            : ApprovalDecision::Rejected;

        $engine->decide($transaction, auth()->user(), $decision, $this->actionRemarks);

        $this->closeActionModal();
        session()->flash('success', $decision === ApprovalDecision::Approved ? 'Employee approved.' : 'Employee rejected.');
    }

    // ── Render ────────────────────────────────────────────────────────────────
    public function render()
    {
        $employees = Employee::query()
            ->with([
                'user',
                'user.roles',
                'user.permissions',
                'user.tenant',
                'division.department',
                'unit',
                'approvalTransaction.currentStep'
            ])
            // ->where('is_hr_registered', true)

            // Search across name + email
            ->when(
                $this->search,
                fn($q) =>
                $q->whereHas(
                    'user',
                    fn($u) =>
                    $u->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name',  'like', "%{$this->search}%")
                        ->orWhere('email',      'like', "%{$this->search}%")
                )
            )

            // Department filter (resolved via division, no direct department_id on employees)
            ->when(
                $this->filterDepartment,
                fn($q) =>
                $q->whereHas('division', fn($d) => $d->where('department_id', $this->filterDepartment))
            )

            // Unit filter
            ->when(
                $this->filterUnit,
                fn($q) =>
                $q->where('unit_id', $this->filterUnit)
            )

            ->when(
                $this->filterStatus !== '',
                fn($q) =>
                $q->whereHas(
                    'user',
                    fn($u) => $u->where('is_active', $this->filterStatus === 'true')
                )
            )

            // Gender filter
            ->when(
                $this->filterGender,
                fn($q) =>
                $q->where('gender', $this->filterGender)
            )

            // Sorting
            ->when(
                in_array($this->sortField, ['created_at', 'opf_number', 'gender']),
                fn($q) => $q->orderBy($this->sortField, $this->sortDir),
                fn($q) => $q->when(
                    $this->sortField === 'is_active',
                    // Sort by related user's status via join
                    fn($q2) => $q2->leftJoin('users', 'users.id', '=', 'employees.user_id')
                        ->select('employees.*')
                        ->orderBy('users.is_active', $this->sortDir),
                    // Sort by related user name
                    fn($q2) => $q2->orderBy(
                        User::select('first_name')
                            ->whereColumn('id', 'employees.user_id')
                            ->limit(1),
                        $this->sortDir
                    )
                )
            )

            ->paginate($this->perPage);

        $counts = [
            'total'    => Employee::whereHas('user', fn($u) => $u->where('is_active', true)->where('is_active', true))->orWhere('is_hr_registered', true)->count(),
            'active'   => Employee::where('is_hr_registered', true)
                ->whereHas('user', fn($u) => $u->where('is_active', true))
                ->count(),
            'inactive' => Employee::where('is_hr_registered', true)
                ->whereHas('user', fn($u) => $u->where('is_active', false))
                ->count(),
            'male'     => Employee::where('is_hr_registered', true)->where('gender', 'male')->count(),
            'female'   => Employee::where('is_hr_registered', true)->where('gender', 'female')->count(),
        ];

        return view('hrm::livewire.h-r-m.employees.employee-index', [
            'employees' => $employees,
            'counts'    => $counts,
        ]);
    }
}
