<?php

namespace Modules\PAYROLL\Livewire\Payroll\Setups;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\PAYROLL\Enums\AppliesTo;
use Modules\PAYROLL\Enums\CalculationType;
use Modules\PAYROLL\Enums\ComponentType;
use Modules\PAYROLL\Models\SalaryComponent;

class SalaryComponentIndex extends Component
{
    use WithPagination; // 

    // Use Bootstrap styling for pagination links
    protected $paginationTheme = 'bootstrap';

    public string $calculation_type    = '';
    public string $applies_to          = 'all';
    public        $custom_amount       = '';
    public        $percentage_value    = '';
    public string $filterCalculationType = '';
    public string $filterGlobal        = '';
    public string $search = '';
    public string $filterType = '';

    // Create / Edit modal state
    public bool   $showModal  = false;
    public ?int   $editId     = null;
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
            'name'      => 'required|string|max:100|unique:salary_components,name,' . ($this->editId ?? 'NULL'),
            'type'             => 'required|string|in:' . implode(',', ComponentType::ALL),
            'calculation_type' => 'required|string|in:' . implode(',', CalculationType::ALL),
            'applies_to'       => 'required|string|in:' . implode(',', AppliesTo::OPTIONS),
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
        $this->name               = $component->name;
        $this->type               = $component->type;
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

        $data = [
            'name'                => $this->name,
            'type'                => $this->type,
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
            'name',
            'type',
            'applies_to',
            'calculation_type',
            'custom_amount',
            'percentage_value',
            'showModal',
        ]);
        $this->type       = ComponentType::EARNING;
        $this->applies_to = AppliesTo::ALL;
    }

    // Add to component:
    public function clearFilters(): void
    {
        $this->reset(['search', 'filterType', 'filterGlobal', 'filterCalculationType']);
        $this->resetPage();
    }


    public function render()
    {
        // 4. Eager-loaded 'creator' relationship used by your template loop
        $components = SalaryComponent::query()
            // ->with('creator')
            ->when($this->search,       fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterType,   fn($q) => $q->where('type', $this->filterType))
            ->when(
                $this->filterCalculationType,
                fn($q) =>
                $q->where('calculation_type', $this->filterCalculationType)
            )

            ->withTrashed(false)
            ->latest()
            ->paginate(15);

        return view('payroll::livewire.payroll.setups.salary-component-index', compact('components'));
    }
}
