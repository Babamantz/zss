<?php

namespace App\Livewire\Core\Admin\Users;

use Throwable;
use App\Models\User;
use App\Models\Tenant;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class UserEdit extends Component
{
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public ?string $mode = null;
    public string $email = '';

    public int|string|null $location = null;

    public array $role = [];
    public array $roles = [];
    public array $permissions = [];
    public array $direct_permissions = [];
    public ?int $userId = null;



    protected function rules()
    {
        return [
            'first_name' => 'required|string|min:2|max:50',
            'middle_name' => 'nullable|string|max:50',
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
        'last_name.required' => 'Last name is required',
        'email.required' => 'Email address is required',
        'email.email' => 'Please enter a valid email address',
        'email.unique' => 'This email is already registered',
        'location.required' => 'Please select a location',
        'role.required' => 'Please select a role',
    ];

    public function mount(?int $id = null, ?string $mode = null)
    {
        $this->userId = $id;
        $this->mode = $mode;


        logger($mode);

        if ($this->userId) {

            try {
                $user = User::findOrFail($this->userId);
                // $this->fillFromUser($user);
                $this->fill([
                    'first_name'  => $user->first_name,
                    'middle_name' => $user->middle_name,
                    'last_name'   => $user->last_name,
                    'email'       => $user->email,
                    'location'    => $user->tenant_id,
                    'role'        => $user->getRoleNames()->toArray(),
                    'direct_permissions' => $user->getDirectPermissions()->pluck('name')->toArray(),
                ]);
            } catch (Throwable $e) {

                if ($e instanceof ModelNotFoundException) {
                    $this->dispatch(
                        'toastMagic',
                        status: 'error',
                        title: 'Finding Error',
                        message: 'User not found'
                    );

                    return;
                }

                // Re-throw anything else (VERY important)
                throw $e;
            }
        }


        $this->permissions = [];
        $this->roles = [];
    }

    public function getIsEditProperty(): bool
    {
        return filled($this->userId);
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

    public function edit($id, string $mode = 'view') {}

    public function save()
    {
        try {
            DB::transaction(function () {

                if ($this->mode === 'edit') {
                    // Find the user first, then update
                    $user = User::findOrFail($this->userId);

                    $user->update([
                        'first_name' => $this->first_name,
                        'middle_name' => $this->middle_name,
                        'last_name' => $this->last_name,
                        'email' => $this->email,
                        'tenant_id' => $this->location,
                        'password' => Hash::make($this->last_name)
                    ]);

                    $this->assignRole($user);
                    $this->assignPermissions($user);

                    $this->dispatch('toastMagic', [
                        'type' => 'success',
                        'message' => 'User updated successfully!',
                    ]);

                    return; // Important: Exit after edit
                }

                // Create new user (only runs if mode is NOT 'edit')
                $user = User::create([
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'tenant_id' => $this->location,
                    'password' => Hash::make($this->last_name)
                ]);

                $this->assignRole($user);
                $this->assignPermissions($user);

                $this->dispatch('toastMagic', [
                    'type' => 'success',
                    'message' => 'User created successfully!',
                ]);

                $this->reset(['first_name', 'middle_name', 'last_name', 'email', 'location', 'role']);
            });
        } catch (\Exception $e) {
            Log::error('User save failed', [
                'mode' => $this->mode,
                'user_id' => $this->userId ?? null,
                'email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toastMagic', [
                'type' => 'error',
                'message' => 'Failed to save user. Please try again.',
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
            $user->givePermissionTo($this->direct_permissions);
        }
    }
    public function render()
    {
        return view('livewire.core.admin.users.user-edit');
    }
}
