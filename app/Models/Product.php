<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [] ;


    public function gatePasses(){

        return $this->belongsToMany(GatePass::class,'gate_pass_product')->withTimestamps();

    }

}

