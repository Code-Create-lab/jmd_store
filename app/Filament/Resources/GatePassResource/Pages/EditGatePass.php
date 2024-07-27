<?php

namespace App\Filament\Resources\GatePassResource\Pages;

use App\Filament\Resources\GatePassResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGatePass extends EditRecord
{
    protected static string $resource = GatePassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
