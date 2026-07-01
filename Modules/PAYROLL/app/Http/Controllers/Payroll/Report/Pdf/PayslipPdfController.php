<?php

namespace Modules\PAYROLL\Http\Controllers\Payroll\Report\Pdf;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\PAYROLL\Models\PayrollEntry;

class PayslipPdfController extends Controller
{
    public function view(int $entry)
    {
        $entry = PayrollEntry::with([
            'employee.user',
            'employee.department',
            'employee.unit',
            'employee.financeProfile',
            'payPeriod',
            'items',
            // 'processedBy',
        ])->findOrFail($entry);

        $earnings = $entry->items
            ->filter(fn($i) => $i->item_type === 'Earning')
            ->sortByDesc('finalized_amount');

        $deductions = $entry->items
            ->filter(fn($i) => $i->item_type === 'Deduction')
            ->sortByDesc('finalized_amount');

        $pdf = Pdf::loadView('payroll::payroll.reports.pdf.payslip', [
            'entry'      => $entry,
            'earnings'   => $earnings,
            'deductions' => $deductions,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream(
            'Payslip-' . ($entry->employee->opf_number ?? $entry->employee_id)
                . '-' . $entry->payPeriod->start_date->format('M-Y') . '.pdf'
        );
    }
}
