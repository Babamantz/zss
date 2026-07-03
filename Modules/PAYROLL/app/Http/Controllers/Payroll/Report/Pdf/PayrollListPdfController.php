<?php

namespace Modules\PAYROLL\Http\Controllers\Payroll\Report\Pdf;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PAYROLL\Models\PayPeriod;
use Modules\PAYROLL\Models\PayrollEntry;
use Barryvdh\DomPDF\Facade\Pdf;


class PayrollListPdfController extends Controller
{
    public function view(int $periodId)
    {
        $period = PayPeriod::findOrFail($periodId);

        $entries = PayrollEntry::with([
            'employee.user',
            'employee.financeProfile',
            'employee.bankAccount.bank'
        ])
            ->where('pay_period_id', $period->id)
            ->get()
            ->groupBy(function ($entry) {
                return $entry->employee?->bankAccount?->bank?->slug ?? '—';
            })
            ->map(function ($group) {
                return $group->map(function ($entry) {
                    return [
                        'name' => $entry->employee->user->first_name . ' ' . $entry->employee->user->last_name,
                        'opf_number' => $entry->employee->opf_number ?? '—',
                        'account_no' => $entry->employee->bankAccount->account_no ?? '—',
                        'bank' => $entry->employee?->bankAccount?->bank?->slug ?? '—',
                        'net_pay' => $entry->net_pay,
                    ];
                });
            });

        $totalNet = $entries->sum('net_pay');

        $pdf = Pdf::loadView('payroll::payroll.reports.pdf.payroll-list', [
            'period'    => $period,
            'entries'   => $entries,
            'totalNet'  => $totalNet,
        ])->setPaper('a4', 'portrait');

        // 'stream' opens inline in the browser — viewable, not forced download
        return $pdf->stream("Payroll-List-{$period->start_date->format('M-Y')}.pdf");
    }
}
