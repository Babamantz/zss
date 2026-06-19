<?php

namespace Modules\PAYROLL\Livewire\Payroll\PayPeriod;

use Livewire\Component;
use Modules\PAYROLL\Models\PayPeriod;

// Modules/HRM/Livewire/Payroll/PayPeriods/PayPeriodIndex.php
class PayPeriodIndex extends Component
{
    public string $filterStatus = '';
    public bool   $showModal    = false;
    public ?int   $editId       = null;
    public string $start_date   = '';
    public string $end_date     = '';
    public string $status       = 'Draft';

    protected function rules(): array
    {
        return [
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'status'     => 'required|in:Draft,Processing,Locked_Completed',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editId', 'start_date', 'end_date']);
        $this->status    = 'Draft';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $period = PayPeriod::findOrFail($id);

        // Prevent editing locked periods
        if ($period->isLocked()) {
            session()->flash('error', 'Locked pay periods cannot be edited.');
            return;
        }

        $this->editId     = $id;
        $this->start_date = $period->start_date->format('Y-m-d');
        $this->end_date   = $period->end_date->format('Y-m-d');
        $this->status     = $period->status;
        $this->showModal  = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'status'     => $this->status,
            'updated_by' => auth()->id(),
        ];

        if ($this->editId) {
            PayPeriod::findOrFail($this->editId)->update($data);
        } else {
            PayPeriod::create(array_merge($data, ['created_by' => auth()->id()]));
        }

        $this->reset(['showModal', 'editId', 'start_date', 'end_date']);
        $this->status = 'Draft';
        session()->flash('success', 'Pay period saved.');
    }

    public function render()
    {
        $periods = PayPeriod::query()
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest('start_date')
            ->paginate(12);

        return view('payroll::livewire.payroll.pay-period.pay-period-index', compact('periods'));
    }
}
