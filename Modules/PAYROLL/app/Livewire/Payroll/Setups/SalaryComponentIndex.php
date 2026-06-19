<?php

namespace Modules\PAYROLL\Livewire\Payroll\Setups;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\PAYROLL\Models\SalaryComponent;

class SalaryComponentIndex extends Component
{
    use WithPagination; // 

    // Use Bootstrap styling for pagination links
    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterType = '';
    public bool   $filterGlobal = false;

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
            'type'      => 'required|in:Earning,Deduction',
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
        $component = SalaryComponent::findOrFail($id);
        $this->editId    = $id;
        $this->name      = $component->name;
        $this->type      = $component->type;
        $this->is_global = $component->is_global;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'       => $this->name,
            'type'       => $this->type,
            'is_global'  => $this->is_global,
            'updated_by' => auth()->id(),
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
        $this->reset(['editId', 'name', 'type', 'is_global', 'showModal']);
        $this->type = 'Earning';
    }

    public function render()
    {
        // 4. Eager-loaded 'creator' relationship used by your template loop
        $components = SalaryComponent::query()
            // ->with('creator')
            ->when($this->search,       fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterType,   fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterGlobal, fn($q) => $q->where('is_global', true))
            ->withTrashed(false)
            ->latest()
            ->paginate(15);

        return view('payroll::livewire.payroll.setups.salary-component-index', compact('components'));
    }
}
