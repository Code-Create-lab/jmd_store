<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;

class ViewProduct extends Page implements HasForms
{
    use InteractsWithRecord;

    protected static string $resource = ProductResource::class;

    protected static string $view = 'filament.resources.product-resource.pages.view-product';

    public $productData;

    public function mount(int | string $record): void
    {

        $this->productData =[];
        $this->record = $this->resolveRecord($record);
        // $this->productData =  $this->productData->merge($this->record);
        // array_merge($this->productData, $this->record->toArray());
        // $this->productData =  $this->record->gatePasses->toArray();
        // array_merge($this->record->toArray(), $this->record->gatePasses->toArray() );
        $this->productData['product'] =  $this->record;
        $this->productData['gate_passes'] =  $this->record->gatePasses;


        // dd($this->productData,$this->record->gatePasses);

        // dd($this->record->gatePasses[2]->pivot->box,$this->record->id);
    }
}
