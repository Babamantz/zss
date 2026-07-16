<?php

// Modules/PAYROLL/Livewire/Payroll/RunPayroll.php

namespace Modules\PAYROLL\Livewire\Payroll;

use App\Enums\ChainStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\PAYROLL\Models\PayPeriod;
use Modules\PAYROLL\Models\PayrollEntry;
use Modules\PAYROLL\Services\PayrollProcessor;


class RunPayroll extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $filterStatus = '';
    public string $search       = '';

    // ── Selected period (for modal) ───────────────────────────────────────────
    public ?int  $selectedPeriodId = null;
    public bool  $showModal        = false;

    // ── Process state ─────────────────────────────────────────────────────────
    public bool  $confirmed  = false;
    public array $results    = [];
    public bool  $processed  = false;
    public bool  $status  = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    // ── Open period modal ─────────────────────────────────────────────────────

    public function openPeriod(int $id): void
    {
        $this->selectedPeriodId = $id;

        $this->results          = [];
        $this->showModal        = true;

        $period = PayPeriod::find($id);
        $this->confirmed        = (bool) $period->is_confirmed;
        $this->status        = $period->status;
        // dd($period);
        // If already processed — load existing entries for display
        if ($period && $period->status === 'Processing') {
            $this->loadExistingResults($period);
        }
    }

    // ── Load existing payroll entries into $results for display ───────────────

    protected function loadExistingResults(PayPeriod $period): void
    {
        // dd($period);
        $this->results = PayrollEntry::with(['employee.user'])
            ->where('pay_period_id', $period->id)
            ->get()
            ->map(fn($entry) => [
                'employee_id'     => $entry->employee_id,
                'employee_name'   => $entry->employee->user->first_name
                    . ' ' . $entry->employee->user->last_name,
                'opf_number'      => $entry->employee->opf_number ?? '—',
                'tenant'          => $entry->employee->user->tenant?->name ?? '—',
                'employment_type' => $entry->employee->employment_type ?? 'permanent',
                'base_salary'     => (float) PayrollEntry::getBaseSalaryForEntry($entry),
                'gross'           => (float) $entry->total_gross,
                'deductions'      => (float) $entry->total_deductions,
                'net_pay'         => (float) $entry->net_pay,
            ])
            ->toArray();

        $this->processed = count($this->results) > 0;
    }


    public function process(): void
    {
        $this->validate(['selectedPeriodId' => 'required|exists:pay_periods,id']);

        $period = PayPeriod::findOrFail($this->selectedPeriodId);


        
        try {
            $this->results   = (new PayrollProcessor())->process($period,$this->confirmed);
            $this->processed = true;
            $period->is_confirmed = $this->confirmed;
            $period->save();
            $period->refresh();
            session()->flash('success', count($this->results) . ' employees processed successfully.');
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    // ── Lock ──────────────────────────────────────────────────────────────────

    public function lock(): void
    {
        $period = PayPeriod::findOrFail($this->selectedPeriodId);

        try {
            (new PayrollProcessor())->lock($period);
            $this->closeModal();
            session()->flash('success', 'Pay period locked and completed.');
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    // ── Close modal ───────────────────────────────────────────────────────────

    public function closeModal(): void
    {
        $this->reset([
            'selectedPeriodId',
            'showModal',
            'confirmed',
            'results',
            'processed',
        ]);
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $periods = PayPeriod::query()
            ->when(
                $this->filterStatus,
                fn($q) => $q->where('status', $this->filterStatus)
            )
            ->when(
                $this->search,
                fn($q) => $q->where(
                    fn($w) =>
                    $w->whereYear('start_date',  'like', "%{$this->search}%")
                        ->orWhereMonth('start_date', 'like', "%{$this->search}%")
                )
            )
            ->orderByDesc('start_date')
            ->paginate(15);

        $selectedPeriod = $this->selectedPeriodId
            ? PayPeriod::find($this->selectedPeriodId)
            : null;

        return view(
            'payroll::livewire.payroll.run-payroll',
            compact('periods', 'selectedPeriod')
        );
    }
}
