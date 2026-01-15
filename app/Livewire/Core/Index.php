<?php

namespace App\Livewire\Core;

use Livewire\Component;

class Index extends Component
{
    public function mount()
    {
        session(['module' => 'general']);
    }
    public function render()
    {
        return view('livewire.core.index');
    }
}
