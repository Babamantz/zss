<?php

// Modules/PAYROLL/Services/PayrollProcessor.php

namespace Modules\PAYROLL\Services;

use App\Models\ApprovalAction;
use App\Models\ApprovalRequest;
use App\Models\ChainModule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\HRM\Models\Employee;
use Modules\PAYROLL\Models\PayPeriod;
use Modules\PAYROLL\Models\PayrollEntry;
use Modules\PAYROLL\Models\SalaryComponent;
use RuntimeException;

class PayrollProcessor
{
    public function process(PayPeriod $period, $confirmation): array
    {
        if ($period->isLocked() || $period->isApproved()) {
            throw new RuntimeException('This pay period cannot be re-processed.');
        }

        $period->update([
            'status'     => 'Processing',
            'confirmation' => $confirmation,
            'updated_by' => auth()->id(),
        ]);

        // Load employees with finance profile + active components
        $employees = Employee::with([
            'financeProfile',
            'components' => fn($q) => $q->with('component')->active(),
            'user.tenant',
        ])
            ->where('is_active', 'active')
            ->whereHas('financeProfile') // must have a finance profile
            ->get();

        if ($employees->isEmpty()) {
            throw new RuntimeException(
                'No active employees with finance profiles found.'
            );
        }

        // Load global components once — applied to all employees
        // $globalComponents = SalaryComponent::globalComponents()
        //     ->orderByRaw("CASE WHEN calculation_type = 'fixed' THEN 0 ELSE 1 END") // fixed first
        //     ->get();

        $results = [];

        DB::transaction(function () use ($employees, $period, &$results) {

            // Wipe previous draft entries for this period
            PayrollEntry::where('pay_period_id', $period->id)
                ->each(function ($e) {
                    $e->items()->delete();
                    $e->delete();
                });

            foreach ($employees as $employee) {

                // ── 1. Base salary from Financial Profile ─────────────────────
                $baseSalary = (float) $employee->financeProfile->base_salary;

                if ($baseSalary <= 0) {
                    continue; // skip employees with no salary set
                }

                $earningsTotal   = $baseSalary; // base salary is the starting gross
                $deductionsTotal = 0;
                $lineItems       = [];

                // ── 2. Add base salary as a snapshot line item ─────────────────
                // Not stored as a component — just a snapshot for the payslip
                $lineItems[] = [
                    'component_id'            => null,
                    'component_name_snapshot' => 'Base Salary',
                    'finalized_amount'        => $baseSalary,
                    'item_type'               => 'Earning',
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ];

                // ── 3. Normal employee-specific components ─────────────────────
                foreach ($employee->components as $ec) {
                    $comp   = $ec->component;

                    $amount = $ec->computeAmount($earningsTotal);

                    if ($comp->type === 'Earning') {
                        $earningsTotal += $amount;
                    } else {
                        $deductionsTotal += $amount;
                    }

                    $lineItems[] = [
                        'component_id'            => $comp->id,
                        'component_name_snapshot' => $comp->name,
                        'finalized_amount'        => $amount,
                        'item_type'               => $comp->type,
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ];
                }

                // ── 4. Total Gross is now fixed before global components ────────
                $totalGross = $earningsTotal;


                $netPay = $totalGross - $deductionsTotal;

                // ── 6. Create payroll entry ────────────────────────────────────
                $entry = PayrollEntry::create([
                    'employee_id'      => $employee->id,
                    'pay_period_id'    => $period->id,
                    'total_gross'      => $totalGross,
                    'total_deductions' => $deductionsTotal,
                    'total_allowances' => $earningsTotal,
                    'net_pay'          => $netPay,
                    'processed_at'     => now(),
                    'processed_by'     => auth()->id(),
                ]);

                $entry->items()->createMany($lineItems);

                $results[] = [
                    'employee_id'     => $employee->id,
                    'employee_name'   => $employee->user->first_name . ' ' . $employee->user->last_name,
                    'opf_number'      => $employee->opf_number ?? '—',
                    'tenant'          => $employee->user->tenant?->name ?? '—',
                    'employment_type' => $employee->employment_type ?? 'permanent',
                    'base_salary'     => $baseSalary,
                    'gross'           => $totalGross,
                    'deductions'      => $deductionsTotal,
                    'net_pay'         => $netPay,
                ];
            }
        });

        // ── Update pay period summary totals ──────────────────────────────────
        $period->update([
            'total_gross'      => collect($results)->sum('gross'),
            'total_net'        => collect($results)->sum('net_pay'),
            'total_allowances' => collect($results)->sum('gross'), // earnings before deductions
            'total_deductions' => collect($results)->sum('deductions'),
            'total_cost'       => collect($results)->sum('gross'), // adjust if employer contributions added
            'employee_count'   => count($results),
            'status'           => 'Processing',
            'updated_by'       => auth()->id(),
        ]);

        return $results;
    }

    public function lock(PayPeriod $period): void
    {
        if (!$period->entries()->exists()) {
            throw new RuntimeException('Cannot lock a period with no payroll entries.');
        }

        $period->update([
            'status'     => 'Locked_Completed',
            'updated_by' => auth()->id(),
        ]);
    }
}
