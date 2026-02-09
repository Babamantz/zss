<?php

namespace Modules\HRM\Livewire\HRM;

use session;
use Livewire\Component;
use Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeDashboard extends Component
{
    public function mount()
    {
        session(['module' => 'HRM']);
    }
    public function render()
    {
        $stats = [
            'total_staff' => Employee::where('is_active', 'active')->count(),
            'missing_docs' => Employee::where(function ($query) {
                $query->whereNull('employment_contract_file')
                    ->orWhereNull('nida_file');
            })->count(),
            'upcoming_retirements' => Employee::where('retiring_date', '<=', now()->addYear())
                ->where('is_active', 'active')
                ->count(),
            'gender_ratio' => Employee::select('gender', DB::raw('count(*) as total'))
                ->groupBy('gender')
                ->pluck('total', 'gender') // Returns ['male' => 10, 'female' => 5]
        ];
        return view('hrm::livewire.h-r-m.employee-dashboard', [
            'stats' => $stats
        ]);
    }
}
