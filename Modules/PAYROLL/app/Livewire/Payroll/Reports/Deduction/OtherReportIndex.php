<?php

namespace Modules\PAYROLL\Livewire\Payroll\Reports\Deduction;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\HRM\Models\Employee;
use Modules\PAYROLL\Enums\ReportType;
use Modules\PAYROLL\Models\PayPeriod;
use Modules\PAYROLL\Models\PayrollEntry;

class OtherReportIndex extends Component
{


    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $reportType      = ReportType::SDL;
    public ?int   $filterPeriodId  = null;
    public ?int   $filterEmployeeId = null;
    public string $search          = '';

    // ── Sorting ───────────────────────────────────────────────────────────────
    public string $sortField = 'sdl_amount';
    public string $sortDir   = 'desc';

    public function updatingReportType(): void
    {
        $this->resetPage();
        $this->sortField = 'sdl_amount';
    }
    public function updatingFilterPeriodId(): void
    {
        $this->resetPage();
    }
    public function updatingFilterEmployeeId(): void
    {
        $this->resetPage();
    }
    public function updatingSearch(): void
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
        $this->reset(['filterPeriodId', 'filterEmployeeId', 'search']);
        $this->resetPage();
    }

    // public function getPdfUrl(): string
    // {
    //     return route('payroll.reports.other-pdf', array_filter([
    //         'report_type' => $this->reportType,
    //         'period_id'   => $this->filterPeriodId,
    //         'employee_id' => $this->filterEmployeeId,
    //         'search'      => $this->search,
    //         'sort'        => $this->sortField,
    //         'dir'         => $this->sortDir,
    //     ]));
    // }

    // ── SDL Calculation Logic ─────────────────────────────────────────────────

    protected function buildSdlQuery()
    {
        /*
         * SDL base per entry:
         *   base_salary  = item where component_id IS NULL (the base salary snapshot)
         *   allowances   = SUM of items where item_type='Earning'
         *                  AND LOWER(component_name_snapshot) LIKE '%allowance%'
         *   sdl_base     = base_salary + allowances
         *   sdl_amount   = sdl_base * 0.05
         */
        return PayrollEntry::query()
            ->select([
                'payroll_entries.id',
                'payroll_entries.employee_id',
                'payroll_entries.pay_period_id',
                'payroll_entries.total_gross',
                'payroll_entries.net_pay',
                // Base salary (null component_id line)
                DB::raw("
                    COALESCE((
                        SELECT SUM(pei.finalized_amount)
                        FROM payroll_entry_items pei
                        WHERE pei.payroll_entry_id = payroll_entries.id
                          AND pei.component_id IS NULL
                    ), 0) AS base_salary
                "),
                // Allowances (items containing 'allowance' in name)
                DB::raw("
                    COALESCE((
                        SELECT SUM(pei.finalized_amount)
                        FROM payroll_entry_items pei
                        WHERE pei.payroll_entry_id = payroll_entries.id
                          AND pei.item_type = 'Earning'
                          AND LOWER(pei.component_name_snapshot) LIKE '%allowance%'
                    ), 0) AS total_allowances
                "),
                // SDL base = base_salary + allowances
                DB::raw("
                    COALESCE((
                        SELECT SUM(pei.finalized_amount)
                        FROM payroll_entry_items pei
                        WHERE pei.payroll_entry_id = payroll_entries.id
                          AND pei.component_id IS NULL
                    ), 0)
                    +
                    COALESCE((
                        SELECT SUM(pei.finalized_amount)
                        FROM payroll_entry_items pei
                        WHERE pei.payroll_entry_id = payroll_entries.id
                          AND pei.item_type = 'Earning'
                          AND LOWER(pei.component_name_snapshot) LIKE '%allowance%'
                    ), 0) AS sdl_base
                "),
                // SDL amount = sdl_base × 5%
                DB::raw("
                    (
                        COALESCE((
                            SELECT SUM(pei.finalized_amount)
                            FROM payroll_entry_items pei
                            WHERE pei.payroll_entry_id = payroll_entries.id
                              AND pei.component_id IS NULL
                        ), 0)
                        +
                        COALESCE((
                            SELECT SUM(pei.finalized_amount)
                            FROM payroll_entry_items pei
                            WHERE pei.payroll_entry_id = payroll_entries.id
                              AND pei.item_type = 'Earning'
                              AND LOWER(pei.component_name_snapshot) LIKE '%allowance%'
                        ), 0)
                    ) * 0.05 AS sdl_amount
                "),
            ])
            ->with(['employee.user', 'employee.department', 'payPeriod'])
            ->when(
                $this->filterPeriodId,
                fn($q) =>
                $q->where('pay_period_id', $this->filterPeriodId)
            )
            ->when(
                $this->filterEmployeeId,
                fn($q) =>
                $q->where('employee_id', $this->filterEmployeeId)
            )
            ->when(
                $this->search,
                fn($q) =>
                $q->whereHas(
                    'employee.user',
                    fn($u) =>
                    $u->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name',  'like', "%{$this->search}%")
                )
            );
    }

    public function render()
    {
        $periods   = PayPeriod::orderByDesc('start_date')->get();
        $employees = Employee::whereHas('user',fn($q)=>$q->where('is_active',true))->get();

        // ── SDL report ────────────────────────────────────────────────────────
        if ($this->reportType === ReportType::SDL) {

            $query = $this->buildSdlQuery()
                ->orderBy($this->sortField, $this->sortDir);

            $rows = $query->paginate(20);

            // Totals (unfiltered by page)
            $totalsQuery = $this->buildSdlQuery();
            $totals = DB::table(DB::raw("({$totalsQuery->toSql()}) as sub"))
                ->mergeBindings($totalsQuery->getQuery())
                ->selectRaw('
                    COUNT(*)            as employee_count,
                    SUM(base_salary)    as total_base,
                    SUM(total_allowances) as total_allowances,
                    SUM(sdl_base)       as total_sdl_base,
                    SUM(sdl_amount)     as total_sdl
                ')
                ->first();

            return view('payroll::livewire.payroll.reports.deductions.other-report-index', [
                'rows'      => $rows,
                'totals'    => $totals,
                'periods'   => $periods,
                'employees' => $employees,
                'reportTypes' => ReportType::ALL,
            ]);
        }

        
    }
}
