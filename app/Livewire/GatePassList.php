<?php

namespace App\Livewire;

use App\Filament\Resources\GatePassResource;
use App\Models\GatePass;
use Livewire\Component;

class GatePassList extends Component
{
    // Define public properties
    public $gatepass;
    public $gst = "";
    public $totalAmount;
    public $totalAmountGST;
    public $message = '';

    // Mount method to set default data
    public function mount()
    {
        // Set values to properties
        $this->gatepass = GatePass::all();
         $this->totalAmount  =collect($this->gatepass)->sum('total_amount');
    }

    public function calculateAmount()
    {
        // dd('asdasd');
        $this->gst = $this->gst;
        $this->totalAmountGST = ($this->totalAmount * ($this->gst/100) )+ $this->totalAmount;
    }

    public function render()
    {
        return view('livewire.gate-pass-list');
    }
}
