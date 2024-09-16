<?php

namespace App\Filament\Resources\GatePassResource\Pages;

use App\Filament\Resources\GatePassResource;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewGatePass extends Page implements HasForms
{
    use InteractsWithRecord;

    protected static string $resource = GatePassResource::class;

    protected static string $view = 'filament.resources.gate-pass-resource.pages.view-gate-pass';


    public function mount(int | string $record): void
    {
        // dd($record);
        $this->record = $this->resolveRecord($record);
    }

}
