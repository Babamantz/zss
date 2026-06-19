<?php

namespace Modules\PAYROLL\Services;

use Illuminate\Support\Facades\DB;
use Modules\HRM\Models\Employee;
use Modules\PAYROLL\Models\PayPeriod;
use Modules\PAYROLL\Models\PayrollEntry;

class PayrollProcessor
{
    public function process(PayPeriod $period): array
    {
        if ($period->isLocked()) {
            throw new \RuntimeException('This pay period is already locked.');
        }

        // Mark period as processing
        $period->update(['status' => 'Processing', 'updated_by' => auth()->id()]);

        $employees = Employee::with([
            'components' => fn($q) => $q->with('component')->active(),
            'financeProfile',
        ])->where('is_active', 'active')->get();

        $results = [];

        DB::transaction(function () use ($employees, $period, &$results) {

            // Wipe any previous draft entries for this period
            PayrollEntry::where('pay_period_id', $period->id)->each(function ($e) {
                $e->items()->delete();
                $e->delete();
            });

            foreach ($employees as $employee) {
                $earningsTotal   = 0;
                $deductionsTotal = 0;
                $lineItems       = [];

                foreach ($employee->components as $ec) {
                    $comp   = $ec->component;
                    $amount = $ec->custom_amount;

                    if ($comp->type === 'Earning') {
                        $earningsTotal += $amount;
                    } else {
                        $deductionsTotal += $amount;
                    }

                    $lineItems[] = [
                        'component_id'            => $comp->id,
                        'component_name_snapshot' => $comp->name,
                        'finalized_amount'        => $amount,
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ];
                }

                $netPay = $earningsTotal - $deductionsTotal;

                $entry = PayrollEntry::create([
                    'employee_id'      => $employee->id,
                    'pay_period_id'    => $period->id,
                    'total_gross'      => $earningsTotal,
                    'total_deductions' => $deductionsTotal,
                    'net_pay'          => $netPay,
                    'processed_at'     => now(),
                    'processed_by'     => auth()->id(),
                ]);

                // Bulk insert line items
                $entry->items()->createMany($lineItems);

                // In PayrollProcessor::process() — update the $results[] push:
                $results[] = [
                    'employee_id'   => $employee->id,
                    'employee_name' => $employee->user->first_name . ' ' . $employee->user->last_name,
                    'opf_number'    => $employee->opf_number ?? '—',
                    'gross'         => $earningsTotal,
                    'deductions'    => $deductionsTotal,
                    'net_pay'       => $netPay,
                ];

                // $results[] = [
                //     'employee_id' => $employee->id,
                //     'net_pay'     => $netPay,
                // ];
            }
        });

        return $results;
    }

    public function lock(PayPeriod $period): void
    {
        if (!$period->entries()->exists()) {
            throw new \RuntimeException('Cannot lock a period with no payroll entries.');
        }

        $period->update([
            'status'     => 'Locked_Completed',
            'updated_by' => auth()->id(),
        ]);
    }
}
