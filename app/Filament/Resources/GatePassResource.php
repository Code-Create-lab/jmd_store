<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GatePassResource\Pages;
use App\Filament\Resources\GatePassResource\RelationManagers;
use App\Filament\Resources\GatePassResource\RelationManagers\GatePassRelationManager as RelationManagersGatePassRelationManager;
use App\Filament\Resources\GatePassResource\RelationManagers\ProductRelationManager;
use App\Filament\Resources\PassResource\RelationManagers\GatePassRelationManager;
use App\Models\GatePass;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GatePassResource extends Resource
{
    protected static ?string $model = GatePass::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('slip_no')
                    ->required()
                    ->maxLength(255),
                // Forms\Components\TextInput::make('box')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('total_amount')
                //     ->required()
                //     ->maxLength(255),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        // ->heading('Clients')
            ->poll('10s')
            ->deferLoading()
            ->striped()
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

            ])
            ->paginated([10, 25, 50, 100])
            ->filters([
                Filter::make('created_at')
                ->form([
                    DatePicker::make("created_at")
                        ->label('Created From'),
                    DatePicker::make("created_to")
                        ->label('Created To')
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['created_at'], fn(Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                        ->when($data['created_to'], fn(Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
                })
                ->indicateUsing(function (array $data): ?string {
                    if (!$data['created_at'] && !$data['created_to']) {
                        return null;
                    }
                    $indicator = '';

                    if ($data['created_at']) {
                        $indicator .= 'Created From: ' . $data['created_at'];
                    }
                    if ($data['created_to'] && $data['created_at']) {
                        $indicator .= ' To ' . $data['created_to'];
                    }
                    if ($data['created_to'] && !$data['created_at']) {
                        $indicator .= 'Created To ' . $data['created_to'];
                    }
                    return $indicator;
                }),
                SelectFilter::make('product_id')
                    ->label('Product')
                    // ->options(Product::all()->pluck('name','id')),
                    ->relationship('product', 'name')
                    ->options(function () {
                        return Product::all()->pluck('name', 'id');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

            ProductRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGatePasses::route('/'),
            'create' => Pages\CreateGatePass::route('/create'),
            'edit' => Pages\EditGatePass::route('/{record}/edit'),
        ];
    }
}
