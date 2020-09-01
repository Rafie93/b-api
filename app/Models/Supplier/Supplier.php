<?php

namespace App\Models\Supplier;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
    protected $table = "supplier";
    protected $fillable = ["name","phone","email","address","location","pic"];
}
