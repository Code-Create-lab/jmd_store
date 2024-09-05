<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [] ;


    public function gatePasses(){

        return $this->belongsToMany(GatePass::class,'gate_pass_products')->withTimestamps();

    }

    public function gatePass(){

        return $this->belongsTo(GatePass::class,'gate_pass_id');

    }

    protected static function boot()
    {
        parent::boot();

        // Concatenate name and marka during creation
        static::creating(function ($product) {
            $product->name = "{$product->name} - ({$product->marka})";
        });

        // Concatenate name and marka during update
        static::updating(function ($product) {
            // Check if the name already ends with the marka value
            if (!str_ends_with($product->name, "({$product->marka})")) {
                $product->name = "{$product->name} ({$product->marka})";
            }
        });
    }

}

