<?php

namespace App\Livewire\Core\Admin\Users;

use Throwable;
use App\Models\User;
use App\Models\Tenant;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserEdit extends Component
{
    public ?int $userId = null;

    // Form Inputs
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public int $is_officer = 0;
    public int $is_active = 1;
    public int|string|null $location = null;
    public string $role = '';
    public array $direct_permissions = [];

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'middle_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'min:2', 'max:50'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)],
            'is_officer' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'location' => ['required', 'integer', 'exists:tenants,id'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'direct_permissions' => ['nullable', 'array'],
            'direct_permissions.*' => ['string', 'exists:permissions,name'],
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

    public function mount(?int $id = null): void
    {
        $this->userId = $id;

        if ($this->userId) {
            try {
                $user = User::findOrFail($this->userId);

                $this->fill([
                    'first_name' => $user->first_name,
                    'middle_name' => $user->middle_name ?? '',
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'is_officer' => (int) $user->is_officer,
                    'is_active' => (int) $user->is_active,
                    'location' => $user->tenant_id,
                    'role' => $user->getRoleNames()->first() ?? '',
                    'direct_permissions' => $user->getDirectPermissions()->pluck('name')->toArray(),
                ]);
            } catch (ModelNotFoundException $e) {
                $this->dispatch('toastMagic', status: 'error', title: 'Finding Error', message: 'User not found');
                $this->redirect(route('users.create'), navigate: true);
                return;
            } catch (Throwable $e) {
                Log::error('User mount failed', ['user_id' => $id, 'error' => $e->getMessage()]);
                throw $e;
            }
        }

        // Hydrate the JS select widgets once, on initial load only.
    }

    #[Computed]
    public function isEdit(): bool
    {
        return filled($this->userId);
    }

    #[Computed]
    public function roleNames()
    {
        return Role::select(['id', 'name'])->get()->map(fn($role) => [
            'value' => $role->name,
            'label' => $role->name,
        ]);
    }

    #[Computed]
    public function permissionNames()
    {
        return Permission::select(['id', 'name'])->get()->map(fn($permission) => [
            'value' => $permission->name,
            'label' => $permission->name,
        ]);
    }

    #[Computed]
    public function locations()
    {
        return Tenant::select(['id', 'name'])->orderBy('name')->get();
    }

    public function updatedIsOfficer($value)
    {
        $this->is_officer = (int) $value;
    }

    public function updatedIsActive($value)
    {
        $this->is_active = (int) $value;
    }

    public function save()
    {
        // Run validation up front so field-level messages (e.g. "Please
        // select a role") reach the user instead of falling through to
        // the generic catch block below.
        $validated = $this->validate();

        try {
            DB::transaction(function () use ($validated) {
                $payload = [
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? '',
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'is_officer' => $validated['is_officer'],
                    'is_active' => $validated['is_active'],
                    'tenant_id' => $validated['location'],
                ];

                if ($this->isEdit) {
                    $user = User::findOrFail($this->userId);
                    $user->update($payload);

                    $this->assignRole($user);
                    $this->assignPermissions($user);

                    $this->dispatch('toastMagic', type: 'success', message: 'User updated successfully!');
                    return;
                }

                // Append lowercase default secure password on create
                $payload['password'] = Hash::make(strtolower(trim($this->last_name)));

                $user = User::create($payload);
                $this->assignRole($user);
                $this->assignPermissions($user);

                $this->dispatch('toastMagic', type: 'success', message: 'User created successfully!');
                $this->reset(['first_name', 'middle_name', 'last_name', 'email', 'is_officer', 'is_active', 'location', 'role', 'direct_permissions']);

                // Re-hydrate the JS selects to reflect the cleared form state.
                $this->dispatch('refreshSelect2', role: $this->role, permissions: $this->direct_permissions);
            });
        } catch (\Exception $e) {
            Log::error('User save failed', [
                'is_edit' => $this->isEdit,
                'user_id' => $this->userId,
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('toastMagic', type: 'error', message: 'Failed to save user. Please try again.');
        }
    }



    protected function assignRole(User $user): void
    {
        if (filled($this->role)) {
            $user->syncRoles([$this->role]);
        }
    }

    protected function assignPermissions(User $user): void
    {
        // syncPermissions() always runs (no filled() guard): an empty
        // $direct_permissions array is a valid "remove all permissions"
        // state, and guarding it would make that state unreachable.
        $user->syncPermissions($this->direct_permissions);
    }

    public function render()
    {
        return view('livewire.core.admin.users.user-edit');
    }
}
