<?php

namespace App\Filament\Tables\Actions;

use Filament\Tables\Actions\Action;

class SummarizedAction extends Action
{
    public static function make(?string $name = null): static
    {
        return (new static($name))
            ->color('primary') // Customize the button color
            ->label('Summary Action') // Customize the action label
            ->action(fn () => static::performAction()); // Define action behavior
    }

    protected static function performAction()
    {
        // Your action logic here
    }
}
