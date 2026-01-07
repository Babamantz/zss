<?php

namespace App\Livewire\Core\Admin\Role;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Session;

class RoleCreate extends Component
{

    public bool $isEdit = false;

    public ?string $role = null;
    public array $permissions = [];
    public function getRolesProperty()
    {
        return Role::select(['id', 'name'])->get();
    }
    public function getPermissionNamesProperty()
    {
        return Permission::select(['id', 'name'])->get();
    }

    public function mount(?string $roleName = null)
    {
        // dd($roleName);
        $this->permissions = [];

        if ($roleName) {
            $this->isEdit = true;

            $role = Role::findByName($roleName);

            $this->role = $role->name;
            $this->permissions = $role->permissions->pluck('name')->toArray();

            // dd($this->permissions);

            $this->dispatch('prefill-permissions', $this->permissions);
        }
    }


    public function save(): void
    {
        $this->validate([
            'role'        => ['required', 'string'],
            'permissions' => [ 'array'],
        ]);

        // dd($this->permissions);

        try {
            $role = Role::findByName($this->role);

            $role->syncPermissions($this->permissions);

            $this->reset(['role', 'permissions']);

            $this->dispatch('resetSelectedPermissions');
            $this->dispatch('resetRoleName');
            // In your save method:

            session()->flash('toastMagic', [
                'status' => 'success',
                'message' => $this->isEdit ? 'Permissions updated successfully' : 'Permissions created successfully.',
            ]);

            $this->redirectRoute('roles.index', navigate: true);
        } catch (\Throwable $e) {
            report($e);

            $this->dispatch('error', [
                'message' => 'Unable to assign permissions.',
            ]);
        }
    }
    public function render()
    {
        return view('livewire.core.admin.role.role-create');
    }
}
