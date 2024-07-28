<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatePass extends Model
{
    use HasFactory;

    protected $guarded = [] ;


    public function product(){

        return $this->belongsToMany(Product::class,'gate_pass_product')->withPivot(['box'])->withTimestamps();

    }
    
}
