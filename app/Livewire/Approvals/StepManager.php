<?php

// app/Livewire/Approvals/StepManager.php
namespace App\Livewire\Approvals;

use App\Models\ApprovalChain;
use App\Models\ApprovalChainStep;
use Illuminate\Validation\Rule;
use Livewire\Component;

class StepManager extends Component
{
    public ApprovalChain $chain;
    public $steps;

    public ?int $editingStepId = null;

    public string $name = '';
    public int $order = 1;
    public string $approver_type = 'role';
    public string $approver_value = '';
    public bool $requires_all = false;
    public bool $showForm = false;

    public function mount(ApprovalChain $chain)
    {
        $this->chain = $chain;
        $this->order = $chain->steps()->max('order') + 1;
        $this->loadSteps();
    }

    public function loadSteps()
    {
        $this->steps = $this->chain->steps()->orderBy('order')->get();
    }

    protected function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'order'          => [
                'required',
                'integer',
                'min:1',
                Rule::unique('approval_chain_steps', 'order')
                    ->where('approval_chain_id', $this->chain->id)
                    ->ignore($this->editingStepId),
            ],
            'approver_type'  => 'required|in:role,user,department_head',
            'approver_value' => 'required|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'order.unique' => 'Another step in this chain already uses that order.',
        ];
    }

    public function addStep()
    {
        $this->validate();

        $this->chain->steps()->create([
            'name'           => $this->name,
            'order'          => $this->order,
            'approver_type'  => $this->approver_type,
            'approver_value' => $this->approver_value,
            'requires_all'   => $this->requires_all,
        ]);

        $this->resetForm();
        $this->loadSteps();
    }

    public function editStep(int $stepId)
    {
        $step = $this->chain->steps()->findOrFail($stepId);

        $this->editingStepId = $step->id;
        $this->name = $step->name;
        $this->order = $step->order;
        $this->approver_type = $step->approver_type->value;
        $this->approver_value = $step->approver_value;
        $this->requires_all = $step->requires_all;
        $this->showForm = true;
    }

    public function updateStep()
    {
        $this->validate();

        $step = $this->chain->steps()->findOrFail($this->editingStepId);

        $step->update([
            'name'           => $this->name,
            'order'          => $this->order,
            'approver_type'  => $this->approver_type,
            'approver_value' => $this->approver_value,
            'requires_all'   => $this->requires_all,
        ]);

        $this->resetForm();
        $this->loadSteps();
    }

    public function deleteStep(int $stepId)
    {
        $this->chain->steps()->findOrFail($stepId)->delete();
        $this->loadSteps();
    }

    public function cancelForm()
    {
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset(['name', 'approver_value', 'requires_all', 'showForm', 'editingStepId']);
        $this->approver_type = 'role';
        $this->order = $this->chain->steps()->max('order') + 1;
    }

    public function render()
    {
        return view('livewire.approvals.step-manager');
    }
}
