<?php

// Modules/PAYROLL/Services/PayrollProcessor.php

namespace Modules\PAYROLL\Services;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\HRM\Models\Employee;
use Modules\PAYROLL\Models\PayPeriod;
use Modules\PAYROLL\Models\PayrollEntry;
use Modules\PAYROLL\Models\PayrollEntryItem;
use Modules\PAYROLL\Models\SalaryComponent;
use RuntimeException;

class PayrollProcessor
{
    public function process(PayPeriod $period,bool $confirmed): array
    {
        if ($period->isLocked() || $period->isApproved()) {
            throw new RuntimeException('This pay period cannot be re-processed.');
        }

        $period->update([
            'status'       => 'Processing',
            'updated_by'   => auth()->id(),
        ]);

        // Load employees with finance profile + BOTH component sources:
        // - employeeComponents: assignments specific to this employee
        // - salaryComponents:   global components applied to all employees
        // No special ordering needed — every component computes against the
        // fixed base_salary, not a running total, so calculation order doesn't
        // affect the result.
        $employees = Employee::with([
            'financeProfile',
            'employeeComponents' => fn($q) => $q->with('component')->active(),
        ])
            ->where('is_active', 'active') // NOTE: confirm this column is string/enum, not boolean
            ->whereHas('financeProfile') // must have a finance profile
            ->get();

        if ($employees->isEmpty()) {
            throw new RuntimeException(
                'No active employees with finance profiles found.'
            );
        }

        // dd($employees);

        $results = [];

        DB::transaction(function () use ($employees, $period, &$results) {

            // Wipe previous draft entries for this period (bulk, not N+1)
            $entryIds = PayrollEntry::where('pay_period_id', $period->id)->pluck('id');

            if ($entryIds->isNotEmpty()) {
                PayrollEntryItem::whereIn('payroll_entry_id', $entryIds)->delete();
                PayrollEntry::whereIn('id', $entryIds)->delete();
            }

            foreach ($employees as $employee) {

                // ── 1. Base salary from Financial Profile ─────────────────────
                $baseSalary = (float) $employee->financeProfile->base_salary;

                // if ($baseSalary <= 0) {
                //     continue; // skip employees with no salary set
                // }

                // All percentage-based components compute against this fixed
                // figure — it must NOT be mutated inside the loops below.
                $grossBase = $baseSalary;

                $earningsTotal   = $baseSalary; // running total for reporting/net pay only
                $allowancesTotal = 0;           // earning components only, separate from base
                $deductionsTotal = 0;
                $lineItems       = [];

                // ── 2. Add base salary as a snapshot line item ─────────────────
                $lineItems[] = [

                    'component_id'            => null,
                    'component_name_snapshot' => 'Base Salary',
                    'finalized_amount'        => $baseSalary,
                    'item_type'               => 'Earning',
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ];



                // ── 3a. Employee-specific components ────────────────────────────
                foreach ($employee->employeeComponents as $ec) {
                    $comp   = $ec->component;
                    $amount = $ec->computeAmount($grossBase); // fixed base, not running total

                    // dd($comp->type);

                    // dd($comp->type);

                    if ($comp->type === 'Earning') {
                        $earningsTotal   += $amount;
                        $allowancesTotal += $amount;
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


                // Eager-load once before the loop to avoid N+1 on ->component and ->appliesTo
                $globalComponents = SalaryComponent::whereHas('component')
                    ->with(['component', 'appliesTo'])
                    ->where('is_global', true)
                    // ->where('is_active', true)
                    ->get();
                // dd($globalComponents);
                foreach ($globalComponents as $gc) {
                    // null applies_to => applies to everyone. Otherwise must match employee's type.
                    if ($gc->applies_to !== null && $gc->applies_to !== $employee->employment_type_id) {
                        continue;
                    }

                    // dd($grossBase);

                    $amount = match (true) {
                        $gc->isPaye()                          => SalaryComponent::calculatePaye($grossBase),
                        $gc->calculation_type === 'percentage' => round($grossBase * ((float) $gc->percentage_value / 100), 2),
                        default                                 => (float) $gc->amount,
                    };

                    // dd($amount);

                    if ($gc->component->type === 'Earning') {
                        $earningsTotal   += $amount;
                        $allowancesTotal += $amount;
                    } else {
                        $deductionsTotal += $amount;
                    }
                    // dd($lineItems);

                    $lineItems[] = [
                        'component_id'            => $gc->component?->id ?? $gc->component_id,
                        'component_name_snapshot' => $gc->component?->name ?? $gc->id,
                        'item_type' => $gc->component?->type,
                        'finalized_amount'        => $amount,
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ];
                }


                // ── 4. Total Gross = base + all earning components ─────────────
                $totalGross = $grossBase;
                $netPay       = $grossBase - $deductionsTotal;
                // ── 5. Create payroll entry ────────────────────────────────────
                $entry = PayrollEntry::create([
                    'employee_id'      => $employee->id,
                    'pay_period_id'    => $period->id,
                    'total_gross'      => $totalGross,
                    'total_deductions' => $deductionsTotal,
                    'total_allowances' => $allowancesTotal, // earning components only, not full gross
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
                    'employment_type' => $employee->employmentType?->name ?? 'permanent',
                    'base_salary'     => $baseSalary,
                    'allowances'      => $allowancesTotal,
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
            'total_allowances' => collect($results)->sum('allowances'),
            'total_deductions' => collect($results)->sum('deductions'),
            'total_cost'       => collect($results)->sum('gross'), // adjust if employer contributions added
            'employee_count'   => count($results),
            'status'           => 'Processed',
            'updated_by'       => auth()->id(),
        ]);

        $period->is_confirmed = $confirmed;
        $period->save();



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
