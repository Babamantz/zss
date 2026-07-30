<?php

namespace App\Livewire\Core\Admin\Users;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

use function PHPUnit\Framework\throwException;


class UserIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search       = '';
    public string $filterRole   = '';
    public ?int $filterStatus = null;
    public string $filterTenant = '';
    public string $sortField    = 'first_name';
    public string $sortDir      = 'asc';
    public int    $perPage      = 15;

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
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }
    public function updatedFilterStatus(): void
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
        $this->resetPage();
    }

    public function render()
    {
        Log::info( $this->filterStatus);

        $users = User::with(['tenant', 'roles', 'permissions', 'employee'])
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
                $q->whereHas(
                    'roles',
                    fn($r) =>
                    $r->where('name', $this->filterRole)
                )
            )
            ->when(
                $this->filterTenant,
                fn($q) =>
                $q->where('tenant_id', $this->filterTenant)
            )
            ->when(
                $this->filterStatus,
                fn($q) =>
                $q->where('is_active',(int) $this->filterStatus)
            )
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.core.admin.users.user-index', [
            'users' => $users
        ]);
    }
}
