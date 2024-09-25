<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\GatePassResource;
use App\Models\GatePass;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class GatePassList extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->striped()
            ->query(GatePassResource::getEloquentQuery())
            ->columns([
                TextColumn::make('product.name')
                    ->searchable(isIndividual: true)
                    ->label('Attached Product')
                    ->weight(FontWeight::Bold)
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->wrap()
                    ->placeholder('No Attached Product.'),
                TextColumn::make('product.date')
                    ->since()
                    // ->description(fn (GatePass $record): string => $record->date)
                    // ->dateTimeTooltip()
                    ->label('Added Date')
                    // ->weight(FontWeight::Bold)
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->wrap(),

                TextColumn::make('product.box')
                    ->label('Total Boxes')
                    // ->weight(FontWeight::Bold)
                    ->listWithLineBreaks()
                    ->badge()
                    ->wrap(),
                Tables\Columns\TextColumn::make('box')
                    ->label('Boxes'),
                Tables\Columns\TextColumn::make('slip_no')
                    ->searchable(),


                Tables\Columns\TextColumn::make('total_amount'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Pass Date')
                    ->date()
                    ->sortable(),
                // Tables\Columns\IconColumn::make('status')
                //     ->boolean(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_amount')
                    ->summarize(Sum::make()->label('Total Amount')->money('INR', locale: 'nl')),
                TextColumn::make('box')

                    ->summarize(Sum::make()->label('Total Box')),
            ]);
    }
}
