<?php

namespace App\Filament\Resources\GatePassResource\RelationManagers;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\ButtonAction as ActionsButtonAction;
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
            // ->allowDuplicates()
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
                        // dd($record->gatePass);
                        $productGatePassDate =  $product->gatePass->date;
                        // Convert the string dates to Carbon instances
                        $productAddedDateCarbon = Carbon::parse($productAddedDate);
                        $productGatePassDateCarbon = Carbon::parse($productGatePassDate);

                        // Get the year and month of both dates
                        $productAddedYearMonth = $productAddedDateCarbon->format('Y-m-d');
                        $productGatePassYearMonth = $productGatePassDateCarbon->format('Y-m-d');

                        // Calculate the difference in months between the two dates
                        $diffInMonths = $productAddedDateCarbon->diffInMonths($productGatePassDateCarbon);


                        // If dates are in different months, adjust the result to ensure any partial month counts as a full month
                        if ($productAddedYearMonth !== $productGatePassYearMonth) {
                            $diffInMonths = ceil($productAddedDateCarbon->diffInMonths($productGatePassDateCarbon));
                            // $diffInMonths = (int)$productAddedDateCarbon->startOfMonth()->diffInMonths($productGatePassDateCarbon->endOfMonth()) ;
                            // dd($diffInMonths);
                        } else {
                            $diffInMonths = 1;
                        }




                        return $diffInMonths > 0 ? $diffInMonths . " Months" : $diffInMonths . " Month";
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect()
                            // ->getOptionLabelFromRecordUsing(fn (Product $record) => "{$record->name} {$record->marka}")
                            // ->relationship('products', 'name_marka')  // Use the accessor here
                            ->searchable(['name', 'marka'])
                            ->searchingMessage('Searching Products...'),

                        // Select::make('product_id')
                        //     ->label('Select Product')
                        //     ->options(Product::all()->pluck('name', 'id')->map(function ($name, $id) {
                        //         $product = Product::find($id);
                        //         return "{$product->name} ({$product->marka})";
                        //     }))
                        //     ->searchable(['name', 'marka'])
                        //     ->searchingMessage('Searching Products...'),

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
                ActionsButtonAction::make('createProduct')
                    ->label('Create Product')
                    ->url(fn() => ProductResource::getUrl('create')), // Redirect to the Category resource's create page
                    // ->icon('heroicon-o-plus'),
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
