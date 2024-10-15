<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\GatePassHasProduct;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Purchased Slips';
    protected static ?string $pluralModelLabel = 'Purchased Slips';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('marka')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('box')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rate')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date')
                    //     ->native(false)
                    //  ->minDate(now())
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Acount Head')
                    ->searchable(),
                Tables\Columns\TextColumn::make('marka')
                    ->label('LOT NO')
                    ->searchable(),
                Tables\Columns\TextColumn::make('box')
                    ->label('Balance(Boxes)')
                    // ->getStateUsing(function($record){
                    //     // dd($record);
                    //     $gatepassProduct = GatePassHasProduct::where('product_id',$record->id)->sum('box');

                    //     // dd($gatepassProduct);
                    //     $box = $record->box - $gatepassProduct;
                    //     return $box;
                    // })
                    ->searchable(),
                Tables\Columns\TextColumn::make('remaining_box')
                    ->label('Remaining Boxes')
                    // ->getStateUsing(function($record){
                    //     // dd($record);
                    //     $gatepassProduct = GatePassHasProduct::where('product_id',$record->id)->sum('box');

                    //     // dd($gatepassProduct);
                    //     $box = $record->box - $gatepassProduct;
                    //     return $box;
                    // })
                    ->searchable(),
                Tables\Columns\TextColumn::make('rate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('name')

                    // ->options(Location::all()->pluck('name', 'id'))
                    ->form([
                        Select::make("name")
                            ->label('Name')
                            ->options(function () {
                                $alphabet = range('A', 'Z');
                                $options = [];

                                // Create A-Z options for the filter
                                foreach ($alphabet as $letter) {
                                    $options[$letter] = $letter;
                                }

                                return $options;
                            })
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            function (Builder $query, $name) {
                                return $query->where('name', 'like', "$name%");
                            }
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['name']) {
                            return null;
                        }
                        $indicator = 'Name: ' . $data['name'];
                        return $indicator;
                    }),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}/view'),
        ];
    }

    public static function getAlphabetOptions(): array
    {
        $alphabet = range('A', 'Z');
        $options = [];

        // Create A-Z options for the filter
        foreach ($alphabet as $letter) {
            $options[$letter] = $letter;
        }

        return $options;
    }
}
