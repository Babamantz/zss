<?php

namespace Modules\PAYROLL\Livewire\Payroll\PayrollEntries;

use Livewire\Component;
use Modules\PAYROLL\Models\PayrollEntry;

class PayrollEntryShow extends Component
{

    public PayrollEntry $entry;

    public function mount(PayrollEntry $entry): void
    {
        $this->entry = $entry->load([
            'employee.user',
            'employee.department',
            'employee.unit',
            'employee.financeProfile',
            'payPeriod',
            'items.component',
            // 'processedBy',
        ]);
    }

    public function render()
    {
        $earnings = $this->entry->items
            ->filter(fn($i) => $i->component->type === 'Earning')
            ->sortByDesc('finalized_amount');

        $deductions = $this->entry->items
            ->filter(fn($i) => $i->component->type === 'Deduction')
            ->sortByDesc('finalized_amount');


        return view(
            'payroll::livewire.payroll.payroll-entries.payroll-entry-show',
            compact(
                'earnings',
                'deductions'
            )
        );
    }
}
