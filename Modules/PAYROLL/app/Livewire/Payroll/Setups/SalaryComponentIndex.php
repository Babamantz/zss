<?php

namespace Modules\PAYROLL\Livewire\Payroll\Setups;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\HRM\Models\EmploymentType;
use Modules\PAYROLL\Enums\AppliesTo;
use Modules\PAYROLL\Enums\CalculationType;
use Modules\PAYROLL\Enums\ComponentType;
use Modules\PAYROLL\Models\Component as PayComponent;
use Modules\PAYROLL\Models\SalaryComponent;



class SalaryComponentIndex extends Component
{
    use WithPagination; // 

    // Use Bootstrap styling for pagination links
    protected $paginationTheme = 'bootstrap';

    public string $calculation_type    = '';
    public  $applies_to          = null;
    public        $custom_amount       = '';
    public        $percentage_value    = '';
    public string $filterCalculationType = '';
    public string $filterGlobal        = '';
    public string $search = '';
    public string $filterType = '';

    // Create / Edit modal state
    public bool   $showModal  = false;
    public ?int   $editId     = null;
    public ?int   $componentNameId     = null;
    public string $name       = '';
    public string $type       = 'Earning';
    public bool   $is_global  = false;

    // 3. Reset page numbers whenever search filters change
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterType(): void
    {
        $this->resetPage();
    }
    public function updatingFilterGlobal(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            // 'name'      => 'required|string|max:100|unique:salary_components,name,' . ($this->editId ?? 'NULL'),
            'type'             => 'required|string|in:' . implode(',', ComponentType::ALL),
            'calculation_type' => 'required|string|in:' . implode(',', CalculationType::ALL),
            'applies_to'       => 'nullable|integer',
            'is_global' => 'boolean',
        ];
    }

    public function openCreate(): void
    {
        $this->resetModal();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetErrorBag();
        $component = SalaryComponent::findOrFail($id);

        $this->editId             = $component->id;
        $this->componentNameId    = $component->component_id;
        $this->calculation_type   = $component->calculation_type;
        $this->is_global          = (bool) $component->is_global;
        $this->applies_to         = $component->applies_to;
        $this->custom_amount      = $component->amount ?? '';
        $this->percentage_value   = $component->percentage_value ?? '';
        $this->showModal          = true;
    }

    public function save(): void
    {
        $this->validate();

        // dd($this->componentNameId);

        $data = [
            // 'name'                => $this->name,
            'component_id'    => $this->componentNameId,
            'calculation_type'    => $this->calculation_type,
            'is_global'           => $this->is_global,
            'applies_to'          => $this->applies_to,
            'amount'       => $this->calculation_type === CalculationType::FIXED
                ? $this->custom_amount : null,
            'percentage_value'    => $this->calculation_type === CalculationType::PERCENTAGE
                ? $this->percentage_value : null,
            'updated_by'          => auth()->id(),
        ];



        if ($this->editId) {
            SalaryComponent::findOrFail($this->editId)->update($data);
        } else {
            SalaryComponent::create(array_merge($data, ['created_by' => auth()->id()]));
        }

        $this->resetModal();
        session()->flash('success', 'Salary component saved.');
    }

    public function delete(int $id): void
    {
        SalaryComponent::findOrFail($id)->delete();
        session()->flash('success', 'Component removed.');
    }


    public function resetModal(): void
    {
        $this->reset([
            'editId',
            'componentNameId',
            'applies_to',
            'calculation_type',
            'custom_amount',
            'percentage_value',
            'showModal',
        ]);
        $this->applies_to = null;
    }

    // Add to component:
    public function clearFilters(): void
    {
        $this->reset(['search', 'filterType', 'filterGlobal', 'filterCalculationType']);
        $this->resetPage();
    }
    public function render()
    {
        $components = PayComponent::pluck('name', 'id');
        $appliesTo  = EmploymentType::pluck('name', 'id');

        $salary_components = SalaryComponent::query()
            ->with(['component', 'appliesTo'])
            ->when(
                $this->search,
                fn($q) => $q->whereHas('component', fn($cq) => $cq->where('name', 'like', "%{$this->search}%"))
            )
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when(
                $this->filterCalculationType,
                fn($q) => $q->where('calculation_type', $this->filterCalculationType)
            )
            ->withTrashed(false)
            ->latest()
            ->paginate(15);

        // One grouped query for Earning/Deduction counts instead of N separate ->count() calls
        // $typeCounts = SalaryComponent::selectRaw('type, COUNT(*) as count')
        //     ->groupBy('type')
        //     ->pluck('count', 'type');

        $statCards = [
            [
                'label' => 'Total Components',
                'value' => $salary_components->total(),
                'icon'  => 'fa-sliders',
                'color' => 'primary',
                'sub'   => 'all configured',
            ],
            [
                'label' => 'Earnings',
                'value' => $typeCounts[ComponentType::EARNING] ?? 0,
                'icon'  => 'fa-arrow-up-circle',
                'color' => 'success',
                'sub'   => 'earning components',
            ],
            [
                'label' => 'Deductions',
                'value' => $typeCounts[ComponentType::DEDUCTION] ?? 0,
                'icon'  => 'fa-arrow-down-circle',
                'color' => 'danger',
                'sub'   => 'deduction components',
            ],
            [
                'label' => 'Global Components',
                'value' => SalaryComponent::where('is_global', true)->count(),
                'icon'  => 'fa-globe',
                'color' => 'info',
                'sub'   => 'applied to gross salary',
            ],
        ];

        return view(
            'payroll::livewire.payroll.setups.salary-component-index',
            compact('salary_components', 'components', 'appliesTo', 'statCards')
        );
    }


    // public function render()
    // {
    //     // 4. Eager-loaded 'creator' relationship used by your template loop
    //     $components = PayComponent::pluck('name', 'id');
    //     $appliesTo = EmploymentType::pluck('name','id');
    //     // dd($appliesTo);
    //     $salary_components = SalaryComponent::query()
    //         // ->with('creator')
    //         ->when($this->search,       fn($q) => $q->where('name', 'like', "%{$this->search}%"))
    //         ->when($this->filterType,   fn($q) => $q->where('type', $this->filterType))
    //         ->when(
    //             $this->filterCalculationType,
    //             fn($q) =>
    //             $q->where('calculation_type', $this->filterCalculationType)
    //         )

    //         ->withTrashed(false)
    //         ->latest()
    //         ->paginate(15);

    //     return view('payroll::livewire.payroll.setups.salary-component-index', compact('salary_components', 'components','appliesTo'));
    // }
}
