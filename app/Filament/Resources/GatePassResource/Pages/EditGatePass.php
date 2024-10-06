<?php

namespace App\Filament\Resources\GatePassResource\Pages;

use App\Filament\Resources\GatePassResource;
use Filament\Actions;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\EditRecord;

class EditGatePass extends EditRecord
{
    protected static string $resource = GatePassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ButtonAction::make('create')
                ->label('New gate pass')
                ->url(fn() => $this->getResource()::getUrl('create')), // Redirect to create page
                // ->icon('heroicon-o-plus'),
            Actions\DeleteAction::make(),
        ];
    }
}
