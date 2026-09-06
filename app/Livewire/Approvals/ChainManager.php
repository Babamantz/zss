<?php

namespace App\Livewire\Approvals;


use App\Models\ApprovalChain;
use Livewire\Component;

class ChainManager extends Component
{
    public $chains;

    public string $name = '';
    public string $module = '';
    public bool $is_active = true;
    public bool $showForm = false;

    public function mount()
    {
        $this->loadChains();
    }

    public function loadChains()
    {
        $this->chains = ApprovalChain::withCount('steps')->latest()->get();
    }

    public function createChain()
    {
        $this->validate([
            'name'   => 'required|string|max:255',
            'module' => 'nullable|string|max:100',
        ]);

        ApprovalChain::create([
            'name'      => $this->name,
            'module'    => $this->module,
            'is_active' => $this->is_active,
        ]);

        $this->reset(['name', 'module', 'showForm']);
        $this->is_active = true;
        $this->loadChains();
        session()->flash('success', 'Chain created.');
    }

    public function toggleActive(int $chainId)
    {
        $chain = ApprovalChain::findOrFail($chainId);
        $chain->update(['is_active' => !$chain->is_active]);
        $this->loadChains();
    }

    public function render()
    {
        return view('livewire.Approvals.chain-manager');
    }
}
