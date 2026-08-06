<?php

namespace App\Livewire\Core;

use App\Models\User;
use Livewire\Component;
use Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserProfile extends Component
{
    public $user;
    public $employee;

    // ── Change Password Modal ───────────────────────────────────────────────
    public bool $showChangePasswordModal = false;
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount()
    {
        $id = Auth::id();
        // Eager load everything to prevent N+1 queries
        $this->user = User::with(['roles', 'tenant'])->findOrFail($id);

        $this->employee = Employee::with(['division', 'division.department', 'unit', 'bankAccount.bank'])->where('user_id', $id)->first();
    }

    // =========================================================================
    // CHANGE PASSWORD
    // =========================================================================

    public function openChangePasswordModal(): void
    {
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->resetErrorBag();
        $this->showChangePasswordModal = true;
    }

    public function closeChangePasswordModal(): void
    {
        $this->showChangePasswordModal = false;
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->resetErrorBag();
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'string'],
            'new_password'      => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'new_password.different' => 'New password must be different from your current password.',
            'new_password.confirmed' => 'New password confirmation does not match.',
        ]);

        if (!Hash::check($this->current_password, $this->user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password you entered is incorrect.',
            ]);
        }

        $this->user->update([
            'password' => Hash::make($this->new_password),
        ]);

        // Password changed — force a fresh login with the new credentials
        // rather than leaving the (now stale) session active.
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')
            ->with('message', 'Password updated successfully. Please log in with your new password.');
    }

    public function render()
    {
        // Logic to calculate profile completion based on your migration's file fields
        $requiredFiles = ['nida_file', 'zan_id_file', 'birth_certificate_file', 'employment_contract_file'];
        $uploadedCount = 0;

        if ($this->employee) {
            foreach ($requiredFiles as $file) {
                if ($this->employee->$file) $uploadedCount++;
            }
        }

        $completion = ($uploadedCount / count($requiredFiles)) * 100;

        return view('livewire.core.user-profile', [
            'completion' => $completion,
        ]);
    }
}
