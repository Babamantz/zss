<?php
namespace App\Livewire\Core\Admin\Role;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search    = '';
    public string $sortField = 'name';
    public string $sortDir   = 'asc';
    public int    $perPage   = 15;

    public function updatingSearch(): void
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
        $this->reset(['search']);
        $this->resetPage();
    }

    public function render()
    {
        $roles = Role::withCount('permissions', 'users')
            ->when(
                $this->search,
                fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            )
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        $stats = [
            'total_roles'       => Role::count(),
            'total_permissions' => Permission::count(),
            'total_users_with_roles' => \App\Models\User::role(
                Role::pluck('name')->toArray()
            )->count(),
        ];

        return view('livewire.core.admin.role.role-index', compact('roles', 'stats'));
    }
}
