<?php

namespace Modules\HRM\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\HRM\Models\Employee;

class EmployeePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    /**
     * Determine if a user can view a list of employees.
     */
    public function viewAny(User $user): bool
    {
        // Global roles like HR can view the list. 
        // Heads can also view the list (though they will filter the query in the controller).
        return $user->hasRole(['director-hr', 'hr-officer', 'head', 'director']);
    }

    /**
     * Determine if a user can view a specific employee's details.
     */
    public function view(User $user, Employee $employee): bool
    {
        // 1. HR roles can see all employees across any tenant
        if ($user->hasRole(['director-hr', 'hr-officer'])) {
            return true;
        }

        // 2. Special rule for the "pemba" tenant
        if ($employee->tenant_name === 'pemba') {
            return $user->hasRole('director') && $user->tenant_name === 'pemba';
        }

        // 3. Department Head rule: must be a "head" and share the same tenant
        if ($user->hasRole('head')) {
            return $user->tenant_name === $employee->tenant_name;
        }

        return false;
    }
}
