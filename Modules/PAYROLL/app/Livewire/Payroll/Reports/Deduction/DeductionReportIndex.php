<?php

namespace Modules\PAYROLL\Livewire\Payroll\Reports\Deduction;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\HRM\Models\Employee;
use Modules\PAYROLL\Models\EmployeeComponent;
use Modules\PAYROLL\Models\PayPeriod;
use Modules\PAYROLL\Models\PayrollEntryItem;
use Modules\PAYROLL\Models\SalaryComponent;

class DeductionReportIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ── Filters ───────────────────────────────────────────────────────────────
    public ?int   $filterComponentId = null;  // salary_components.id
    public ?int   $filterEmployeeId  = null;  // employees.id
    public ?int   $filterPeriodId    = null;  // pay_periods.id
    public string $search            = '';

    public float $componentTotal;

    // ── Sorting ───────────────────────────────────────────────────────────────
    public string $sortField = 'finalized_amount';
    public string $sortDir   = 'desc';

    public bool $isComponentTotal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterComponentId(): void
    {
        $this->resetPage();
    }
    public function updatingFilterEmployeeId(): void
    {
        $this->resetPage();
    }
    public function updatingFilterPeriodId(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $this->sortField === $field
            ? $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc'
            : [$this->sortField = $field, $this->sortDir = 'asc'];
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'filterComponentId',
            'filterEmployeeId',
            'filterPeriodId',
            'search',
        ]);
        $this->sortField = 'finalized_amount';
        $this->sortDir   = 'desc';
        $this->resetPage();
    }

    // ── PDF URL — passes current filters as query params ──────────────────────

    // public function getPdfUrl(): string
    // {
    //     return route('payroll.reports.deductions-pdf', array_filter([
    //         'component_id' => $this->filterComponentId,
    //         'employee_id'  => $this->filterEmployeeId,
    //         'period_id'    => $this->filterPeriodId,
    //         'search'       => $this->search,
    //         'sort'         => $this->sortField,
    //         'dir'          => $this->sortDir,
    //     ]));
    // }

    // protected function calculateGrossTotal(PayPeriod $payPeriod)
    // {
    //     if ($this->isComponentTotal) {
    //         // 1. Find the single component instance
    //         $component = Component::find($this->filterComponentId);

    //         if ($component) {
    //             // 2. Convert name to lowercase once for easy matching
    //             $componentName = strtolower($component->name);
    //             // 3. Perform the checks with clean math multipliers
    //             if (str_contains(strtolower($componentName, 'zssf')) {
    //                 $this->componentTotal = (float)$payPeriod->total_gross * 0.14;
    //             } elseif (str_contains($componentName, 'zhsf')) {
    //                 $this->componentTotal = (float)$payPeriod->total_gross * 0.035;
    //             }
    //         }
    //     }
    // }



    public function render()
    {
        // dd($this->filterComponentId);
        // Log::info($this->filterComponentId);
        // ── Deduction items from payroll_entry_items ──────────────────────────
        $items = PayrollEntryItem::with([
            'entry.employee.user',
            'entry.employee.department',
            'entry.payPeriod',
            'component',
        ])
            ->where('item_type', 'Deduction')
            ->whereNotNull('component_id')  // exclude non-component lines

            ->when(
                $this->filterComponentId,
                fn($q) =>
                $q->where('component_id', $this->filterComponentId)
            )
            ->when(
                $this->filterEmployeeId,
                fn($q) =>
                $q->whereHas(
                    'entry',
                    fn($e) =>
                    $e->where('employee_id', $this->filterEmployeeId)
                )
            )
            ->when(
                $this->filterPeriodId,
                fn($q) =>
                $q->whereHas(
                    'entry',
                    fn($e) =>
                    $e->where('pay_period_id', $this->filterPeriodId)
                )
            )
            ->when(
                $this->search,
                fn($q) =>
                $q->where('component_name_snapshot', 'like', "%{$this->search}%")
                    ->orWhereHas(
                        'entry.employee.user',
                        fn($u) =>
                        $u->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name',  'like', "%{$this->search}%")
                    )
            )
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate(20);

        // ── Summary totals (respects all filters) ─────────────────────────────
        $totals = PayrollEntryItem::where('item_type', 'Deduction')
            ->whereNotNull('component_id')
            ->when(
                $this->filterComponentId,
                fn($q) =>
                $q->where('component_id', $this->filterComponentId)
            )
            ->when(
                $this->filterEmployeeId,
                fn($q) =>
                $q->whereHas(
                    'entry',
                    fn($e) =>
                    $e->where('employee_id', $this->filterEmployeeId)
                )
            )
            ->when(
                $this->filterPeriodId,
                fn($q) =>
                $q->whereHas(
                    'entry',
                    fn($e) =>
                    $e->where('pay_period_id', $this->filterPeriodId)
                )
            )
            ->when(
                $this->search,
                fn($q) =>
                $q->where('component_name_snapshot', 'like', "%{$this->search}%")
            )
            ->selectRaw('
                COUNT(DISTINCT payroll_entry_id) as entry_count,
                COUNT(*)                         as item_count,
                SUM(finalized_amount)            as total_amount,
                AVG(finalized_amount)            as avg_amount,
                MAX(finalized_amount)            as max_amount,
                MIN(finalized_amount)            as min_amount
            ')
            ->first();

        // ── Breakdown by component (for sidebar insight) ──────────────────────
        $byComponent = PayrollEntryItem::where('item_type', 'Deduction')
            ->whereNotNull('component_id')
            ->when(
                $this->filterEmployeeId,
                fn($q) =>
                $q->whereHas(
                    'entry',
                    fn($e) =>
                    $e->where('employee_id', $this->filterEmployeeId)
                )
            )
            ->when(
                $this->filterPeriodId,
                fn($q) =>
                $q->whereHas(
                    'entry',
                    fn($e) =>
                    $e->where('pay_period_id', $this->filterPeriodId)
                )
            )
            ->selectRaw('
                component_id,
                component_name_snapshot,
                COUNT(*) as occurrences,
                SUM(finalized_amount) as total
            ')
            ->groupBy('component_id', 'component_name_snapshot')
            ->orderByDesc('total')
            ->get();

        // ── Filter options ────────────────────────────────────────────────────
        // Filter SalaryComponents where the related component name is 'Deduction'
        $salaryComponents = SalaryComponent::whereHas('component', function ($q) {
            $q->where('type', 'Deduction');
        })->with('component')
            ->get();

        // dd($salaryComponents);

        // Filter EmployeeComponents where the related component name is 'Deduction'
        $employeeComponents = EmployeeComponent::whereHas('component', function ($q) {
            $q->where('type', 'Deduction');
        })->with('component')
            ->get();




        // dd($salaryComponents,$employeeComponents);
        // ->orderBy('')
        $components = $salaryComponents->concat($employeeComponents);

        // dd($components);

        $employees = Employee::with('user')
            ->where('is_active', 'active')
            ->get();

        $periods = PayPeriod::orderByDesc('start_date')->get();

        return view('payroll::livewire.payroll.reports.deductions.deduction-report-index', compact(
            'items',
            'totals',
            'byComponent',
            'components',
            'employees',
            'periods',
        ));
    }
}
