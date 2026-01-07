<?php

namespace App\Livewire\Core\Admin\Role;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleIndex extends Component
{

    public $search = '';


    public function render()
    {
        $roles = Role::query()
            ->with('permissions')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->get();
        return view('livewire.core.admin.role.role-index', [
            'roles' => $roles
        ]);
    }
}
