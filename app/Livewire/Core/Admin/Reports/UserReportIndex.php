<?php

namespace App\Livewire\Core\Admin\Reports;

use App\Models\User;
use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\UserExport;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;

class UserReportIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $filterRole   = '';
    public string $filterTenant = '';
    public string $filterStatus = '1';
    public string $search       = '';

    // ── Sorting ───────────────────────────────────────────────────────────────
    public string $sortField = 'first_name';
    public string $sortDir   = 'asc';

    // ── Per page ──────────────────────────────────────────────────────────────
    public int $perPage = 15;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterRole(): void
    {
        $this->resetPage();
    }
    public function updatingFilterTenant(): void
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

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterRole', 'filterTenant']);
        $this->filterStatus = '1';
        $this->resetPage();
    }

    // ── Excel export (respects current filters) ───────────────────────────────
    public function downloadReport()
    {
        $filters = [
            'role'      => $this->filterRole,
            'tenant_id' => $this->filterTenant,
            'is_active' => $this->filterStatus,
            'search'    => $this->search,
        ];

        return Excel::download(
            new UserExport($filters),
            'User_Report_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    public function render()
    {
        $users = User::with(['roles', 'permissions', 'tenant', 'employee'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where('first_name', 'like', "%{$this->search}%")
                    ->orWhere('last_name',  'like', "%{$this->search}%")
                    ->orWhere('email',      'like', "%{$this->search}%")
            )
            ->when(
                $this->filterRole,
                fn($q) =>
                $q->role($this->filterRole)
            )
            ->when(
                $this->filterTenant,
                fn($q) =>
                $q->where('tenant_id', $this->filterTenant)
            )
            ->where('is_active', (bool) $this->filterStatus)
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        // Summary counts (unfiltered)
        $counts = [
            'total'       => User::count(),
            'active'      => User::where('is_active', true)->count(),
            'inactive'    => User::where('is_active', false)->count(),
            'with_roles'  => User::has('roles')->count(),
        ];

        return view('livewire.core.admin.reports.user-report-index', [
            'users'   => $users,
            'roles'   => Role::orderBy('name')->get(),
            'tenants' => Tenant::orderBy('name')->get(),
            'counts'  => $counts,
        ]);
    }
}
