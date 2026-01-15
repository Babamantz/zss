<?php

namespace Modules\HRM\Livewire\HRM;

use session;
use Livewire\Component;

class EmployeeDashboard extends Component
{
    public function mount()
    {
        session(['module' => 'HRM']);
    }
    public function render()
    {
        return view('hrm::livewire.h-r-m.employee-dashboard');
    }
}
