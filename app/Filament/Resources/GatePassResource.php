<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GatePassResource\Pages;
use App\Filament\Resources\GatePassResource\RelationManagers;
use App\Filament\Resources\GatePassResource\RelationManagers\GatePassRelationManager as RelationManagersGatePassRelationManager;
use App\Filament\Resources\GatePassResource\RelationManagers\ProductRelationManager;
use App\Filament\Resources\PassResource\RelationManagers\GatePassRelationManager;
use App\Models\GatePass;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
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
                    // ->since()
                    ->date()
                    // ->description(fn (GatePass $record): string => $record->date)
                    // ->dateTimeTooltip()
                    ->label('Added Date')
                    // ->weight(FontWeight::Bold)
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('In Slip Date')
                    ->formatStateUsing(function ($record) {
                        // Format each box count as a bullet point
                        return $record->product->map(function ($product) {
                            // dd($product->pivot);
                            if ($product->pivot->in_slip_date) {
                                $in_slip_date = \Carbon\Carbon::parse($product->pivot->in_slip_date)->format('M d, Y');
                            } else {

                                $in_slip_date = "Not Available";
                            }
                            return "<li>{$in_slip_date}</li>"; // Wrap each box in a list item
                        })->join(""); // Join list items without extra separators
                    })
                    ->html() // Enable HTML rendering for the column
                    ->wrap(), // Wraps the text if it's too long
               TextColumn::make('product.remaining_box')
                    ->label('Remaining Boxes')
                    // ->weight(FontWeight::Bold)
                    ->listWithLineBreaks()
                    ->badge()
                    ->wrap(),

                TextColumn::make('box')
                    ->label('Boxes')
                    ->formatStateUsing(function ($record) {
                        // Format each box count as a bullet point
                        return $record->product->map(function ($product) {
                            return "<li>{$product->pivot->box}</li>"; // Wrap each box in a list item
                        })->join(""); // Join list items without extra separators
                    })
                    ->html() // Enable HTML rendering for the column
                    ->wrap(), // Wraps the text if it's too long
                Tables\Columns\TextColumn::make('slip_no')
                    ->searchable()
                    ->weight(FontWeight::Bold),


                // Tables\Columns\TextColumn::make('total_amount'),
                TextColumn::make('total_amount')
                // ->prefix('₹')
                    ->summarize(Sum::make()->label('Total Amount')->money('INR', locale: 'nl'))
                    ->money('INR', locale: 'nl'),
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
                // TextColumn::make('total_amount')
                //     ->summarize(Sum::make()->label('Total Amount')->money('INR', locale: 'nl')),
                // TextColumn::make('box')

                //     ->summarize(Sum::make()->label('Total Box')),

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
                ExportBulkAction::make(),
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
