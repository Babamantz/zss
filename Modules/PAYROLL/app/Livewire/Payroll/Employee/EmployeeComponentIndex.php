<?php

namespace Modules\PAYROLL\Livewire\Payroll\Employee;

use Livewire\Component;
use Modules\HRM\Models\Employee;
use Modules\PAYROLL\Models\EmployeeComponent;
use Modules\PAYROLL\Models\SalaryComponent;

class EmployeeComponentIndex extends Component
{
    // public string $search     = '';
    // public ?int   $employeeId = null; // optional — filter by one employee

    // public bool   $showModal    = false;
    // public ?int   $editId       = null;
    // public ?int   $emp_id       = null;
    // public ?int   $component_id = null;
    // public string $custom_amount = '';
    // public bool   $is_recurring = false;
    // public string $ends_at      = '';

    public string $search     = '';
    public ?int   $employeeId = null;

    public bool   $showModal    = false;
    public ?int   $editId       = null;
    public ?int   $emp_id       = null;
    public ?int   $component_id = null;

    // CHANGED: Removed strict string types to prevent mapping crashes with numeric/null values
    public $custom_amount = '';
    public bool   $is_recurring = false;
    public $ends_at      = '';

    protected function rules(): array
    {
        return [
            'emp_id'        => 'required|exists:employees,id',
            'component_id'  => 'required|exists:salary_components,id',
            'custom_amount' => 'required|numeric|min:0',
            'is_recurring'  => 'boolean',
            'ends_at'       => 'nullable|date|after:today',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'employee_id'   => $this->emp_id,
            'component_id'  => $this->component_id,
            'custom_amount' => $this->custom_amount,
            'is_recurring'  => $this->is_recurring,
            'ends_at'       => $this->ends_at ?: null,
            'updated_by'    => auth()->id(),
        ];

        if ($this->editId) {
            EmployeeComponent::findOrFail($this->editId)->update($data);
        } else {
            EmployeeComponent::create(array_merge($data, ['created_by' => auth()->id()]));
        }

        $this->reset(['showModal', 'editId', 'emp_id', 'component_id', 'custom_amount', 'ends_at']);
        session()->flash('success', 'Employee component saved.');
    }

    public function openEdit(int $id): void
    {
        // 1. Clear any leftover property values or validation errors
        $this->resetErrorBag();

        // 2. Locate the database record or fail gracefully
        $item = EmployeeComponent::findOrFail($id); // Adjust model namespace if inside a Module

        // 3. Map properties safely with clear data type casting
        $this->editId        = (int) $item->id;
        $this->emp_id        = (int) $item->employee_id;
        $this->component_id  = (int) $item->component_id;
        $this->custom_amount = $item->custom_amount;
        $this->is_recurring  = (bool) $item->is_recurring;

        // Handle dates carefully to ensure standard HTML date inputs recognize the string format
        $this->ends_at       = $item->ends_at ? \Carbon\Carbon::parse($item->ends_at)->format('Y-m-d') : '';

        // 4. Open the modal
        $this->showModal = true;
    }

    public function resetModal(): void
    {
        $this->reset(['editId', 'emp_id', 'component_id', 'custom_amount', 'ends_at', 'showModal']);
        $this->is_recurring = false;
    }


    public function render()
    {
        $items = EmployeeComponent::with(['employee.user', 'component'])
            ->when($this->employeeId, fn($q) => $q->where('employee_id', $this->employeeId))
            ->when($this->search, fn($q) => $q->whereHas(
                'component',
                fn($q2) => $q2->where('name', 'like', "%{$this->search}%")
            ))
            ->active()
            ->latest()
            ->paginate(15);

        $employees  = Employee::with('user')->get();
        $components = SalaryComponent::orderBy('type')->orderBy('name')->get();

        return view(
            'payroll::livewire.payroll.employee.employee-component-index',

            compact('items', 'employees', 'components')
        );
    }
}
