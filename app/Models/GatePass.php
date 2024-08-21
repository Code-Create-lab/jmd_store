<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatePass extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function product()
    {

        return $this->belongsToMany(Product::class, 'gate_pass_products')->withPivot(['box'])->withTimestamps();
    }

    protected static function boot()
    {
        parent::boot();

        // static::creating(function ($model) {
        //     dd($model->product);
        // });

        // This will be called on every model call (when model is instantiated)
        static::retrieved(function ($model) {
            // dd($model);

            $totalRate = 0;
            $totalBox = 0;
            // Ensure products are loaded and not null
            foreach ($model->product as $product) {

                $productAddedDate = $product->date;
                $productGatePassDate = $model->date;
                // Convert the string dates to Carbon instances
                $productAddedDateCarbon = Carbon::parse($productAddedDate);
                $productGatePassDateCarbon = Carbon::parse($productGatePassDate);

                // Get the year and month of both dates
                $productAddedYearMonth = $productAddedDateCarbon->format('Y-m');
                $productGatePassYearMonth = $productGatePassDateCarbon->format('Y-m');

                // Calculate the difference in months between the two dates
                $diffInMonths = $productAddedDateCarbon->diffInMonths($productGatePassDateCarbon);

                // If dates are in different months, adjust the result to ensure any partial month counts as a full month
                if ($productAddedYearMonth !== $productGatePassYearMonth) {
                    $diffInMonths = (int)$productAddedDateCarbon->startOfMonth()->diffInMonths($productGatePassDateCarbon->endOfMonth()) + 1;
                } else {

                    $diffInMonths = 1;
                }
                $totalRate += $product->rate * $product->pivot->box * $diffInMonths;
                $totalBox += $product->pivot->box;

                $productData = Product::find($product->id);
                $productData->remaining_box = $productData->box - $product->pivot->box;
                $productData->save();
                // dd($diffInMonths,$product, $model);
            }

            // $otalRrate = $model->product->sum('rate') * $model->product->sum('rate' pivot_box;

            $model->total_amount = $totalRate;
            $model->box = $totalBox;

            $model->save();
            // dd($totalRate);
            //
            // Store your data or perform actions here
            // For example, storing the timestamp of retrieval
            // $model->last_retrieved_at = now();

            // Or log something
            // \Log::info('Model retrieved:', ['model' => $model]);
        });
    }
}
