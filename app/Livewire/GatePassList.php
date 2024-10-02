<?php

namespace App\Livewire;

use App\Filament\Resources\GatePassResource;
use App\Models\GatePass;
use Livewire\Component;

class GatePassList extends Component
{
    // Define public properties
    public $gatepass;
    public $message = '';

    // Mount method to set default data
    public function mount()
    {
        // Set values to properties
        $this->gatepass = GatePass::all();
    }

    public function calculateAmount()
    {
        $this->message = 'Keydown triggered!';
    }

    public function render()
    {
        return view('livewire.gate-pass-list');
    }
}
