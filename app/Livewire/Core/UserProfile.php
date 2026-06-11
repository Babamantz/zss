<?php

namespace App\Livewire\Core;

use App\Models\User;
use Livewire\Component;
use Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\Auth;

class UserProfile extends Component
{
    public $user;
    public $employee;

    public function mount()
    {
        $id = Auth::id();
        // Eager load everything to prevent N+1 queries
        $this->user = User::with(['roles', 'tenant'])->findOrFail($id);
        
        $this->employee = Employee::with(['department', 'unit', 'bank'])->where('user_id', $id)->first();
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
            'completion' => $completion
        ]);
    }
}
