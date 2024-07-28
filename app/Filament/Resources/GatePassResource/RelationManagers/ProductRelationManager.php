<?php

namespace App\Filament\Resources\GatePassResource\RelationManagers;

use App\Models\Product;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductRelationManager extends RelationManager
{
    protected static string $relationship = 'product';
    protected static bool $isLazy = false;


    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('name')
                    ->options(Product::all()->pluck('name', 'id'))->searchable(),
                Forms\Components\TextInput::make('box')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        
                        TextInput::make('box')->required()
                            ->afterStateUpdated(function ($state, Get $get) {
                                // dd($get());
                            }),
                        TextInput::make('amount')
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $box = $get('box');

                                $product = $get('product_id');
                                // if ($box) {
                                dd($product, $box);
                                // }
                                $product_data = Product::find($product);
                                if ($product_data) {
                                    $productDate = $product_data->date;
                                    $currentDate = Carbon::now();
                                    $difference = $currentDate->diffInMonths($productDate);
                                    dd($difference);
                                }
                            }),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
