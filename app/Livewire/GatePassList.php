<?php

namespace App\Livewire;

use App\Filament\Resources\GatePassResource;
use App\Models\GatePass;
use Carbon\Carbon;
use Livewire\Component;

class GatePassList extends Component
{
    // Define public properties
    public $gatepass;
    public $gst = "";
    public $totalAmount;
    public $totalAmountGST;
    public $message = '';
    public $dateFrom;
    public $dateTo;

    // Mount method to set default data
    // public function mount()
    // {
    //     // Set values to properties
    //      $query = GatePass::with('product');

    //     if ($this->dateFrom) {
    //         $query->where('date', '>=', Carbon::parse($this->dateFrom));
    //     }

    //     if ($this->dateTo) {
    //         $query->where('date', '<=', Carbon::parse($this->dateTo));
    //     }

    //     $this->gatepass = $query->get();
    //     $this->totalAmount  = collect($this->gatepass)->sum('total_amount');
    // }

    public function mount()
    {
        $this->filterGatepassData();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['dateFrom', 'dateTo'])) {
            $this->filterGatepassData();
        }
    }

    public function filterGatepassData()
    {
        $query = Gatepass::with('product');

        if ($this->dateFrom) {
            $query->where('date', '>=', Carbon::parse($this->dateFrom));
        }

        if ($this->dateTo) {
            $query->where('date', '<=', Carbon::parse($this->dateTo));
        }

        $this->gatepass = $query->get();
        $this->totalAmount = $this->gatepass->sum('total_amount');
        $this->totalAmountGST = $this->totalAmount * 1.18; // Example GST
    }

    public function calculateAmount()
    {
        // dd('asdasd');
        $this->gst = $this->gst;
        $this->totalAmountGST = ($this->totalAmount * ($this->gst / 100)) + $this->totalAmount;
    }


    public function render()
    {
        return view('livewire.gate-pass-list');
    }
}
