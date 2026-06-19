<?php

namespace Modules\PAYROLL\Livewire\Payroll\PayrollEntries;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\PAYROLL\Models\PayPeriod;
use Modules\PAYROLL\Models\PayrollEntry;

class PayrollEntryIndex extends Component
{
    use WithPagination;

    public ?int   $pay_period_id = null;
    public string $search        = '';
    public string $filterStatus  = '';  // populated from route query string

    // ── Reset pagination when filters change ──────────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingPayPeriodId(): void
    {
        $this->resetPage();
    }
    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        // Read ?status=xxx from the URL once on page load
        $this->filterStatus = request()->query('status', '');
    }

    public function clearFilters(): void
    {
        $this->reset(['pay_period_id', 'search', 'filterStatus']);
        $this->resetPage();
    }

    public function render()
    {
        $entries = PayrollEntry::with(['employee.user', 'payPeriod'])
            ->when(
                $this->pay_period_id,
                fn($q) => $q->where('pay_period_id', $this->pay_period_id)
            )
            ->when(
                $this->filterStatus,
                fn($q) => $q->whereHas(
                    'payPeriod',
                    fn($q2) => $q2->where('status', $this->filterStatus)
                )
            )
            ->when(
                $this->search,
                fn($q) => $q->whereHas(
                    'employee.user',
                    fn($q2) => $q2
                        ->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name',  'like', "%{$this->search}%")
                )
            )
            ->latest('payroll_entries.created_at')
            ->paginate(20);

        $periods = PayPeriod::orderByDesc('start_date')->get();

        // Totals respect all active filters
        $totals = PayrollEntry::when(
            $this->pay_period_id,
            fn($q) => $q->where('pay_period_id', $this->pay_period_id)
        )
            ->when(
                $this->filterStatus,
                fn($q) => $q->whereHas(
                    'payPeriod',
                    fn($q2) => $q2->where('status', $this->filterStatus)
                )
            )
            ->when(
                $this->search,
                fn($q) => $q->whereHas(
                    'employee.user',
                    fn($q2) => $q2
                        ->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name',  'like', "%{$this->search}%")
                )
            )
            ->selectRaw('
                COUNT(*)                as employee_count,
                SUM(total_gross)        as gross,
                SUM(total_deductions)   as deductions,
                SUM(net_pay)            as net
            ')
            ->first();

        return view(
            'payroll::livewire.payroll.payroll-entries.payroll-entry-index',
            compact('entries', 'periods', 'totals')
        );
    }
}
