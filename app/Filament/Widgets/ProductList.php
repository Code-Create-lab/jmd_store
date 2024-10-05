<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ProductList extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(ProductResource::getEloquentQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Acount Head')
                    ->searchable(),
                Tables\Columns\TextColumn::make('marka')
                    ->label('LOT NO')
                    ->searchable(),
                Tables\Columns\TextColumn::make('box')
                    ->label('Balance(Boxes)'),
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


            // ->actions([
            //     // Tables\Actions\EditAction::make()
            //     //    ->url(fn (Product $request): string => url('admin/ledgers/' . $request->id . "/edit")),,
            //     // Tables\Actions\ViewAction::make(),

            //     Action::make('feature')
            //         ->label('Print Invoice')
            //         ->url(fn (Product $request): string => url('admin/product/' . $request->id . "/view"))
            //         ->visible(fn (Product $request) => $request->total_amount !== null)
            // ]);
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn(Product $requset): string => url('admin/products/' . $requset->id . "/edit"))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->label("View")
                    ->color("danger")
                    ->url(fn(Product $requset): string => url('admin/products/' . $requset->id . "/view"))
                    ->openUrlInNewTab(),
                Action::make('feature')
                    ->label('Print Invoice')
                    ->url(fn(Product $requset): string => route('pdf', ['id' => $requset->id]))
                    ->visible(fn(Product $request) => $request->total_amount !== null)
            ])
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
            ]) ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
