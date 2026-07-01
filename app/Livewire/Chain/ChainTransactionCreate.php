<?php

namespace App\Livewire\Chain;

use App\Models\ChainModule;
use App\Services\ChainWorkflowService;
use Livewire\Component;

class ChainTransactionCreate extends Component
{
    public string $moduleCode   = '';
    public string $title        = '';
    public string $description  = '';

    protected function rules(): array
    {
        return [
            'moduleCode'  => 'required|string|exists:chain_modules,code',
            'title'       => 'required|string|min:5|max:150',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        try {
            $tx = (new ChainWorkflowService())->create(
                $this->moduleCode,
                $this->title,
                $this->description ?: null
            );

            session()->flash(
                'success',
                "Transaction {$tx->reference_no} created and submitted for approval."
            );

            $this->reset(['title', 'description', 'moduleCode']);
            $this->dispatch('transaction-created', id: $tx->id);
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.chain.chain-transaction-create', [
            'modules' => ChainModule::where('is_active', true)
                ->orderBy('name')->get(),
        ]);
    }
    
}
