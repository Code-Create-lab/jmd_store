<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatePassHasProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'gate_pass_products';

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }


    public function gatePass()
    {
        return $this->belongsToMany(GatePass::class);
    }

   
}
