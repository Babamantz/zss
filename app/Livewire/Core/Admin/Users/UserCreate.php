<?php

namespace App\Livewire\Core\Admin\Users;

use App\Models\User;
use App\Models\Tenant;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserCreate extends Component
{
    public string $first_name = '';

    public string $user_disabilty_check = '';

    public string $user_title = ''; 
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $location = '';
    public string $role = '';
    public array $permissions = [];
    public array $roles = [];
    public array $direct_permissions = [];

    protected function rules()
    {
        return [
            'first_name' => 'required|string|min:2|max:50',
            'middle_name' => 'required|string|max:50',
            'last_name' => 'required|string|min:2|max:50',
            'email' => 'required|email|unique:users,email',
            'location' => 'required|numeric|exists:tenants,id',
            'role' => 'required|numeric|exists:roles,id',
            'direct_permissions' => 'nullable|array',
            'direct_permissions.*' => 'numeric|exists:permissions,id',
        ];
    }


    protected $messages = [
        'first_name.required' => 'First name is required',
        'first_name.min' => 'First name must be at least 2 characters',
        'middle_name.min' => 'First name must be at least 2 characters',
        'last.min' => 'First name must be at least 2 characters',
        'middle_name.required' => 'Middle name is required',
        'last_name.required' => 'Last name is required',
        'email.required' => 'Email address is required',
        'email.email' => 'Please enter a valid email address',
        'email.unique' => 'This email is already registered',
        'location.required' => 'Please select a location',
        'role.required' => 'Please select a role',
    ];

    public function mount()
    {
        $this->permissions = [];
        $this->roles = [];
    }

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

    public function save()
    {
        // dd($this->direct_permissions);
        // $this->validate();
        try {
            DB::transaction(function () {


                $user = User::create([
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'tenant_id' => $this->location,
                    'password' => Hash::make($this->last_name),
                    'created_by' => auth()->id()
                ]);

                // dd('nafika hapa kwanza ');    


                $this->assignRole($user);
                $this->assignPermissions($user);

                // dd('nafika hapa pili');

                $this->dispatch('toastMagic', [
                    'type' => 'success',
                    'message' => 'User created successfully!',
                ]);

                $this->dispatch('user-created', userId: $user->id);

                $this->reset(['first_name', 'middle_name', 'last_name', 'email', 'location', 'role']);

                // dd($this->direct_permissions);
            });
        } catch (Throwable $e) {
            Log::error('User creation failed', [
                'email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to create user. Please try again.',
            ]);
        }
    }

    protected function assignRole(User $user): void
    {
        if ($this->role) {
            $user->assignRole($this->role);
        }
    }

    protected function assignPermissions(User $user): void
    {
        if (!empty($this->direct_permissions)) {
            // Get permission names from IDs
            $permissionNames = Permission::whereIn('id', $this->direct_permissions)
                ->where('guard_name', 'web')
                ->pluck('name')
                ->toArray();

            if (!empty($permissionNames)) {
                $user->givePermissionTo($permissionNames);
            }
        }
    }

    public function render()
    {
        return view('livewire.core.admin.users.user-create');
    }
}
