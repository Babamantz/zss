<?php

namespace Modules\PAYROLL\Livewire\Payroll;

use Livewire\Component;
use Modules\PAYROLL\Models\PayPeriod;
use Modules\PAYROLL\Services\PayrollProcessor;

// Modules/HRM/Livewire/Payroll/RunPayroll.php
class RunPayroll extends Component
{
    public ?int   $pay_period_id = null;
    public bool   $confirmed     = false;
    public array  $results       = [];
    public bool   $processed     = false;
 
    protected function rules(): array
    {
        return [
            'pay_period_id' => 'required|exists:pay_periods,id',
        ];
    }

    public function process(): void
    {
        // dd(request()->all());
        
        $this->validate();

        $period = PayPeriod::findOrFail($this->pay_period_id);

        try {
            $processor    = new PayrollProcessor();
            $this->results    = $processor->process($period);
            $this->processed  = true;
            session()->flash('success', count($this->results) . ' employees processed.');
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function lock(): void
    {
        $period = PayPeriod::findOrFail($this->pay_period_id);

        try {
            (new PayrollProcessor())->lock($period);
            session()->flash('success', 'Pay period locked successfully.');
            $this->reset(['pay_period_id', 'results', 'processed', 'confirmed']);
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }


    public function render()
    {
        $periods = PayPeriod::whereIn('status', ['Draft', 'Processing'])
            ->orderByDesc('start_date')
            ->get();
        return view('payroll::livewire.payroll.run-payroll', compact(
            'periods'
        ));
    }
}
