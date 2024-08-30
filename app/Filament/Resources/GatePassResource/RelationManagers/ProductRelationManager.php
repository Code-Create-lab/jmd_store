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
use Filament\Tables\Columns\TextColumn;
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
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->default(true)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('box')
                    ->getStateUsing(function ($record, ?Product $product) {

                        // dd($record->product_price , $product->nug , $record->nug );


                        return ($record->pivot->box);
                    }),
                TextColumn::make('price')
                    ->label('Total Price')
                    ->getStateUsing(function ($record, ?Product $product) {

                        // dd($record->product_price , $product->nug , $record->nug );


                        return $record->rate * ($record->pivot->box);
                    }),
                TextColumn::make('date')
                    ->label('Date')
                    ->getStateUsing(function ($record, ?Product $product) {

                        // dd($record->product_price , $product->nug , $record->nug );


                        return $record->date;
                    }),
                TextColumn::make('durations')
                    ->label('Total Durations')
                    ->getStateUsing(function ($record, ?Product $product) {

                        // dd($product->gatePasses[0]->date,$product->date);
                        $productAddedDate =  $product->date;
                        $productGatePassDate =  $product->gatePasses[0]->date;
                        // Convert the string dates to Carbon instances
                        $productAddedDateCarbon = Carbon::parse($productAddedDate);
                        $productGatePassDateCarbon = Carbon::parse($productGatePassDate);

                        // Get the year and month of both dates
                        $productAddedYearMonth = $productAddedDateCarbon->format('Y-m');
                        $productGatePassYearMonth = $productGatePassDateCarbon->format('Y-m');

                        // Calculate the difference in months between the two dates
                        $diffInMonths = $productAddedDateCarbon->diffInMonths($productGatePassDateCarbon);

                        // dd($diffInMonths);
                        // If dates are in different months, adjust the result to ensure any partial month counts as a full month
                        if ($productAddedYearMonth !== $productGatePassYearMonth) {
                            $diffInMonths = (int)$productAddedDateCarbon->startOfMonth()->diffInMonths($productGatePassDateCarbon->endOfMonth()) + 1;
                            // dd($diffInMonths);
                        }else{
                            $diffInMonths =1;

                        }




                        return $diffInMonths > 0 ? $diffInMonths. " Months" : $diffInMonths ." Month";
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),

                        // TextInput::make('box')->required()
                        //     ->afterStateUpdated(function (Set $set, ?Product $record, $state, Get $get) {

                        //         $getProduct = Product::find($get('get'));

                        //         // dd();
                        //     })->live(onBlur: true),
                        // TextInput::make('amount')
                        //     ->afterStateUpdated(function (Set $set, Get $get) {
                        //         $box = $get('box');

                        //         $product = $get('product_id');
                        //         // if ($box) {
                        //         dd($product, $box);
                        //         // }
                        //         $product_data = Product::find($product);
                        //         if ($product_data) {
                        //             $productDate = $product_data->date;
                        //             $currentDate = Carbon::now();
                        //             $difference = $currentDate->diffInMonths($productDate);
                        //             dd($difference);
                        //         }
                        //     }),

                        TextInput::make('box')
                            ->required()

                            // ->afterStateUpdated(function (Set $set, ?Product $record, $state, Get $get) {
                            //     // Get the selected product
                            //     $selectedProduct = Product::find($get('product_id'));

                            //     // Perform any action you need with the selected product
                            //     dd($state, "adasd");
                            // })
                            ->live(onBlur: true),
                        // TextInput::make('amount')
                        //     // ->afterStateUpdated(function (Set $set, Get $get) {
                        //     //     $box = $get('box');
                        //     //     $product = $get('product_id'); // Use the stored product_id

                        //     //     // Debug output for product and box
                        //     //     // dd($product, $box);

                        //     //     $product_data = Product::find($product);
                        //     //     if ($product_data) {
                        //     //         $productDate = $product_data->date;
                        //     //         $currentDate = Carbon::now();
                        //     //         $difference = $currentDate->diffInMonths($productDate);

                        //     //         // Debug output for difference
                        //     //         // dd($difference);
                        //     //     }
                        //     // }),
                        //     ,
                    ]),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
