<?php

namespace App\Livewire\Core\Admin\Users;

use App\Models\User;
use App\Models\Tenant;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserIndex extends Component
{

    public $search = '';

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

    public function edit(int $id){

    }
    public function render()
    {
        $users = User::query()
            ->with(['roles', 'permissions', 'tenant']) // Eager load relationships
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->get(); // Replace ->get() with ->paginate()

        return view('livewire.core.admin.users.user-index', [
            'users' => $users
        ]);
    }
}
