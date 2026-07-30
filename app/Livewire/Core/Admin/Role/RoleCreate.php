<?php

namespace App\Livewire\Core\Admin\Role;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleCreate extends Component
{

    public bool $isEdit = false;

    public ?string $role = null;
    public array $permissions = [];

    public function getRolesProperty()
    {
        return Role::select(['id', 'name'])->get()->map(fn($role) => [
            'value' => $role->name,
            'label' => $role->name,
        ]);
    }

    public function getPermissionNamesProperty()
    {
        return Permission::select(['id', 'name'])->get()->map(fn($permission) => [
            'value' => $permission->name,
            'label' => $permission->name,
        ]);
    }

    public function mount(?string $roleName = null)
    {
        $this->permissions = [];

        if ($roleName) {
            $this->isEdit = true;

            $role = Role::findByName($roleName);

            $this->role = $role->name;
            $this->permissions = $role->permissions->pluck('name')->toArray();
        }
    }


    public function save(): void
    {
        $this->validate([
            'role'          => ['required', 'string', 'exists:roles,name'],
            'permissions'   => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        try {
            $role = Role::findByName($this->role);

            $role->syncPermissions($this->permissions);

            $this->reset(['role', 'permissions']);

          

            session()->flash('toastMagic', [
                'status' => 'success',
                'message' => $this->isEdit ? 'Permissions updated successfully' : 'Permissions created successfully.',
            ]);

            $this->redirectRoute('roles.create', navigate: true);
        } catch (\Throwable $e) {
            report($e);

            $this->dispatch('toastMagic', type: 'error', message: 'Unable to assign permissions.');
        }
    }
    public function render()
    {
        return view('livewire.core.admin.role.role-create');
    }
}
