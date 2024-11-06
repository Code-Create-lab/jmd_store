<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\GatePassResource;
use App\Models\GatePass;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Tables\Actions\SummarizedAction;
use App\Models\Product;
use Filament\Tables\Actions\ButtonAction;

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
                    // ->since()
                    ->date()
                    // ->description(fn (GatePass $record): string => $record->date)
                    // ->dateTimeTooltip()
                    ->label('In Warehouse')
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
                TextColumn::make('slip_no')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                TextColumn::make('total_amount'),
                TextColumn::make('date')
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
                // TextColumn::make('box')
                //     ->summarize(Sum::make()->label('Total Box')),

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
                SelectFilter::make('product_id')
                    ->label('Product')
                    // ->options(Product::all()->pluck('name','id')),
                    ->relationship('product', 'name')
                    ->options(function () {
                        return Product::all()->pluck('name', 'id');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn(GatePass $requset): string => url('admin/gate-passes/' . $requset->id . "/edit"))
                    ->openUrlInNewTab(),
            ]);
        // ->headerActions([
        //     Action::make('downloadSampleXSL')
        //         ->label('Download Sample Excel')
        //         ->url(route('download-sample-xsl', 'block'))
        //     // ->icon('heroicon-o-download')
        // ])
        // ->headerActions([
        //     Action::make('performSummaryAction')
        //         ->label('Perform Summary Action') // Customize the button label
        //         ->color('primary') // Customize the button color
        //         ->action(fn() => $this->performSummaryAction()), // Define the action
        // ]);
        // ->view('gatepass'); // Use the custom Blade view
    }
}
