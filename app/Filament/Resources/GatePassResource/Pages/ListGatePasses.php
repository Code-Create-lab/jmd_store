<?php

namespace App\Filament\Resources\GatePassResource\Pages;

use App\Filament\Resources\GatePassResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGatePasses extends ListRecords
{
    protected static string $resource = GatePassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
