<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = "unit";
    protected $fillable = ["code","name"];
}
