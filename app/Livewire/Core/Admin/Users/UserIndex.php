<?php

namespace App\Livewire\Core\Admin\Users;

use Throwable;
use App\Models\User;
use App\Models\Tenant;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use function PHPUnit\Framework\throwException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserIndex extends Component
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
        $users = User::query()
            ->with(['roles', 'permissions', 'tenant'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->get();

        return view('livewire.core.admin.users.user-index', [
            'users' => $users
        ]);
    }
}
